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
 * External block_groups API
 *
 * @package block_groups
 * @category
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/externallib.php");

/**
 * block_groups external functions
 *
 * @category  External functions API
 * @copyright  2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.9
 */
class block_groups_visibility_change extends external_api{

    /**
     * Specifys the input parameters.
     */
    public static function create_output_parameters() {
        return new external_function_parameters(
            array(
                'groups' => new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'id of group'),
                            'courseid' => new external_value(PARAM_INT, 'id of course'),
                        )
                    )
                )
        );
    }

    /**
     * Specifys the output parameters.
     */
    public static function create_output_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'id of group'),
                'courseid' => new external_value(PARAM_INT, 'id of course'),
                'newelement' => new external_value(PARAM_RAW, 'replace html-element'),
                'visibility' => new external_value(PARAM_INT, 'returns the visibility value')
            )
        );
    }
    /**
     * Changed the Database and returns the updated html content.
     *
     * @param $groups
     * @return array
     */
    public static function create_output($groups) {
        global $PAGE, $CFG, $DB;
        $params = self::validate_parameters(self::create_output_parameters(), array('groups' => $groups));
        $PAGE->set_context(context_course::instance($params['groups']['courseid']));
        require_capability('moodle/course:managegroups', context_course::instance($params['groups']['courseid']));
        require_once($CFG->dirroot.'/blocks/groups/locallib.php');
        block_groups_db_transaction_change_visibility($params['groups']['id'], $params['groups']['courseid']);
        $renderer = $PAGE->get_renderer('block_groups');
        $href = $CFG->wwwroot . '/blocks/groups/changevisibility.php?courseid=' . $params['groups']['courseid'] .
            '&groupid=' . $params['groups']['id'];
        $countmembers = count(groups_get_members($params['groups']['id']));
        $group = groups_get_group($params['groups']['id']);
        $output = array('id' => $params['groups']['id'], 'courseid' => $params['groups']['courseid']);
        // Generates the Output component.
        $groupvisible = $DB->get_records('block_groups_hide', array('id' => $params['groups']['id']));
        if (empty($groupvisible)) {
            $output['newelement'] = $renderer->get_string_group($group, $href, $countmembers, true);
            $output['visibility'] = 1;
        }
        if (!empty($groupvisible)) {
            $output['newelement'] = $renderer->get_string_group($group, $href, $countmembers, false);
            $output['visibility'] = 0;
        }
        return  $output;
    }
    /**
     * Specifys the input parameters.
     */
    public static function create_allgroups_output_parameters() {
        return new external_function_parameters(
            array(
                'groups' => new external_single_structure(
                    array(
                        // Insert 1 for hide groups 0 for show groups.
                        'action' => new external_value(PARAM_INT, 'id of group'),
                        'courseid' => new external_value(PARAM_INT, 'id of course'),
                    )
                )
            )
        );
    }

    /**
     * Specifys the output parameters.
     */
    public static function create_allgroups_output_returns() {
        return new external_single_structure(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course'),
                'newelement' => new external_value(PARAM_RAW, 'replace html-element'),
                'visibility' => new external_value(PARAM_INT, 'returns the visibility value')
            )
        );
    }
    /**
     * Changed the Database and returns the updated html content.
     * create_allgroups_output
     * @param $groups
     * @return array
     */
    public static function create_allgroups_output($groups) {
        global $PAGE, $CFG, $DB;
        $params = self::validate_parameters(self::create_output_parameters(), array('groups' => $groups));
        $PAGE->set_context(context_course::instance($params['groups']['courseid']));
        require_capability('moodle/course:managegroups', context_course::instance($params['groups']['courseid']));
        require_once($CFG->dirroot.'/blocks/groups/locallib.php');
        $groupsuitable = $DB->get_records('groups', array($params['groups']['courseid']));
        // The Course has no groups therefore changing all is not possible.
        if (empty($groupsuitable)) {
            // TODO: Suitable Params?
        }
        $groups = array();
        $groupsvisible = array();
        foreach ($groupsuitable as $group) {
            $entry = $DB->get_records('block_groups_hide', array('id' => $group->id));
            // In the Case, that the group of the course has an entry in the 'block_groups_hide' table the group is visible.
            if (!empty($entry)) {
                $groupsvisible[$group->id] = $group->id;
            }
        }
        $groups = $groupsvisible;
        $messageaction = 'hidden';
        if ($params['groups']['action'] == 1) {
            $messageaction = 'visible';
            $tempgroup = array();
            foreach ($groupsuitable as $group) {
                if (!empty($groupsvisible)) {
                    if (!(in_array($group->id, $groups))) {
                        $tempgroup[$group->id] = $group->id;
                    }
                } else {
                    $tempgroup[$group->id] = $group->id;
                }
            }
            $groups = $tempgroup;
        }
        if (empty($groups)) {
            // TODO: suitable params
        }
        require_once($CFG->dirroot.'/blocks/groups/locallib.php');
        foreach ($groups as $group) {
            block_groups_db_transaction_change_visibility($group, $params['groups']['courseid']);
        }
        // TODO: create htmlelement
    }
}
