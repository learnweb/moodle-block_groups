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
 * The file contains a test script for the moodle block groups
 *
 * @package block_groups
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blocks_groups_testcase extends advanced_testcase {
    public function test_adding() {
        // Recommended in Moodle docs to always include CFG
        global $CFG;
        $this->setAdminUser();
        $course2 = $this->getDataGenerator()->create_course(array('name'=>'Some course'));
//        shortcut $this->setGuestUser(), $this->setAdminUser()
//        sonst: $user = $this->getDataGenerator()->create_user(array(properties))
//        $course2 = $this->getDataGenerator()->create_course(array('name'=>'Some course', 'category'=>$category->id));
//        $this->getDataGenerator()->enrol_user($userid, $courseid);
//        $this->getDataGenerator()->create_group(array('courseid' => $courseid));
//        $this->getDataGenerator()->create_group_member(array('userid' => $userid, 'groupid' => $groupid));
//        $this->getDataGenerator()->create_grouping_group(array('groupingid' => $groupingid, 'groupid' => $groupid));
//        $this->getDataGenerator()->create_grouping(array('courseid' => $courseid));
//        $mygenerator = $this->getDataGenerator()->get_plugin_generator($frankenstylecomponentname);
    }

    public function test_deleting() {
        global $DB;
        $this->resetAfterTest(true);
        $DB->delete_records('user');
        $this->assertEmpty($DB->get_records('user'));
    }

    public function test_user_table_was_reset() {
        global $DB;
        $this->assertEquals(2, $DB->count_records('user', array()));
    }
}
