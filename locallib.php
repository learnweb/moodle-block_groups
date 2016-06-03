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
 * blocks_groups internal functions
 *
 * @package    blocks_groups
 * @copyright  2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function block_groups_db_transaction_changegroups($groupid, $courseid) {
    global $DB;
    $transaction = $DB->start_delegated_transaction();
    $groupsuitable = $DB->get_record('groups', array('id' => $groupid, 'courseid' => $courseid));
    $groupvisible = $DB->get_records('block_groups_hide', array('id' => $groupid));
    if (!empty($groupsuitable)) {
        if (empty($groupvisible)) {
            $DB->import_record('block_groups_hide', array('id' => $groupid));
        }
        if (!empty($groupvisible)) {
            $DB->delete_records('block_groups_hide', array('id' => $groupid));
        }
    }
    $transaction->allow_commit();
}

