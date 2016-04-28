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
    'block_groups_create_output' => array(
        'classname' => 'block_groups_visibility_change',
        'methodname' => 'create_output', // Implement this function into the above class.
        'classpath'   => 'blocks/groups/externallib.php',
        'description' => 'Service to provide group with information',
        'type' => 'write', // The value is 'write' if your function does any database change, otherwise it is 'read'.
        'capabilities' => 'moodle/course:managegroups',
        'ajax' => true,
    )
);/*
$services = array(
    'create_output' => array(
        'functions' => array ('block_groups_create_output'), // Web service functions of this service.
        'requiredcapability' => 'moodle/course:managegroups',        // If set, the web service user need this capability to access.
        // Any function of this service. For example: 'some/capability:specified'.
        'restrictedusers' => 0,    // If enabled, the Moodle administrator must link some user to this service.
        // into the administration.
        'enabled' => 1,                     // If enabled, the service can be reachable on a default installation.
    )
);*/