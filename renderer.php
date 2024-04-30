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
 * @copyright  2016/17 N Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class of the block_groups renderer.
 *
 * @package    block_groups
 * @copyright  2016/17 N Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_groups_renderer extends plugin_renderer_base {
    /**
     * Lists grouping in html format
     *
     * @param array $elementarray
     * @param bool $group true is for groups false for groupings
     * @return string html-string
     */
    public function teaching_groups_or_groupings_list($elementarray, $group) {
        if ($group === true) {
            $type = 'group';
        } else {
            $type = 'grouping';
        }
        $contentgroups = html_writer::tag('input', '', ['type' => "checkbox", 'value' => "1",
                'class' => "blockgroupsandgroupingcheckbox", 'id' => 'checkbox' . $type]) .
            html_writer::tag('label', get_string($type, 'block_groups'), ['for' => "checkbox" . $type]);
        $contentgroups .= html_writer::alist($elementarray, ['class' => 'wrapperlist' . $type]);
        return html_writer::tag('div', $contentgroups, ['class' => 'wrapperblockgroupsandgroupingcheckbox']);
    }

    /**
     * Generates a link to refer to the groupsmodify page.
     * @param string $href
     * @return string html-string
     */
    public function get_link_modify_groups($href) {
        return html_writer::link($href , get_string('modify', 'block_groups'));
    }
    /**
     * Generates components for a groupsarrayentry.
     * (false for hidden groups)
     *
     * @param stdClass $value
     * @param string $href
     * @param integer $countmembers
     * @param boolean $visibility
     * @return string html-string
     */
    public function get_string_group($value, $href, $countmembers, $visibility) {
        if ($visibility === false) {
            $action = 'hide';

            $message = get_string('hidegroup', 'block_groups');
            $spanstring = '';
        } else {
            $action = 'show';
            $message = get_string('showgroup', 'block_groups');
            $spanstring = 'hiddengroups';
        }
        $icon = $this->output->pix_icon('t/' . $action, $message, 'moodle',
            ['class' => "imggroup-". $value->id . ' imggroup']);

        $rightaligndiv = html_writer::div($icon, 'rightalign');
        $line = html_writer::span($value->name . '   ' .get_string('brackets', 'block_groups', $countmembers), $spanstring) .
            html_writer::link($href, $rightaligndiv, ['class' => 'block_groups_toggle', 'data-groupid' => $value->id,
                'data-action' => $action]);
        return html_writer::span($line, 'group-'. $value->id);
    }
    /**
     * Generates string for a grouping list item
     * @param string $name
     * @param integer $counter
     * @return string html-string
     */
    public function get_grouping($name, $counter) {
        return $name . '   ' . get_string('brackets', 'block_groups', $counter);
    }
    /**
     * Renders line to change all groups.
     * @return string html string
     */
    public function change_all_groups() {
        // Creates two urls for showing all groups hiding all groups.
        $urlshow = $this->create_all_groups_link('show', false);
        $urlhide = $this->create_all_groups_link('hide', false);

        $line = html_writer::span(get_string('changeallgroups', 'block_groups'), 'wrapperblockgroupsallgroups') .
           $urlshow . $urlhide;
        return $line;
    }
    /**
     * Returns the frame for the memberlist.
     * @param array $enrolledgroups
     * @return string html-string
     */
    public function get_membership_content($enrolledgroups) {
        $membercontent = get_string('introduction', 'block_groups');
        $membercontent .= html_writer::alist($enrolledgroups);
        return html_writer::div($membercontent, 'memberlist');
    }
    /**
     * Returns the html-span for a single group.
     * (false vor hidden groups)
     * @param stdClass $group
     * @param boolean $visibility
     * @return string html-string
     */
    public function get_tag_group($group, $visibility) {
        $spanclasses = "membergroup-" . $group->id . ' block-groups-membergroup';
        if ($visibility === false) {
            $spanclasses .= ' hiddengroups';
        }
        return html_writer::span($group->name, $spanclasses);
    }

    /**
     * Internal Function to create two links for showing all groups hiding all groups.
     * @param string $action show/hide
     * @param bool $reverseimage whether to reverse the icon (show crossed out eye for 'show' action).
     * @return string html_link
     */
    private function create_all_groups_link($action, $reverseimage = true) {
        global $CFG, $COURSE;
        if ($action === 'hide') {
            $actionnumber = 0;
            $reverse = 'show';
        } else {
            $actionnumber = 1;
            $reverse = 'hide';
        }
        $urlhide = new moodle_url($CFG->wwwroot . '/blocks/groups/changeallgroups.php',
            ['courseid' => $COURSE->id, 'hide' => $actionnumber]);

        $icon = $this->output->pix_icon('t/' . ($reverseimage ? $reverse : $action), get_string($reverse . 'group', 'block_groups'),
            'moodle', ['class' => "imggroup-all imggroup"]);
        $rightaligndiv = html_writer::div($icon, 'rightalign');
        return html_writer::link($urlhide, $rightaligndiv, ['data-action' => $reverse, 'class' => 'block_groups_all_toggle']);
    }
}
