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
 * block_groups renderer.
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
     * @param $groupingsarray
     * @return string
     */
    public function teaching_groupingslist($groupingsarray) {
        $contentgrouping = html_writer::tag('input', '', array('type' => "checkbox",
            'value' => "1", 'class' => "blockgroupsandgroupingcheckbox", 'id' => 'checkboxgrouping')) .
            html_writer::tag('label', get_string('groupings', 'block_groups'), array('for' => "checkboxgrouping"));
        $contentgrouping .= html_writer::alist($groupingsarray);
        return html_writer::tag('div', $contentgrouping, array('class' => "wrapperblockgroupsandgroupingcheckbox"));
    }
    /**
     * Lists groups in html format
     *
     * @param $groupsarray
     * @return string
     */
    public function teaching_groupslist($groupsarray) {
        $contentgroups = html_writer::tag('input', '', array('type' => "checkbox", 'value' => "1",
            'class' => "blockgroupsandgroupingcheckbox", 'id' => 'checkboxgroup')) .
            html_writer::tag('label', get_string('groups', 'block_groups'), array('for' => "checkboxgroup"));
        $contentgroups .= html_writer::alist($groupsarray);
        return html_writer::tag('div', $contentgroups, array('class' => 'wrapperblockgroupsandgroupingcheckbox'));
    }
    /**
     * Generates a link to refer to the groupsmodify page.
     *
     * @return string
     */
    public function get_link_modify_groups($courseid) {
        global $CFG;
        return '<a href="' . $CFG->wwwroot . '/group/index.php?id=' . $courseid . '">'.
            get_string('modify', 'block_groups'). '</a></br>';
    }
    /**
     * Generates components for groupsarrayitems that are not hidden
     *
     * @params value
     * @params href
     * @params countmembers
     * @return string
     */
    public function get_string_visiblegroup($value, $href, $countmembers) {
        global $OUTPUT;
        $img = html_writer::img($OUTPUT->pix_url('t/show'), get_string('hidegroup', 'block_groups'),
            array('class' => "imggroup-". $value->id));
        $ausrichtungdiv = html_writer::div( $img, 'rightalign');
        $line = html_writer::span($value->name . get_string('brackets', 'block_groups', $countmembers), "hiddengroups") .
            html_writer::link($href, $ausrichtungdiv, array('class' => 'block_groups_toggle', 'data-groupid' => $value->id,
                'data-action' => 'show'));
        return html_writer::span($line, 'group-'. $value->id);
    }
    /**
     * Generates components for groupsarrayitems that are hidden
     *
     * @params value
     * @params href
     * @params countmembers
     * @return string
     */
    public function get_string_hiddengroup($value, $href, $countmembers) {
        global $OUTPUT;
        $img = html_writer::img($OUTPUT->pix_url('t/hide'), get_string('hidegroup', 'block_groups'),
            array('class' => "imggroup-". $value->id));
        $ausrichtungdiv = html_writer::div($img, 'rightalign');
        $line = $value->name . get_string('brackets', 'block_groups', $countmembers) . html_writer::link($href , $ausrichtungdiv,
            array('class' => 'block_groups_toggle', 'data-groupid' => $value->id, 'data-action' => 'hide'));
        return html_writer::span($line, 'group-'. $value->id);
    }
    /**
     * Generates components for groupsarrayitems that are hidden
     *
     * @params name name of the grouping
     * @params counter number of members of the grouping
     * @return string
     */
    public function get_grouping($name, $counter) {
        return $name . get_string('brackets', 'block_groups', $counter);
    }
    /**
     * Returns the frame for the memberlist.
     *
     * @params enrolledgroups
     * @return string
     */
    public function get_membership_content($enrolledgroups) {
        $membercontent = get_string('introduction', 'block_groups');
        $membercontent .= html_writer::alist($enrolledgroups);
        return html_writer::div($membercontent, 'memberlist');
    }
    /**
     * Returns the html-text for hidden groups.
     * @params group
     * @return string
     */
    public function get_tag_hiddengroup($group) {
        return html_writer::span($group->name, "hiddengroups" . " membergroup-" . $group->id);
    }
    /**
     * Returns the html-text for visible groups.
     *
     * @params group
     * @return string
     */
    public function get_tag_visiblegroup($group) {
        return html_writer::span($group->name, "membergroup-" . $group->id);
    }
}