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

use mysqli_result as mysqli_result;

/**
 * The data type of the DB Result.
 * @author Ilya V. Moiseev
 * @package ivmoiseev\dbprovider
 * @copyright (c) 2016 - 2019, Ilya V. Moiseev
 */
class DBMySQLiResult implements DBResultInterface
{
    private $num_rows = 0;
    private $result;

    /**
     * DBMySQLiResult constructor.
     * @param mysqli_result $result
     * @version 19.01.2018
     */
    public function __construct(mysqli_result $result)
    {
        $this->result = $result;
        $this->num_rows = $result->num_rows;
        return;
    }

    public function numRows()
    {
        return $this->num_rows;
    }

    /**
     * @param null $offset
     * @return array|bool
     * @version 19.01.2018
     */
    public function asArray($offset = null)
    {
        if ($this->num_rows < 1) {
            return false;
        }
        while ($row = $this->result->fetch_array()) {
            $output[] = $row;
        }
        if (is_int($offset)) {
            return $output[$offset];
        } else {
            return $output;
        }
    }

    /**
     * @param null $offset
     * @param string $class_name
     * @param array $params
     * @return array|bool
     * @version 19.01.2018
     */
    public function asObject($offset = null, string $class_name = "stdClass", array $params = array())
    {
        if ($this->num_rows < 1) {
            return false;
        }
        // TODO: Refactor this repeated code!
        if (!empty($params)) {
            while ($row = $this->result->fetch_object($class_name, $params)) {
                $output[] = $row;
            }
        } else {
            while ($row = $this->result->fetch_object($class_name)) {
                $output[] = $row;
            }
        }
        if (is_int($offset)) {
            return $output[$offset];
        } else {
            return $output;
        }
    }

    /**
     * @return bool|string
     * @version 19.01.2018
     */
    public function asJson()
    {
        if ($this->num_rows < 1) {
            return false;
        }
        return json_encode($this->result->fetch_object());
    }

    /**
     * @param null|int $id
     * @return bool|mixed
     * @version 19.01.2018
     */
    public function row($id = null)
    {
        if ($this->num_rows < 1) {
            return false;
        }
        if (is_int($id)) {
            // This will return string:
            return $this->result->fetch_row()[$id];
        } else {
            // This will return array():
            return $this->result->fetch_row();
        }
    }

    public function __destruct()
    {
        $this->result->close();
    }
}