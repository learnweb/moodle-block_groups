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
        $content = '';
        // Calls the renderer
        /* @var $renderer block_groups_renderer*/
        $renderer = $PAGE->get_renderer('block_groups');
        // Calls Javascript if availeable.
        $PAGE->requires->js_call_amd('block_groups/blocks_groups_visibility', 'initialise', array($COURSE->id));
        // Initializing the grouparray.
        $groupsarray = array();
        foreach ($allgroups as $value) {
            // Checks availability of group and requests the content.
            if (is_object($value) && property_exists($value, 'name')) {
                $countmembers = count(groups_get_members($value->id));
                $href = $CFG->wwwroot . '/blocks/groups/changevisibility.php?courseid=' . $COURSE->id . '&groupid=' . $value->id;
                if (empty($DB->get_records('block_groups_hide', array('id' => $value->id)))) {
                    $groupsarray[] = $renderer->get_string_visiblegroup($value, $href, $countmembers);
                } else {
                    $groupsarray[] = $renderer->get_string_hiddengroup($value, $href, $countmembers);
                }
            }
        }
        // Empty block or block with checkboxes.
        if (count($groupsarray) == 0) {
            $content .= $renderer->get_link_modify_groups();
            $content .= get_string('nogroups', 'block_groups');
        } else {
            $groupingsarray = $this->build_grouping_array($allgroupings);
            if (!empty($groupingsarray)) {
                $content .= $renderer->teaching_groupingslist($groupingsarray);
            }
            $content .= $renderer->teaching_groupslist($groupsarray);
            $content .= $renderer->get_link_modify_groups();
        }
        return $content;
    }

    /**
     * Returns all registered groups.
     *
     * @return string
     */

    private function block_groups_get_content_groupmembers() {
        global $COURSE, $DB, $PAGE;
        $enrolledgroups = array();
        $allgroups = groups_get_my_groups();
        // Necessary to show hidden groups to Course Managers.
        $access = has_capability('moodle/course:managegroups',  context_course::instance($COURSE->id));
        /* @var $renderer block_groups_renderer*/
        $renderer = $PAGE->get_renderer('block_groups');
        foreach ($allgroups as $group) {
            if (($group->courseid == $COURSE->id)) {
                $counter = $DB->get_records('block_groups_hide', array('id' => $group->id));
                if (!empty($counter)) {
                    $enrolledgroups[] = $renderer->get_tag_visiblegroup($group);
                } else if ($access === true) {
                    $enrolledgroups[] = $renderer->get_tag_hiddengroup($group);
                }
            }
        }
        // Returns an empty list of groups.
        if (empty($enrolledgroups)) {
            $content = '';
            return $content;
        }
        $content = $renderer->get_membership_content($enrolledgroups);
        return $content;
    }
    /**
     * The Block is only availeable at course-view pages
     *
     * @return array
     */
    public function applicable_formats() {
        return array('course-view' => true, 'mod' => false, 'my' => false);
    }
    /**
     * Generates an array of groupingnames and their members.
     *
     * @param $allgroupings array of groupings
     * @return array of Groupings
     */
    public function build_grouping_array ($allgroupings) {
        global $DB, $PAGE;
        /* @var $renderer block_groups_renderer*/
        $renderer = $PAGE->get_renderer('block_groups');
        $groupingsarray = array();
        foreach ($allgroupings as $g => $value) {
            if (is_object($value) && property_exists($value, 'name')) {
                // Necessary DB query to prohibit multiple ids of grouping members.
                $countgroupingmem = $DB->count_records_sql("SELECT Count(DISTINCT gm.userid)
                                                            FROM {groupings_groups} gg
                                                            INNER JOIN {groups_members} gm
                                                            ON gg.groupid = gm.groupid
                                                            WHERE gg.groupingid = :groupingid", array('groupingid' => $value->id));
                $groupingsarray[$g] = $renderer->get_grouping($value->name, $countgroupingmem);
            }
        }
        return $groupingsarray;
    }
}