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
declare(strict_types=1);

namespace ilyamoiseev\dbprovider;

use ilyamoiseev\dbprovider\Interfaces\DBException;
use ilyamoiseev\dbprovider\Interfaces\DBMySQLi;
use ilyamoiseev\dbprovider\Interfaces\DBPDO;

/**
 * Class DBInitStatic
 * @author Ilya V. Moiseev
 * @package ilyamoiseev\dbprovider
 * @copyright (c) 2016 - 2019, Ilya V. Moiseev
 */
class DBInitStatic
{
    /**
     * Define constants for SQL drivers:
     */
    const DBDriverMySQLi = 0;
    const DBDriverPDO = 1;

    /**
     * Database interface object.
     * @var object
     */
    private static $db;

    /**
     * @param array $connection
     * @param string $char
     * @param int $driver
     * @throws DBException
     */
    public static function init(array $connection, string $char = "utf8", int $driver = self::DBDriverMySQLi)
    {
        if (!array_key_exists("host", $connection) || !array_key_exists("user", $connection)
            || !array_key_exists("passw", $connection) || !array_key_exists("name", $connection))
            throw new DBException("Check connection info.");
        $host = $connection['host'];
        $user = $connection['user'];
        $passw = $connection['passw'];
        $name = $connection['name'];

        if ($driver == 0) self::$db = new DBMySQLi($host, $user, $passw, $name, $char);
        else self::$db = new DBPDO($host, $user, $passw, $name, $char);
    }

    /**
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws DBException
     * @version 20.05.2017
     */
    public static function query(string $query, array $params = array())
    {
        // Check the connection to database and send a query:
        return self::instance()->query($query, $params);
    }

    /**
     * @param string $query
     * @param array $params
     * @return int
     * @throws DBException
     * @version 20.05.2017
     */
    public static function action(string $query, array $params = array()): int
    {
        // Check the connection to database and send a query:
        return self::instance()->action($query, $params);
    }

    /**
     * The method checks, if the database instance exists.
     * @return object
     * @throws DBException
     * @static
     * @version 20.05.2017
     */
    public static function instance()
    {
        if (!is_object(self::$db)) {
            throw new DBException("Database is not connected.");
        }
        return self::$db;
    }

    /**
     * Method returns the last insert ID.
     * @return int
     * @throws DBException
     * @version 09.04.2018
     */
    public static function insertID(): int
    {
        if (!is_object(self::$db)) {
            throw new DBException("Database is not connected.");
        }
        return self::$db->insertID();
    }

    /**
     * @param string $value
     * @return string
     * @throws DBException
     * @version 20.05.2017
     */
    public static function escaped(string $value): string
    {
        if (!is_object(self::$db)) {
            throw new DBException("Database is not connected.");
        }
        return self::$db->escaped($value);
    }
}