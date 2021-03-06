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
declare(strict_types=1);

namespace ivmoiseev\dbprovider\Interfaces;

/**
 * Interface DBInterface used for communication with databases.
 * @author Ilya V. Moiseev
 * @package ivmoiseev\dbprovider
 * @copyright (c) 2016 - 2019, Ilya V. Moiseev
 */
interface DBInterface
{
    public function __construct(
        string $host,
        string $user,
        string $passw,
        string $name,
        string $char = "utf8");

    public function instance();

    public function query(string $query, array $params = array());

    public function action(string $query, array $params = array()): int;

    public function insertID(): int;

    public function escaped(string $value): string;

    public function transactionBegin(): bool;

    public function transactionRollback(): bool;

    public function transactionCommit(): bool;
}