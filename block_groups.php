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
        $access = has_capability('moodle/course:managegroups',  context_course::instance($COURSE->id));

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
        global  $COURSE, $CFG, $DB,$OUTPUT;
        // Initialises an array of groups.
        $groupsarray = array();
        // Initialises an array of groupings.
        $groupingsarray = array();
        // Array to save all groups.
        $allgroups = groups_get_all_groups($COURSE->id);
        // Array to save all groupings.
        $allgroupings = groups_get_all_groupings($COURSE->id);
        // String initialises an empty string.
        $groupstext = '';
        // Integer to identify the current course.
        $courseshown = $COURSE->id;
        // Groups and Grouping Names are saved in arrays.

        $dbman = $DB->get_manager();

        foreach ($allgroups as $g => $value) {
            if (is_object($value) && property_exists($value, 'name')) {
                $a = count(groups_get_members($value->id));
                $href = 'http://localhost/moodle/course/view.php?id=3';
                $img = html_writer::img($OUTPUT->pix_url('t/hide'), 'hide group');
                $ausrichtungdiv = html_writer::tag('div', $img, array('class' => "rightalign"));
                $groupsarray[$g] = $value->name . get_string('brackets', 'block_groups', $a) .
                    html_writer::link($href , $ausrichtungdiv);
            }
        }

        foreach ($allgroupings as $g => $value) {
            if (is_object($value) && property_exists($value, 'name')) {
                $a = count(groups_get_grouping_members($value->id));
                $groupingsarray[$g] = $value->name  . get_string('brackets', 'block_groups', $a);
            }
        }

        // Empty block or block with checkboxes.
        if (count($groupsarray) == 0) {
            $groupstext = '<a href="' . $CFG->wwwroot . '/group/index.php?id=' . $courseshown . '">'.
                get_string('modify', 'block_groups'). '</a></br>';
            $groupstext .= get_string('nogroups', 'block_groups');
            return $groupstext;
        } else {
            $contentcheckbox = '';
            if (!(empty($groupingsarray))) {
                $contentgrouping = html_writer::tag('label', get_string('groupings', 'block_groups'),
                        array('for' => "blockgroupsandgroupingcheckboxgrouping"));
                $contentgrouping .= html_writer::alist($groupingsarray);
                $groupingcheckbox = html_writer::tag('input', $contentgrouping, array('type' => "checkbox",
                        'value' => "1", 'id' => "blockgroupsandgroupingcheckboxgrouping", 'name' => "checkboxgrouping"));
                $contentcheckbox .= html_writer::tag('div', $groupingcheckbox,
                        array('class' => "blockgroupsandgroupingcheckboxgrouping"));
            }

            $contentgroups = html_writer::tag('label', get_string('groups', 'block_groups'),
                    array('for' => "blockgroupsandgroupingcheckboxgroup"));
            $contentgroups .= html_writer::alist($groupsarray);
            $groupscheckbox = html_writer::tag('input', $contentgroups, array('type' => "checkbox", 'value' => "1",
                    'id' => "blockgroupsandgroupingcheckboxgroup", 'name' => "checkboxgroup"));
            $contentcheckbox .= html_writer::tag('div', $groupscheckbox,
                    array('class' => "blockgroupsandgroupingcheckboxgroup"));
            $groupstext .= html_writer::tag('div', $contentcheckbox,
                    array('class' => 'blockgroupandgroupingcheckbox'));
            $groupstext .= '<a href="' . $CFG->wwwroot . '/group/index.php?id=' . $courseshown . '">'.
                    get_string('modify', 'block_groups'). '</a></br>';

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
        global $COURSE;
        // Initialises an array to save the enrolled groups.
        $enrolledgroups = array();
        // List renders all enrolled groups.
        $allgroups = groups_get_my_groups();
        foreach ($allgroups as $valueall) {
            if ($valueall->courseid == $COURSE->id) {
                $enrolledgroups[] = $valueall->name;
            }
        }
        // Returns an empty block.
        if (empty($enrolledgroups)) {
            $groupstext = '';
            return $groupstext;
        }
        // List all enrolled groups.
        $membercontent = get_string('introduction', 'block_groups');
        $membercontent .= html_writer::alist($enrolledgroups);
        $groupstext = html_writer::tag('div', $membercontent, array('class' => 'memberlist'));
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
}