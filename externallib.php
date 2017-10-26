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
 * @package    block_groups
 * @copyright  2016/17 N Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/externallib.php");

/**
 * block_groups external functions
 *
 * @copyright  2016/17 N Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */
class block_groups_visibility_change extends external_api{

    /**
     * Specifies the input parameters.
     * @return external_function_parameters
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
     * Specifies the output parameters.
     * @return external_single_structure
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
     * @param array $groups
     * @return array of output_parameters
     */
    public static function create_output($groups) {
        global $PAGE, $CFG, $DB;
        $params = self::validate_parameters(self::create_output_parameters(), array('groups' => $groups));
        $PAGE->set_context(context_course::instance($params['groups']['courseid']));
        require_capability('moodle/course:managegroups', context_course::instance($params['groups']['courseid']));
        require_once($CFG->dirroot.'/blocks/groups/locallib.php');
        block_groups_db_transaction_change_visibility($params['groups']['id'], $params['groups']['courseid']);
        $renderer = $PAGE->get_renderer('block_groups');
        $href = new moodle_url('/blocks/groups/changevisibility.php', array('courseid' => $params['groups']['courseid'],
            'groupid' => $params['groups']['id']));
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
        return $output;
    }
}
/**
 * Class to implement the External Api to change all groups
 *
 * @package    block_groups
 * @copyright  2016/17 N Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_groups_visibilityall_change extends external_api{
    /**
     * Specifies the input parameters.
     */
    public static function create_allgroups_output_parameters() {
        return new external_function_parameters(
            array(
                'groups' => new external_single_structure(
                    array(
                        // Insert 1 for hide groups 0 for show groups.
                        'action' => new external_value(PARAM_RAW, 'action'),
                        'courseid' => new external_value(PARAM_INT, 'id of course'),
                    )
                )
            )
        );
    }

    /**
     * Specifies the output parameters.
     */
    public static function create_allgroups_output_returns() {
        return new external_single_structure(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course'),
                'newelement' => new external_value(PARAM_RAW, 'replace html-element'),
                'visibility' => new external_value(PARAM_INT, 'returns the visibility value'),
                'changedgroups' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'groupid' => new external_value(PARAM_INT, 'group-id', VALUE_OPTIONAL)
                        )
                    )
                )
            )
        );
    }
    /**
     * Changed the Database and returns the updated html content.
     * create_allgroups_output
     * @param array $groups
     * @return array
     */
    public static function create_allgroups_output($groups) {
        global $PAGE, $CFG, $DB;
        $params = self::validate_parameters(self::create_allgroups_output_parameters(), array('groups' => $groups));

        require_once($CFG->dirroot.'/blocks/groups/locallib.php');

        $PAGE->set_context(context_course::instance($params['groups']['courseid']));
        require_capability('moodle/course:managegroups', context_course::instance($params['groups']['courseid']));
        $groupsuitable = $DB->get_records('groups', array('courseid' => $params['groups']['courseid']), 'id ASC');
        // The Course has no groups therefore changing all is not possible.

        if (empty($groupsuitable)) {
            $output['courseid'] = $params['groups']['courseid'];
            $link = html_writer::link(new moodle_url('/group/index.php',
                array('id' => $params['groups']['courseid'])), 'modify groups');
            $content = html_writer::div(get_string('nogroups', 'block_groups')) . $link;
            $output['newelement'] = html_writer::div($content, array('class' => 'content'));
            $output['visibility'] = '0';
            $output['changedgroups'] = array();
            return $output;
        }
        $groupsvisible = array();
        $renderer = $PAGE->get_renderer('block_groups');

        foreach ($groupsuitable as $group) {
            $entry = $DB->get_records('block_groups_hide', array('id' => $group->id));

            // In the Case, that the group of the course has an entry in the 'block_groups_hide' table the group is visible.
            if (!empty($entry)) {
                $groupsvisible[$group->id] = $group->id;
            }
        }
        $groups = $groupsvisible;
        $outputvisibility = '3';
        if ($params['groups']['action'] == "show") {
            $outputvisibility = '4';
            $tempgroup = array();
            if (!empty($groupsuitable)) {
                foreach ($groupsuitable as $group) {
                    if (!empty($groupsvisible)) {
                        if (!(in_array($group->id, $groups))) {
                            $tempgroup[$group->id] = $group->id;
                        }
                    } else {
                        $tempgroup[$group->id] = $group->id;
                    }
                }
            }
            $groups = $tempgroup;
        }
        $output['changedgroups'] = array();
        if (!empty($groups)) {
            foreach ($groups as $group) {
                block_groups_db_transaction_change_visibility($group, $params['groups']['courseid']);
                array_push($output['changedgroups'], array('groupid' => $group));
            }
        }
        foreach ($groupsuitable as $group) {
            $fullgroup = groups_get_group($group->id);
            $href = new moodle_url('/blocks/groups/changevisibility.php',
                array('courseid' => $params['groups']['courseid'], 'groupid' => $group->id));
            $countmembers = count(groups_get_members($group->id));
            if ($params['groups']['action'] == 'hide') {
                $visibility = true;
            }
            if ($params['groups']['action'] == 'show') {
                $visibility = false;

            }
            $groupsarray[] = $renderer->get_string_group($fullgroup, $href, $countmembers, $visibility);
        }
        $output['newelement'] = html_writer::alist($groupsarray, array('class' => 'wrapperlistgroup'));
        $output['courseid'] = $params['groups']['courseid'];

        if (empty($groups)) {
            $output['visibility'] = $outputvisibility;
            return $output;
        }
        // Parameter $outputvisibility 0->nogroups 1 -> hidden 2->visible 3-> all are hidden 4-> all are visible.
        if ($params['groups']['action'] == 'hide') {
            $outputvisibility = 1;
        }
        if ($params['groups']['action'] == 'show') {
            $outputvisibility = 2;
        }
        $output['visibility'] = $outputvisibility;
        return $output;
    }
}
