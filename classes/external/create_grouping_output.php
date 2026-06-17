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

use context_course;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use html_writer;
use moodle_url;

/**
 * create_grouping_output external function
 *
 * @package    block_groups
 * @copyright  2026 Lena Herfeldt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_grouping_output extends external_api {
    /**
     * Specifies the input parameters.
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters(
            [
                        'grouping' => new external_single_structure(
                            [
                                        'groupingid' => new external_value(PARAM_INT, 'id of grouping'),
                                        'courseid' => new external_value(PARAM_INT, 'id of course'),
                                        'action' => new external_value(PARAM_ALPHA, 'show or hide'),
                                ]
                        ),
                ]
        );
    }

    /**
     * Specifies the output parameters.
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure(
            [
                        'courseid' => new external_value(PARAM_INT, 'id of course'),
                        'groupingid' => new external_value(PARAM_INT, 'id of grouping'),
                        'newelement' => new external_value(PARAM_RAW, 'replace html-element'),
                        'visibility' => new external_value(PARAM_INT, 'returns the visibility value'),
                        'changedgroups' => new external_multiple_structure(
                            new external_single_structure(
                                [
                                                'groupid' => new external_value(PARAM_INT, 'group-id'),
                                        ]
                            )
                        ),
                ]
        );
    }

    /**
     * Changed the Database and returns the updated html content.
     * create_grouping_output
     * @param array $grouping
     * @return array
     */
    public static function execute($grouping) {
        global $CFG, $PAGE, $DB;
        $params = self::validate_parameters(self::execute_parameters(), ['grouping' => $grouping]);
        $courseid = $params['grouping']['courseid'];
        $groupingid = $params['grouping']['groupingid'];
        $action = $params['grouping']['action'];
        $context = context_course::instance($courseid);

        $PAGE->set_context($context);
        self::validate_context($context);
        require_capability('moodle/course:managegroups', $context);

        require_once($CFG->dirroot . '/blocks/groups/locallib.php');
        require_once($CFG->dirroot . '/group/lib.php');

        $groupingexists = $DB->get_record('groupings', [
                'id' => $groupingid,
                'courseid' => $courseid,
                ]);

        if (!$groupingexists) {
            throw new \invalid_parameter_exception('Grouping does not exist or does not belong to course');
        }

        $groupids = self::get_group_ids_for_grouping($groupingid, $courseid);

        if (empty($groupids)) {
            return [
                    'courseid' => $courseid,
                    'groupingid' => $groupingid,
                    'newelement' => self::generate_groups_html($courseid),
                    'visibility' => 0,
                    'changedgroups' => [],
            ];
        }

        $changedgroups = self::update_group_visibility($groupids, $courseid, $action);
        $statuscode = self::get_status_code($action, $changedgroups);

        return [
                'courseid' => $courseid,
                'groupingid' => $groupingid,
                'newelement' => self::generate_groups_html($courseid),
                'visibility' => $statuscode,
                'changedgroups' => $changedgroups,
        ];
    }

    /**
     * Returns all group ids belonging to a grouping.
     *
     * @param int $groupingid
     * @param int $courseid
     * @return array
     */
    private static function get_group_ids_for_grouping($groupingid, $courseid) {
        global $DB;

        $sql = "SELECT g.id
                    FROM {groups} g
                    JOIN {groupings_groups} gg ON gg.groupid = g.id
                    WHERE gg.groupingid = :groupingid AND g.courseid = :courseid
                ORDER BY g.id ASC";

        $records = $DB->get_records_sql($sql, [
                'groupingid' => $groupingid,
                'courseid' => $courseid,
        ]);

        return array_keys($records);
    }

    /**
     * Changes the visibility of the groups in the database.
     * @param array $groupids
     * @param int $courseid
     * @param string $action
     * @return array
     */
    private static function update_group_visibility($groupids, $courseid, $action) {
        global $DB;

        $changedgroups = [];
        $targetvisible = ($action === 'show');

        foreach ($groupids as $groupid) {
            $currentlyvisible = $DB->record_exists('block_groups_hide', ['id' => $groupid]);

            if ($targetvisible === $currentlyvisible) {
                continue;
            }

            block_groups_set_visibility($groupid, $courseid, $targetvisible);
            $changedgroups[] = ['groupid' => $groupid];
        }
        return $changedgroups;
    }

    /**
     * Generates the complete updated group list html.
     *
     * @param int $courseid
     * @return string
     */
    private static function generate_groups_html($courseid) {
        global $PAGE, $DB;

        $renderer = $PAGE->get_renderer('block_groups');
        $groups = $DB->get_records('groups', ['courseid' => $courseid], 'id ASC');
        $groupsarray = [];

        foreach ($groups as $group) {
            $fullgroup = groups_get_group($group->id);
            $href = new moodle_url(
                '/blocks/groups/changevisibility.php',
                [
                            'courseid' => $courseid,
                            'groupid' => $group->id,
                    ]
            );
            $countmembers = count(groups_get_members($group->id));
            $groupvisible = $DB->record_exists('block_groups_hide', ['id' => $group->id]);
            $hiddenforrenderer = !$groupvisible;
            $groupsarray[] = $renderer->get_string_group(
                $fullgroup,
                $href,
                $countmembers,
                $hiddenforrenderer,
            );
        }
        return html_writer::alist($groupsarray, ['class' => 'wrapperlistgroup']);
    }

    /**
     * Returns status code depending on action and whether anything changed.
     *
     * @param string $action
     * @param array $changedgroups
     * @return int
     */
    private static function get_status_code($action, $changedgroups) {
        if (empty($changedgroups)) {
            return ($action === 'show') ? 4 : 3;
        }

        return ($action === 'hide') ? 1 : 2;
    }
}
