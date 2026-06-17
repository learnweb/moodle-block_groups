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
 * @package    block_groups
 * @copyright  2016/17 N Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Executes a change in the block_groups_hide database
 * @param integer $groupid
 * @param integer $courseid
 */
function block_groups_db_transaction_change_visibility($groupid, $courseid) {
    global $DB;
    $transaction = $DB->start_delegated_transaction();
    $groupsuitable = $DB->get_record('groups', ['id' => $groupid, 'courseid' => $courseid]);
    $groupvisible = $DB->get_records('block_groups_hide', ['id' => $groupid]);
    if (!empty($groupsuitable)) {
        if (empty($groupvisible)) {
            // Methode neccessary since id is the only column.
            $DB->insert_record_raw('block_groups_hide', ['id' => $groupid], true, false, true);
        }
        if (!empty($groupvisible)) {
            $DB->delete_records('block_groups_hide', ['id' => $groupid]);
        }
    }
    $transaction->allow_commit();
}
/**
 * Counts grouping members.
 * @param integer $courseid
 * @return array of database records
 */
function count_grouping_members($courseid) {
    global $DB, $PAGE;
    return  $DB->get_records_sql('SELECT g.id, Count(DISTINCT gm.userid) AS number
                                                 FROM {groups_members} gm
                                                 RIGHT JOIN {groupings_groups} gg
                                                 ON gg.groupid = gm.groupid
                                                 RIGHT JOIN {groupings} g
                                                 ON g.id = gg.groupingid
                                                 WHERE g.courseid = ' . $courseid . '
                                                 GROUP BY g.id, gg.groupingid
                                                 ORDER BY gg.groupingid DESC');
}

/**
 * Sets the visibility of a group.
 * @param int $groupid
 * @param int $courseid
 * @param bool $visible if target visibility is visible or not
 * @return bool true if something changed, false otherwise
 */
function block_groups_set_visibility($groupid, $courseid, $visible) {
    global $DB;

    $transaction = $DB->start_delegated_transaction();
    $groupsuitable = $DB->get_record('groups', ['id' => $groupid, 'courseid' => $courseid]);

    if (empty($groupsuitable)) {
        $transaction->allow_commit();
        return false;
    }

    $currentlyvisible = $DB->record_exists('block_groups_hide', ['id' => $groupid]);

    // The group is currently not visible and should be visible now.
    if ($visible && !$currentlyvisible) {
        $DB->insert_record_raw('block_groups_hide', ['id' => $groupid], true, false, true);
        $transaction->allow_commit();
        return true;
    }

    // The group is currently visible and should be not visible now.
    if (!$visible && $currentlyvisible) {
        $DB->delete_records('block_groups_hide', ['id' => $groupid]);
        $transaction->allow_commit();
        return true;
    }

    $transaction->allow_commit();
    return false;
}
