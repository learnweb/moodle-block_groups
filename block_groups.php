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
 * @category   block
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * The block_group class
 *
 * Displays a group and grouping block.
 *
 * @package block_groups
 * @category   block
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_groups extends block_base
{
    /**Initialises the block*/
    public function init() {
        $this->title = get_string('pluginname', 'block_groups');
    }

    /**
     * Returns the content object
     *
     * @return object $this->content
     */
    public function get_content() {
        // Record the current course.
        global $COURSE;
        // Records the capability to manage courses.
        $access = has_capability('moodle/course:managegroups', context_course::instance($COURSE->id));

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';

        if ($access === true) {
            $this->content->text .= $this->block_groups_get_content_teaching();
        }

        if ($access === false) {
            $this->title = get_string('pluginname2', 'block_groups');
        }
        $this->content->text .= $this->block_groups_get_content_groupmembers();
        return $this->content;
    }


    /**
     * Returns a List of all existing groups and groupings
     *
     * @return string
     */
    private function block_groups_get_content_teaching() {
        global  $COURSE, $PAGE, $DB, $CFG;
        // Array to save all groups.
        $allgroups = groups_get_all_groups($COURSE->id);
        // Array to save all groupings.
        $allgroupings = groups_get_all_groupings($COURSE->id);
        // String initialises an empty string.
        $groupstext = '';
        // Calls the renderer
        /* @var $renderer block_groups_renderer*/
        $renderer = $PAGE->get_renderer('block_groups');
        // Calls Javascript if availeable.
        $PAGE->requires->js_call_amd('block_groups/blocks_groups_visibility', 'initialise', array($COURSE->id));
        // Groups and Grouping Names are saved in arrays.
        $groupsarray = array();
        foreach ($allgroups as $g => $value) {
            if (is_object($value) && property_exists($value, 'name')) {
                $countmembers = count(groups_get_members($value->id));
                $href = $CFG->wwwroot . '/blocks/groups/changevisibility.php?courseid=' . $COURSE->id . '&groupid=' . $value->id;
                if (empty($DB->get_records('block_groups_hide', array('id' => $value->id)))) {
                    $groupsarray[] = $renderer->get_groupsarrayempty($value, $href, $countmembers);
                } else {
                    $groupsarray[] = $renderer->get_groupsarraynonempty($value, $href, $countmembers);
                }
            }
        }
        // Necessary DB query to prohibit multiple ids of grouping members.
        $groupingsarray = $this->build_grouping_array($allgroupings);
        // Groups and Grouping Names are saved in arrays.
        // Empty block or block with checkboxes.
        if (count($groupsarray) == 0) {
            $groupstext .= $renderer->get_link();
            $groupstext .= get_string('nogroups', 'block_groups');
            return $groupstext;
        } else {
            if (!(empty($groupingsarray))) {
                $groupstext .= $renderer->teaching_groupingslist($groupingsarray);
            }
            $groupstext .= $renderer->teaching_groupslist($groupsarray);
            $groupstext .= $renderer->get_link();
            return $groupstext;
        }
    }

    /**
     * Returns all registered groups.
     *
     * @return string
     */

    private function block_groups_get_content_groupmembers() {
        // Records the current course.
        global $COURSE, $DB, $PAGE;
        // Initialises an array to save the enrolled groups.
        $enrolledgroups = array();
        // List renders all enrolled groups.
        $allgroups = groups_get_my_groups();
        // Records the capability to manage courses.
        $access = has_capability('moodle/course:managegroups',  context_course::instance($COURSE->id));
        /* @var $renderer block_groups_renderer*/
        $renderer = $PAGE->get_renderer('block_groups');
        foreach ($allgroups as $valueall) {
            if (($valueall->courseid == $COURSE->id)) {
                $counter = $DB->get_records('block_groups_hide', array('id' => $valueall->id));
                if (!empty($counter)) {
                    $enrolledgroups[] = $renderer->get_tag_groupname($valueall);
                } else if ($access === true) {
                    $enrolledgroups[] = $renderer->get_tag_hiddengroups($valueall);
                }
            }
        }
        // Returns an empty block.
        if (empty($enrolledgroups)) {
            $groupstext = '';
            return $groupstext;
        }
        $groupstext = $renderer->get_membership_content($enrolledgroups);
        return $groupstext;
    }
    /**
     * The Block is only availeable at course-view pages
     *
     * @return array
     */
    public function applicable_formats() {
        return array('course-view' => true, 'mod' => false, 'tag' => false);
    }
    public function build_grouping_array ($allgroupings) {
        global $DB, $PAGE;
        $renderer = $PAGE->get_renderer('block_groups');
        $groupingdbquery = $DB->get_records_sql("SELECT gm.id, gm.groupid, gm.userid, gg.groupingid
                                                               FROM {groupings_groups} gg
                                                               JOIN {groups_members} gm
                                                               ON gg.groupid = gm.groupid", array());
        foreach ($allgroupings as $g => $value) {
            if (is_object($value) && property_exists($value, 'name')) {
                $members = array();
                foreach ($groupingdbquery as $tempvalue) {
                    if ($tempvalue->groupingid === $value->id) {
                        $members[$tempvalue->userid] = $tempvalue->userid;
                    }
                }
                $tempcounter = count($members);
                $groupingsarray[$g] = $renderer->get_groupingsarray($value, $tempcounter);
            }
        }
        return $groupingsarray;
    }
}