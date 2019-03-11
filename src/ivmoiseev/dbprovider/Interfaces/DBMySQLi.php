<?php
/**
 *  This file is a part of IM-CMS 4 Content Management System.
 *
 * @since 4.0
 * @author Ilya Moiseev aka Non Grata <ivmoiseev@inbox.ru>
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

namespace ivmoiseev\dbprovider\Interfaces;

use mysqli as DB;
use mysqli_stmt as mysqli_stmt;

/**
 * Class DBMySQLi
 * Warn: Prepared statements will work only with mysqlnd driver!
 * @author Ilya V. Moiseev
 * @package ivmoiseev\dbprovider
 * @copyright (c) 2016 - 2019, Ilya V. Moiseev
 */
class DBMySQLi extends DBGlobal implements DBInterface
{
    protected $last_insert_id = 0;

    /**
     * DBMySQLi constructor.
     * @param string $host
     * @param string $user
     * @param string $passw
     * @param string $name
     * @param string $char
     * @throws DBException
     * @version 08.01.2018 - 11.03.2019
     */
    public function __construct(string $host, string $user, string $passw, string $name, string $char = "utf8")
    {
        // Start the timer.
        $this->timerStart();
        // Query to database.
        $this->db = new DB($host, $user, $passw, $name);
        // Stop the timer.
        $this->timerStop();
        // If there is an error - throw an exception.
        if ($this->db->connect_error) {
            throw new DBException($this->db->connect_error);
        }
        // Increase the database query count.
        $this->query_count++;
        // Set default database charset.
        $this->setCharset($char);
    }

    /**
     * Set default database charset.
     * @param string $char
     * @return boolean
     * @version 02.03.2017 - 09.01.2018
     */
    private function setCharset(string $char): bool
    {
        // Start the timer to calculate the execution time of the query.
        $this->timerStart();
        // Query to database.
        $this->db->set_charset($char);
        // Stop the timer.
        $this->timerStop();
        // Increase the database query count.
        $this->query_count++;
        return true;
    }

    /**
     * The method is applied to queries that do not return a result.
     * @param string $query
     * @param array $params
     * @return int
     * @throws DBException
     * @version 19.01.2018
     */
    public function action(string $query, array $params = array()): int
    {
        return $this->query($query, $params);
    }

    /**
     * The method applies to queries that return a result.
     * Warn: Prepared statements will work only with mysqlnd driver!
     * @param string $query
     * @param array $params
     * @return DBMySQLiResult|int
     * @throws DBException
     * @version 02.03.2017 - 19.01.2018
     */
    public function query(string $query, array $params = array())
    {
        // Start the timer to calculate the execution time of the query.
        $this->timerStart();
        // If input parameters for the prepared statement exist, prepare the statement:
        if (!empty($params)) {
            // Prepare the statement.
            $prepared = $this->prepareStatement($query, $params);
            // Execute the statement.
            $prepared->execute();
            // WARN: PREPARED STATEMENTS WILL WORK ONLY WITH MYSQLND DRIVER!!!
            $result = $prepared->get_result();
        } else {
            // Simple query to database.
            $result = $this->db->query($query);
        }
        // Save the last insert ID:
        $this->last_insert_id = (int)$this->db->insert_id;
        // If there is an error - throw an exception.
        if ($this->db->errno > 0) {
            throw new DBException($this->db->error);
        }
        // Stop the timer.
        $this->timerStop();
        // Increase the database query count.
        $this->query_count++;
        return (is_bool($result)) ? $this->db->affected_rows : new DBMySQLiResult($result);
    }

    /**
     * This method used for prepare statements in MySQLi.
     * @param string $query
     * @param array $params
     * @return mysqli_stmt
     * @throws DBException
     * @version 19.01.2018 - 10.05.2018
     */
    private function prepareStatement(string $query, array $params): mysqli_stmt
    {
        // Count the number of parameters in $params array:
        $params_type = str_repeat("s", count($params));
        // Construct variable, containing the string with bind type parameters.
        // Add the reference to array. It will be used in call_user_func_array() later.
        $bind_params[] = &$params_type;
        // Add references to parameters from input to array. It will be used in call_user_func_array() later.
        for ($n = 0; $n < count($params); $n++) {
            $bind_params[$n + 1] = &$params[$n];
        }
        // Prepare the statement.
        $prepared = $this->db->prepare($query);
        // Catch an error, if exist:
        if ($this->db->errno > 0) throw new DBException($this->db->error);
        // Increase the database query count.
        $this->query_count++;
        // Call $prepared->bind_param() with parameters from the array.
        call_user_func_array(array($prepared, "bind_param"), $bind_params);
        // Return mysqli_stmt in $prepared:
        return $prepared;
    }

    /**
     * Return MySQLi escaped string.
     * @param string $value
     * @return string
     * @version 03.04.2018
     */
    public function escaped(string $value): string
    {
        return $this->db->real_escape_string($value);
    }

    /**
     * @return DB
     * @version 08.01.2018
     */
    public function instance(): DB
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

    /**
     * Method begins the transaction.
     * @return bool
     * @version 11.03.2019
     */
    public function transactionBegin(): bool
    {
        return $this->db->begin_transaction();
    }

    /**
     * Commit current transaction.
     * @return bool
     * @version 11.03.2019
     */
    public function transactionCommit(): bool
    {
        return $this->db->commit();
    }

    /**
     * Rollback current transaction.
     * @return bool
     * @version 11.03.2019
     */
    public function transactionRollback(): bool
    {
        return $this->db->rollback();
    }
}
