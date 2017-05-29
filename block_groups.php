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
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/groups/locallib.php');

/**
 * The block_group class
 *
 * Displays a group and grouping block.
 *
 * @package block_groups
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_groups extends block_base
{
    /**
     * Initializes the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_groups');
    }
    /**
     * Returns the content object.
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
            $this->content->text .= $this->get_content_teaching();
        }

        if ($access === false) {
            $this->title = get_string('pluginname2', 'block_groups');
        }
        $this->content->text .= $this->get_content_groupmembers();
        return $this->content;
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
     * Returns a List of all existing groups and groupings
     *
     * @return string
     */
    private function get_content_teaching() {
        global  $COURSE, $PAGE, $DB, $CFG;
        $courseid = $COURSE->id;
        // Array to save all groups.
        $allgroups = groups_get_all_groups($courseid);
        // Array to save all groupings.
        $allgroupings = groups_get_all_groupings($courseid);
        $content = '';
        /* @var $renderer block_groups_renderer*/
        $renderer = $PAGE->get_renderer('block_groups');
        // Calls Javascript if available.
        $PAGE->requires->js_call_amd('block_groups/blocks_groups_visibility', 'initialise', array($courseid));
        $PAGE->requires->strings_for_js(array('errortitle', 'nochangeindatabasepossible', 'errorbutton',
            'allgroupsinstate', 'groupschanged', 'nogroups'), 'block_groups');

        $groupsarray = array();
        foreach ($allgroups as $value) {
            // Checks availability of group and requests the content.
            if (is_object($value) && property_exists($value, 'name')) {
                $countmembers = count(groups_get_members($value->id));
                $href = new moodle_url('/blocks/groups/changevisibility.php', array('courseid' => $courseid, 'groupid' => $value->id));
                if (empty($DB->get_records('block_groups_hide', array('id' => $value->id)))) {
                    $groupsarray[] = $renderer->get_string_group($value, $href, $countmembers, true);
                } else {
                    $groupsarray[] = $renderer->get_string_group($value, $href, $countmembers, false);
                }
            }
        }

        // Empty block or block with checkboxes.
        $href = new moodle_url('/group/index.php', array('id' => $courseid));
        if (count($groupsarray) == 0) {
            $content .= html_writer::div(get_string('nogroups', 'block_groups'));
            $content .= $renderer->get_link_modify_groups($href);
        } else {
            // Since 3.3-r5 shows line with link to change all groups.
            $content .= $renderer->change_all_groups();
            $groupingsarray = $this->build_grouping_array($allgroupings, $courseid);
            if (!empty($groupingsarray)) {
                $content .= $renderer->teaching_groups_or_groupings_list($groupingsarray, false);
            }
            $content .= $renderer->teaching_groups_or_groupings_list($groupsarray, true);
            $content .= $renderer->get_link_modify_groups($href);
        }
        return $content;
    }

    /**
     * Returns all registered groups.
     * @return string
     */
    private function get_content_groupmembers() {
        global $COURSE, $DB, $PAGE;
        $enrolledgroups = array();
        $allgroups = groups_get_my_groups();
        // Necessary to show hidden groups to Course Managers.
        $access = has_capability('moodle/course:managegroups',  context_course::instance($COURSE->id));
        /* @var $renderer block_groups_renderer*/
        $renderer = $PAGE->get_renderer('block_groups');
        foreach ($allgroups as $group) {
            if (($group->courseid == $COURSE->id)) {
                $groupdbentry = $DB->get_records('block_groups_hide', array('id' => $group->id));
                if (!empty($groupdbentry)) {
                    $enrolledgroups[] = $renderer->get_tag_group($group, true);
                } else if ($access === true) {
                    $enrolledgroups[] = $renderer->get_tag_group($group, false);
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
     * Generates an array of groupingnames and their members.
     *
     * @param array $allgroupings
     * @return array
     */
    public function build_grouping_array($allgroupings, $courseid) {
        global $PAGE;
        /* @var $renderer block_groups_renderer*/
        $renderer = $PAGE->get_renderer('block_groups');
        $groupingsarray = array();
        $arrayofmembers = count_grouping_members($courseid);
        foreach ($allgroupings as $g => $value) {
            if (is_object($value) && property_exists($value, 'name')) {
                // Necessary DB query to prohibit multiple ids of grouping members.
                $countgroupingmember = $arrayofmembers[$value->id]->number;
                $groupingsarray[$g] = $renderer->get_grouping($value->name, $countgroupingmember);
            }
        }
        return $groupingsarray;
    }
}