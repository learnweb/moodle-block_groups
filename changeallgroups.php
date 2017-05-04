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
 * File which changes the visibility of groups.
 *
 * @package block_groups
 * @category   upgrade
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_login();

$courseid         = required_param('courseid', PARAM_INT);
$hide             = required_param('hide', PARAM_INT);

$PAGE->set_url('/blocks/groups/changeallgroups.php');
// In Case the given id of the course is not available in the database exit message is shown.
if (empty($DB->get_record('course', array('id' => $courseid)))) {
    exit(get_string('nocourse', 'block_groups'));
}
$PAGE->set_context(context_course::instance($courseid));
// Check for capability beforehand not possible since a context is needed.
require_capability('moodle/course:managegroups', context_course::instance($courseid));
// Get all groups of the selected course.
$groupsuitable = $DB->get_records('groups', array('courseid' => $courseid));
// The Course has no groups therefore changing all is not possible.
if (empty($groupsuitable)) {
    notice(get_string('nochangeindatabasepossible', 'block_groups'),
        $CFG->wwwroot . '/course/view.php?id=' . $courseid);
    exit();
}
$groups = array();
$groupsvisible = array();
foreach ($groupsuitable as $group) {
    $entry = $DB->get_records('block_groups_hide', array('id' => $group->id));
    // In the Case, that the group of the course has an entry in the 'block_groups_hide' table the group is visible.
    if (!empty($entry)) {
        $groupsvisible[$group->id] = $group->id;
    }
}
$groups = $groupsvisible;

if ($hide === 0) {
    $tempgroup = array();
    foreach ($groupsuitable as $group) {
        if (!empty($groupsvisible)) {
            if (!(in_array($group->id, $groups))) {
                $tempgroup[$group->id] = $group->id;
            }
        } else {
            $tempgroup[$group->id] = $group->id;
        }
    }
    $groups = $tempgroup;
}
if (empty($groups)) {
    // TODO: Fehler abfangen wenn alle gruppen sichtbar sind.
    notice(get_string('nochangeindatabasepossible', 'block_groups'),
        $CFG->wwwroot . '/course/view.php?id=' . $courseid);
    exit();
}
require_once($CFG->dirroot.'/blocks/groups/locallib.php');
foreach ($groups as $group) {
    block_groups_db_transaction_change_visibility($group, $courseid);
}
redirect($CFG->wwwroot . '/course/view.php?id=' . $courseid);
exit();