<?php
/**
 *  This file is a part of IM-CMS 4 Content Management System.
 *
 *  @since 4.0
 *  @author Ilya Moiseev aka Non Grata <ivmoiseev@inbox.ru>
 *  @copyright Copyright (c) 2010-2018, Ilya Moiseev
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
use PDO;
use PDOStatement;

// Class will be generated dynamically. Don't delete this line.

/**
 * The data type of the DB Result.
 * @author Ilya V. Moiseev
 * @package ivmoiseev\dbprovider
 * @copyright (c) 2016 - 2019, Ilya V. Moiseev
 */
class DBPDOResult implements DBResultInterface
{
    private $num_rows = 0;
    private $result;

    /**
     * DBPDOResult constructor.
     * @param PDOStatement $result
     * @version 19.01.2018
     */
    public function __construct(PDOStatement $result)
    {
        $this->result = $result;
        if (property_exists($result, "rowCount")) {
            $this->num_rows = (int) $result->rowCount;
        }
        return;
    }

    /**
     * @return array|bool
     * @version 19.01.2018
     */
    public function asArray()
    {
        $result = $this->result->fetchAll(PDO::FETCH_ASSOC);
        $this->num_rows = count($result);
        return ($this->num_rows > 0) ? $result : false;
    }

    /**
     * @return bool|string
     * @version 19.01.2018
     */
    public function asJson()
    {
        $result = $this->result->fetchAll(PDO::FETCH_OBJ);
        $this->num_rows = count($result);
        return ($this->num_rows > 0) ? json_encode($result) : false;
    }

    /**
     * @param null|int $offset
     * @param string $class_name
     * @param array $params
     * @return array|bool
     * @version 19.01.2018
     */
    public function asObject($offset = null, string $class_name = "stdClass", array $params = array())
    {
        $result = $this->result->fetchAll(PDO::FETCH_CLASS, $class_name, $params);
        $this->num_rows = count($result);
        return ($this->num_rows > 0) ? (is_int($offset)) ? $result[$offset] : $result : false;
    }

    /**
     * @return int
     * @version 19.01.2018
     */
    public function numRows()
    {
        return $this->num_rows;
    }

    /**
     * @param null $id
     * @return array|string
     * @version 19.01.2018
     */
    public function row($id = null)
    {
        if (is_int($id)) {
            // This will return string:
            return $this->result->fetch(PDO::FETCH_NUM)[$id];
        } else {
            // This will return array:
            return $this->result->fetch(PDO::FETCH_NUM);
        }
    }

    /**
     * @version 19.01.2018
     */
    public function __destruct()
    {
        $this->result->closeCursor();
    }
}