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
require_login();

$courseid         = required_param('courseid', PARAM_INT);
$groupid          = required_param('groupid', PARAM_INT);

$PAGE->set_url('/blocks/groups/changevisibility.php');
// In Case the given id of the course is not available in the database exit message is shown.
if (empty($DB->get_record('course', array('id' => $courseid)))) {
    exit(get_string('nocourse', 'block_groups'));
}
$PAGE->set_context(context_course::instance($courseid));
// Capabilitycheck beforehand not possible since a context is needed.
require_capability('moodle/course:managegroups', context_course::instance($courseid));
$groupsuitable = $DB->get_record('groups', array('id' => $groupid, 'courseid' => $courseid));
if (empty($groupsuitable)) {
    notice(get_string('nochangeindatabasepossible', 'block_groups'),
        $CFG->wwwroot . '/course/view.php?id=' . $courseid);
    exit();
}
require_once($CFG->dirroot.'/blocks/groups/locallib.php');
block_groups_db_transaction_changegroups($groupid, $courseid);
redirect($CFG->wwwroot . '/course/view.php?id=' . $courseid);
exit();
