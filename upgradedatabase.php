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
 * The file contains a class to build a Group Block
 *
 * @package block_groups
 * @category   upgrade
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
        require_once('../../config.php');
global $DB, $CFG;
$id               = optional_param('id', 0, PARAM_INT);
$groupid          = optional_param('groupid', 0, PARAM_INT);
echo $id;
if (!empty($id)) {
    if (!empty($groupid)) {
        $counter = $DB->get_records('block_groups_hide', array('groupid' => $groupid));
        $insertionarray = array('groupid' => $groupid);
        if (empty($counter)) {
            $DB->insert_record('block_groups_hide', array('groupid' => $groupid, 'visibility' => 1));
            redirect($CFG->wwwroot . '/course/view.php?id=' . $id);
//        get_local_referer
            exit();
        }
        if (!empty($counter)) {
            $DB->delete_records('block_groups_hide', $insertionarray);
            redirect($CFG->wwwroot . '/course/view.php?id=' . $id);
        }
    }
}
