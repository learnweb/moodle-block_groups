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
 * The class contains a test script for the moodle block groups
 *
 * @package block_groups
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot.'/blocks/groups/locallib.php');

class blocks_groups_testcase extends advanced_testcase {

    public function test_adding() {

        // Recommended in Moodle docs to always include CFG.
        global $CFG;

        $this->test_deleting();
        // Example data to try first test.
        $generator = advanced_testcase::getDataGenerator();
        $course = $this->getDataGenerator()->create_course(array('name' => 'Some course'));
        // Creates groups.
        $group1 = $generator->create_group(array('courseid' => $course->id));
        $group2 = $generator->create_group(array('courseid' => $course->id));
        $group3 = $generator->create_group(array('courseid' => $course->id));
        // Create 3 groupings in course 1.
        $grouping1 = $generator->create_grouping(array('courseid' => $course->id));
        $grouping2 = $generator->create_grouping(array('courseid' => $course->id));
        $grouping3 = $generator->create_grouping(array('courseid' => $course->id));
        // Add Grouping to groups.
        $generator->create_grouping_group(array('groupingid' => $grouping1->id, 'groupid' => $group1->id));
        $generator->create_grouping_group(array('groupingid' => $grouping2->id, 'groupid' => $group2->id));
    }
    /**
     * Function to test the locallib functions.
     * @package block_groups
     */
    public function test_locallib() {
        global $DB;

        $this->test_deleting();

        $generator = advanced_testcase::getDataGenerator();
        $course2 = $this->getDataGenerator()->create_course(array('name' => 'Some course'));
        // Creates groups.
        $group4 = $generator->create_group(array('courseid' => $course2->id));

        // Executes Function.
        block_groups_db_transaction_change_visibility($group4->id, $course2->id);
        $functionresult = $groupvisible = $DB->get_records('block_groups_hide', array('id' => $group4->id));
        $functioncount = empty($functionresult);
        $course1ctx = context_course::instance($course2->id);
        $this->assertEquals(false, $functioncount);
        block_groups_db_transaction_change_visibility($group4->id, $course2->id);
        $functionresult = $groupvisible = $DB->get_records('block_groups_hide', array('id' => $group4->id));
        $functioncount = empty($functionresult);
        $this->assertEquals(true, $functioncount);
    }
    /**
     * Methodes recommended by moodle to assure database and dataroot is reset.
     * @package block_groups
     */
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
