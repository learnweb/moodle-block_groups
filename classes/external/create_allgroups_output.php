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

namespace block_groups\external;

use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use context_course;
use html_writer;
use moodle_url;

/**
 * create_allgroups_output external function
 *
 * @package    block_groups
 * @copyright  2026 Daniel Meißner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_allgroups_output extends \core_external\external_api {
    /**
     * Specifies the input parameters.
     */
    public static function execute_parameters() {
        return new external_function_parameters(
            [
                'groups' => new external_single_structure(
                    [
                        // Insert 1 for hide groups 0 for show groups.
                        'action' => new external_value(PARAM_RAW, 'action'),
                        'courseid' => new external_value(PARAM_INT, 'id of course'),
                    ]
                ),
            ]
        );
    }

    /**
     * Specifies the output parameters.
     */
    public static function execute_returns() {
        return new external_single_structure(
            [
                'courseid' => new external_value(PARAM_INT, 'id of course'),
                'newelement' => new external_value(PARAM_RAW, 'replace html-element'),
                'visibility' => new external_value(PARAM_INT, 'returns the visibility value'),
                'changedgroups' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'groupid' => new external_value(PARAM_INT, 'group-id', VALUE_OPTIONAL),
                        ]
                    )
                ),
            ]
        );
    }

    /**
     * Changed the Database and returns the updated html content.
     * create_allgroups_output
     * @param array $groups
     * @return array
     */
    public static function execute($groups) {
        global $PAGE, $CFG, $DB;
        $params = self::validate_parameters(self::execute_parameters(), ['groups' => $groups]);
        $PAGE->set_context(context_course::instance($params['groups']['courseid']));
        require_capability('moodle/course:managegroups', context_course::instance($params['groups']['courseid']));

        $groupsuitable = $DB->get_records('groups', ['courseid' => $params['groups']['courseid']], 'id ASC');

        // The Course has no groups therefore changing all is not possible.
        if (empty($groupsuitable)) {
            return self::createNoGroupsResponse($params['groups']['courseid']);
        }

        $groupsToChange = self::get_groups_to_change($params['groups']['action'], $groupsuitable);
        $changedGroups = self::update_group_visibility($groupsToChange, $params['groups']['courseid']);
        $html = self::genererate_groups_html($groupsuitable, $params['groups']['courseid'], $params['groups']['action']);
        $statusCode = self::get_status_code($params['groups']['action'], $groupsToChange);

        return [
            'courseid' => $params['groups']['courseid'],
            'newelement' => $html,
            'visibility' => $statusCode,
            'changedgroups' => $changedGroups
        ];
    }

    private static function createNoGroupsResponse($courseid) {
        $output['courseid'] = $courseid;
        $link = html_writer::link(new moodle_url(
                '/group/index.php',
                ['id' => $courseid]
        ), 'modify groups');
        $content = html_writer::div(get_string('nogroups', 'block_groups')) . $link;
        $output['newelement'] = html_writer::div($content, ['class' => 'content']);
        $output['visibility'] = '0';
        $output['changedgroups'] = [];
        return $output;
    }

    private static function get_groups_to_change($action, $groupsuitable) {
        global $DB;

        $groupsvisible = [];
        foreach ($groupsuitable as $group) {
            $entry = $DB->get_records('block_groups_hide', ['id' => $group->id]);

            // In the Case, that the group of the course has an entry in the 'block_groups_hide' table the group is visible.
            if (!empty($entry)) {
                $groupsvisible[$group->id] = $group->id;
            }
        }

        if ($action == "show") {
            $tempgroup = [];
            foreach ($groupsuitable as $group) {
                if (!in_array($group->id, $groupsvisible)) {
                    $tempgroup[$group->id] = $group->id;
                }
            }
            return $tempgroup;
        }

        return $groupsvisible;
    }

    private static function update_group_visibility($groups, $courseid) {
        global $CFG;
        require_once($CFG->dirroot . '/blocks/groups/locallib.php');

        $changedgroups = [];
        foreach ($groups as $groupid) {
            block_groups_db_transaction_change_visibility($groupid, $courseid);
            $changedgroups[] = ['groupid' => $groupid];
        }
        return $changedgroups;
    }

    private static function genererate_groups_html($groupsuitable, $courseid, $action) {
        global $PAGE;

        $renderer = $PAGE->get_renderer('block_groups');
        $groupsarray = [];

        foreach ($groupsuitable as $group) {
            $fullgroup = groups_get_group($group->id);
            $href = new moodle_url(
                    '/blocks/groups/changevisibility.php',
                    ['courseid' => $courseid, 'groupid' => $group->id]
            );
            $countmembers = count(groups_get_members($group->id));
            $visibility = ($action == "show") ? false : true;
            $groupsarray[] = $renderer->get_string_group(
                    $fullgroup, $href, $countmembers, $visibility
            );
        }
        return html_writer::alist($groupsarray, ['class' => 'wrapperlistgroup']);
    }

    private static function get_status_code($action, $groupsToChange) {
        if (empty($groupsToChange)) {
            return ($action == "show") ? 4 : 3;
        }

        return ($action == "hide") ? 1 : 2;
    }
}
