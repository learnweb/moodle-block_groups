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
//require_once($CFG->dirroot.'/blocks/groups/locallib.php');

class blocks_groups_testcase extends advanced_testcase {

    public function test_adding() {
        // Recommended in Moodle docs to always include CFG.
        global $CFG;
        $this->test_deleting();
    }
    /**
     * Function to test the locallib functions.
     * @package block_groups
     */
    public function test_locallib() {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/blocks/groups/locallib.php');
        $this->test_deleting();
        $generator = advanced_testcase::getDataGenerator();
        // TODO Einbindung per lib.
        $course2 = $this->getDataGenerator()->create_course(array('name' => 'Some course'));
        $data['course2'] = $course2;
        // Creates groups.
        $group1 = $generator->create_group(array('courseid' => $course2->id));
        $data['group1'] = $group1;
        $group2 = $generator->create_group(array('courseid' => $course2->id));
        $data['group2'] = $group2;
        $group21 = $generator->create_group(array('courseid' => $course2->id));
        $data['group21'] = $group21;
        // Create 3 groupings in course 2.
        $grouping1 = $generator->create_grouping(array('courseid' => $course2->id));
        $data['grouping1'] = $grouping1;
        $grouping2 = $generator->create_grouping(array('courseid' => $course2->id));
        $data['grouping2'] = $grouping2;
        $grouping3 = $generator->create_grouping(array('courseid' => $course2->id));
        $data['grouping3'] = $grouping3;
        // Add Groupings to groups.
        $generator->create_grouping_group(array('groupingid' => $grouping1->id, 'groupid' => $group1->id));
        $generator->create_grouping_group(array('groupingid' => $grouping2->id, 'groupid' => $group2->id));
        $generator->create_grouping_group(array('groupingid' => $grouping2->id, 'groupid' => $group21->id));

        // Creates 9 Users, enroles them in course2.
        for ($i = 1; $i <= 9; $i++) {
            $user = $generator->create_user();
            $generator->enrol_user($user->id, $course2->id);
            $data['user' . $i] = $user;
        }
        // Initiates the groupings and grouping members.
        $generator->create_group_member(array('groupid' => $group1->id, 'userid' => $data['user1']->id));
        $generator->create_group_member(array('groupid' => $group1->id, 'userid' => $data['user2']->id));
        $generator->create_group_member(array('groupid' => $group2->id, 'userid' => $data['user3']->id));
        $generator->create_group_member(array('groupid' => $group21->id, 'userid' => $data['user4']->id));
        $generator->create_group_member(array('groupid' => $group21->id, 'userid' => $data['user3']->id));
        $generator->create_group_member(array('groupid' => $group2->id, 'userid' => $data['user4']->id));
        $generator->create_group_member(array('groupid' => $group21->id, 'userid' => $data['user2']->id));

        // Test the function that changes the database.
        block_groups_db_transaction_change_visibility($data['group1']->id, $data['course2']->id);
        block_groups_db_transaction_change_visibility($data['group2']->id, $data['course2']->id);
        block_groups_db_transaction_change_visibility($data['group2']->id, $data['course2']->id);
        $functionresultshow = $DB->get_records('block_groups_hide', array('id' => $data['group1']->id));
        $functionresulthide = $DB->get_records('block_groups_hide', array('id' => $data['group2']->id));
        $booleanvisible = empty($functionresultshow);
        $booleandeleted = empty($functionresulthide);

        $this->assertEquals(false, $booleanvisible);
        $this->assertEquals(true, $booleandeleted);

        // Test the function that counts the grouping members.
        $functioncount = count_grouping_members ($data['grouping1']->id);
        $functioncount2 = count_grouping_members($data['grouping2']->id);
        $functioncount3 = count_grouping_members($data['grouping3']->id);

        $this->assertEquals(2, $functioncount);
        // Members are not counted multiple.
        $this->assertEquals(3, $functioncount2);
        // Test empty grouping.
        $this->assertEquals(0, $functioncount3);
    }
    /**
     * Methodes recommended by moodle to assure database and dataroot is reset.
     * @package block_groups
     */
    public function test_deleting() {
        global $DB;
        $this->resetAfterTest(true);
        $DB->delete_records('user');
        $DB->delete_records('block_groups_hide');
        $this->assertEmpty($DB->get_records('user'));
        $this->assertEmpty($DB->get_records('block_groups_hide'));
    }
    public function test_user_table_was_reset() {
        global $DB;
        $this->assertEquals(2, $DB->count_records('user', array()));
        $this->assertEquals(0, $DB->count_records('block_groups_hide', array()));
    }
}