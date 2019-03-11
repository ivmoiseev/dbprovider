<?php
/**
 *  This file is a part of IM-CMS 4 Content Management System.
 *
 * @since 4.0
 * @author Ilya Moiseev aka Non Grata <ilyamoiseev@inbox.ru>
 * @copyright Copyright (c) 2010-2018, Ilya Moiseev
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace ilyamoiseev\dbprovider\Interfaces;

use PDO;
use PDOException;
use PDOStatement as PDOStatement;

/**
 * Class DBPDO
 * @author Ilya V. Moiseev
 * @package ilyamoiseev\dbprovider
 * @copyright (c) 2016 - 2019, Ilya V. Moiseev
 */
class DBPDO extends DBGlobal implements DBInterface
{
    protected $last_insert_id = 0;

    /**
     * DBPDO constructor.
     * @param string $host
     * @param string $user
     * @param string $passw
     * @param string $name
     * @param string $char
     * @throws DBException
     * @version 19.01.2018 - 11.03.2019
     */
    public function __construct(string $host, string $user, string $passw, string $name, string $char = "utf8")
    {
        // Start the timer.
        $this->timerStart();
        try {
            // Query to database.
            $this->db = new PDO("mysql:host=$host;dbname=$name;charset=$char", $user, $passw);
        } catch (PDOException $e) {
            // If there is an error - throw an exception.
            throw new DBException($e->getMessage());
        }
        // Stop the timer.
        $this->timerStop();
        // Increase the database query count.
        $this->query_count++;
    }

    /**
     * The method applies to queries that return a result.
     * @param string $query
     * @param array $params
     * @return DBPDOResult
     * @throws DBException
     * @version 02.03.2017 - 19.01.2018
     */
    public function query(string $query, array $params = array())
    {
        // Start the timer to calculate the execution time of the query.
        $this->timerStart();
        // If input parameters for the prepared statement exist, prepare the statement:
        if (!empty($params)) {
            // Prepared statement:
            $result = $this->prepareStatement($query, $params);
        } else // else do simple query to database:
        {
            $result = $this->db->query($query);
        }
        // If there is an error - throw an exception:
        if (!$result) {
            throw new DBException($this->db->errorInfo()[2]);
        }
        // Stop the timer.
        $this->timerStop();
        // Increase the database query count.
        $this->query_count++;
        return new DBPDOResult($result);
    }

    /**
     * This method used for prepare statements in PDO.
     * @param string $query
     * @param array $params
     * @return PDOStatement
     * @version 19.01.2018
     */
    private function prepareStatement(string $query, array $params): PDOStatement
    {
        // Prepare the statement:
        $result = $this->db->prepare($query);
        // Increase the database query count.
        $this->query_count++;
        // Execute the statement:
        $result->execute($params);
        if ($result->errorCode() != "0000") {
            throw new PDOException($result->errorInfo()[2]);
        }
        return $result;
    }

    /**
     * The method is applied to queries that do not return a result.
     * @param string $query
     * @param array $params
     * @return int
     * @version 19.01.2018
     */
    public function action(string $query, array $params = array()): int
    {
        // Start the timer to calculate the execution time of the query.
        $this->timerStart();
        // If input parameters for the prepared statement exist, prepare the statement:
        if (!empty($params)) {
            // Prepared statement:
            $result = $this->prepareStatement($query, $params)->rowCount();
        } else // else do simple query to database:
        {
            $result = $this->db->exec($query);
        }
        // Save the last insert ID:
        $this->last_insert_id = (int)$this->db->lastInsertId();
        // Stop the timer.
        $this->timerStop();
        // Increase the database query count.
        $this->query_count++;
        return (int)$result;
    }

    /**
     * Return PDO escaped string.
     * @param string $value
     * @return string
     * @version 03.04.2018
     */
    public function escaped(string $value): string
    {
        return $this->db->quote($value);
    }

    /**
     * @return PDO
     * @version 19.01.2018
     */
    public function instance()
    {
        return $this->db;
    }

    /**
     * Method returns the last insert ID.
     * @return int
     * @version 09.04.2018
     */
    public function insertID(): int
    {
        return $this->last_insert_id;
    }
}