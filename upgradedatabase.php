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
    // Message hinzufügen überprüfen ob gruppe zu kurs gehört.
require_once('../../config.php');
require_login();

$courseid         = required_param('courseid', PARAM_INT);
$groupid          = required_param('groupid', PARAM_INT);

$PAGE->set_url('/blocks/groups/upgradedatabase.php');
echo $courseid;
require_capability('moodle/course:managegroups', context_course::instance($courseid));

if (!empty($courseid)) {
    if (!empty($groupid)) {
        $counter = $DB->get_records('block_groups_hide', array('id' => $groupid));
        if (empty($counter)) {
            $DB->import_record('block_groups_hide', array('id' => $groupid));
            redirect($CFG->wwwroot . '/course/view.php?id=' . $courseid);
            exit();
        }
        if (!empty($counter)) {
            $DB->delete_records('block_groups_hide', array('id' => $groupid));
            redirect($CFG->wwwroot . '/course/view.php?id=' . $courseid);
            exit();
        } else {
            $message = get_string('nochangeindatabase', 'block_groups');
            notice($message, $CFG->wwwroot . '/my');
            exit();
        }
    }
    notice(get_string('novalidgroup', 'block_groups'), $CFG->wwwroot . '/my');
}
notice(get_string('notenrolled', 'block_groups'));
exit();
