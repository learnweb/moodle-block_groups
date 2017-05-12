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
 * @copyright 2016/17 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$functions = array(
    'block_groups_create_output' => array(
        'classname' => 'block_groups_visibility_change',
        'methodname' => 'create_output',
        'classpath'   => 'blocks/groups/externallib.php',
        'description' => 'Service to allow changing the visibility of groups',
        'type' => 'write',
        'capabilities' => 'moodle/course:managegroups',
        'ajax' => true,
    )
);