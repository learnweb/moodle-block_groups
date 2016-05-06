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
 * Log report renderer.
 *
 * @package    block_groups
 * @copyright  2016 Nina Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

class block_groups_renderer extends plugin_renderer_base {
    /**
     * Lists grouping in html format
     *
     * @param $groupingarray
     * @return string
     */
    public function teaching_groupingslist($groupingsarray) {
        $empty = '';
        $contentgrouping = html_writer::tag('input', $empty, array('type' => "checkbox",
            'value' => "1", 'class' => "blockgroupsandgroupingcheckbox", 'id' => 'checkboxgrouping'));
        $contentgrouping .= html_writer::tag('label', get_string('groupings', 'block_groups'),
            array('for' => "checkboxgrouping"));
        $contentgrouping .= html_writer::alist($groupingsarray);
        return html_writer::tag('div', $contentgrouping, array('class' => "wrapperblockgroupsandgroupingcheckbox"));
    }
    /**
     * Lists groups in html format
     *
     * @param $grouparray
     * @return string
     */
    public function teaching_groupslist($groupsarray) {
        $empty = '';
        $contentgroups = html_writer::tag('input', $empty, array('type' => "checkbox", 'value' => "1",
            'class' => "blockgroupsandgroupingcheckbox", 'id' => 'checkboxgroup'));
        $contentgroups .= html_writer::tag('label', get_string('groups', 'block_groups'),
            array('for' => "checkboxgroup"));
        $contentgroups .= html_writer::alist($groupsarray);
        return html_writer::tag('div', $contentgroups, array('class' => 'wrapperblockgroupsandgroupingcheckbox'));
    }
    /**
     * Generates a link to refer to the groupsmodify page.
     *
     * @return string
     */
    public function get_link() {
        global $CFG, $COURSE;
        // Integer to identify the current course.
        $courseshown = $COURSE->id;
        return '<a href="' . $CFG->wwwroot . '/group/index.php?id=' . $courseshown . '">'.
            get_string('modify', 'block_groups'). '</a></br>';
    }
    /**
     * Generates components for groupsarrayitems that are not hidden
     *
     * @return string
     */
    public function get_groupsarrayempty($value, $href, $countmembers) {
        global $OUTPUT;
        $img = html_writer::img($OUTPUT->pix_url('t/show'), get_string('hidegroup', 'block_groups'));
        $ausrichtungdiv = html_writer::tag('div', $img, array('class' => "rightalign"));
        return html_writer::tag('span', $value->name . get_string('brackets', 'block_groups',
                $countmembers), array('class' => "hiddengroups")) . html_writer::link($href, $ausrichtungdiv,
            array('class' => 'block_groups_toggle', 'data-groupid' => $value->id, 'data-courseid' => $value->courseid,
                'data-action' => 'show'));
    }
    /**
     * Generates components for groupsarrayitems that are hidden
     *
     * @return string
     */
    public function get_groupsarraynonempty($value, $href, $countmembers) {
        global $OUTPUT;
        $img = html_writer::img($OUTPUT->pix_url('t/hide'), get_string('hidegroup', 'block_groups'));
        $ausrichtungdiv = html_writer::tag('div', $img, array('class' => "rightalign"));
        return $value->name . get_string('brackets', 'block_groups', $countmembers) . html_writer::link($href , $ausrichtungdiv,
            array('class' => 'block_groups_toggle', 'data-groupid' => $value->id, 'data-courseid' => $value->courseid,
                'data-action' => 'hide'));
    }
    public function get_groupingsarray($value) {
        $a = count(groups_get_grouping_members($value->id));
        return $value->name . get_string('brackets', 'block_groups', $a);
    }
    public function get_membership_content($enrolledgroups) {
        $membercontent = get_string('introduction', 'block_groups');
        $membercontent .= html_writer::alist($enrolledgroups);
        return html_writer::tag('div', $membercontent, array('class' => 'memberlist'));
    }
    public function get_tag_hiddengroups($name) {
        return html_writer::tag('div', $name, array('class' => "hiddengroups"));
    }

}