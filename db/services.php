<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Services for the groups block.
 *
 * @package block_groups
 * @category   services
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$functions = array(
    'services_for_groups' => array(
        'classname' => 'services_for_groups',
        'methodname' => 'call_javascript', // Implement this function into the above class.
        'classpath'   => 'local/group/externallib.php',
        'description' => 'Service to provide group with information
                                          (Administration > Plugins > Webservices > API documentation)',
        'type' => 'write', // The value is 'write' if your function does any database change, otherwise it is 'read'.
        'capabilities' => 'moodle/course:managegroups',
    )
);