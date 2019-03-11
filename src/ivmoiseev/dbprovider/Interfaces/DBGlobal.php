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

/**
 * Class DBGlobal
 * @author Ilya V. Moiseev
 * @package ivmoiseev\dbprovider
 * @copyright (c) 2016 - 2019, Ilya V. Moiseev
 */
abstract class DBGlobal
{
    /**
     * @var object
     */
    protected $db;

    /**
     * Total number of queries.
     * @var int
     */
    protected $query_count = 0;

    /**
     * Microtime of current query.
     * @var float
     */
    private $inner_timer = 0.0;

    /**
     * Total microtime of all queries.
     * @var float
     */
    private $query_time = 0.0;

    public function getTimer(bool $get_as_float = false)
    {
        if ($get_as_float) return $this->query_time;
        return round($this->query_time, 2);
    }

    /**
     * Start the timer to calculate the execution time of the query.
     * @return boolean
     * @static
     * @version 02.03.2017 - 19.05.2017
     */
    protected function timerStart()
    {
        $this->inner_timer = microtime(true);
        return true;
    }

    /**
     * Stop the timer and return the query time.
     * @return boolean
     * @static
     * @version 02.03.2017 - 19.05.2017
     */
    protected function timerStop()
    {
        $this->query_time = $this->query_time
            + (microtime(true) - $this->inner_timer);
        return true;
    }

    /**
     * The method returns the number of queries to the database.
     * @return integer
     * @static
     * @version 02.03.2017 - 19.05.2017
     */
    protected function queryCount()
    {
        return $this->query_count;
    }
}