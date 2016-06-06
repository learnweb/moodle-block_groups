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
 * Upgrade for the groups block.
 *
 * @package block_groups
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function xmldb_block_groups_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2016040704) {
        $table = new xmldb_table('block_groups');
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        $dbman->rename_table($table, 'block_groups_hide');
        $fieldgroupid = new xmldb_field('groupid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false);
        $dbman->rename_field($table, $fieldgroupid, 'userid');
        $fieldvisibility = new xmldb_field('visibility', XMLDB_TYPE_CHAR, null, XMLDB_NOTNULL, false);
        $dbman->change_field_type($table, $fieldvisibility, XMLDB_TYPE_INTEGER);
        upgrade_mod_savepoint(true, 201640704, 'block_groups_hide');
    }
    if ($oldversion < 2016041301) {
        $table = new xmldb_table('block_groups_hide');
        $fieldid = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, true);
        $fieldgroupid = new xmldb_field('groupid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false);
        $fieldvisibility = new xmldb_field('visibility', XMLDB_TYPE_INTEGER, '1', null, null, false);
        $dbman->drop_field($table, $fieldid);
        $dbman->drop_field($table, $fieldvisibility);
        if ($dbman->field_exists($table, $fieldgroupid)) {
            $dbman->add_field($table, $fieldgroupid);
        }
        $oldkey = new xmldb_key('primary', 'primary', array('id'));
        $newkey = new xmldb_key('groupid', 'foreign', array('groupid'));
        if (!$dbman->key_exists($table, $oldkey)) {
            $dbman->drop_key($table, $oldkey);
        }
        if (!$dbman->key_exists($table, $newkey)) {
            $dbman->drop_key($table, $newkey);
        }
        upgrade_mod_savepoint(true, 201641301, 'block_groups_hide');
    }
    if ($oldversion < 2016041304) {
        $table = new xmldb_table('block_groups_hide');
        $firstnewkey = new xmldb_key('primary', 'primary', array('id'));
        $secondnewkey = new xmldb_key('foreign', 'foreign', array('groupid'), 'groups', 'id');
        $keytoremove = new xmldb_key('groupid', 'primary', array('groupid'));
        if ($dbman->key_exists($table, $keytoremove)) {
            $dbman->drop_key($table, $keytoremove);
        }
        if (!$dbman->key_exists($table, $firstnewkey)) {
            $dbman->add_key($table, $firstnewkey);
        }
        if (!$dbman->key_exists($table, $secondnewkey)) {
            $dbman->add_key($table, $secondnewkey);
        }
        upgrade_mod_savepoint(true, 2016041304, 'block_groups_hide');

    }
    if ($oldversion < 2016042800) {
        $table = new xmldb_table('block_groups_hide');
        $fieldtoremove = new xmldb_field('groupid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false);
        if ($dbman->field_exists($table, $fieldtoremove)) {
            $dbman->drop_field($table, $fieldtoremove);
        }
        $keytoremove = new xmldb_field('foreign', 'foreign', array('groupid'), 'groups', 'id');
        if ($dbman->key_exists($table, $keytoremove)) {
            $dbman->drop_key($table, $keytoremove);
        }
        $keytoadd = new xmldb_field('foreign', 'foreign', array('id'), 'groups', 'id');
        if (!$dbman->key_exists($table, $keytoadd)) {
            $dbman->add_key($table, $keytoadd);
        }
        upgrade_mod_savepoint(true, 2016042800, 'block_groups_hide');

    }
    return true;
}