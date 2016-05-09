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
 * External blocks_groups API
 *
 * @package block_groups
 * @category
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->libdir/externallib.php");

/**
 * blocks_groups external functions
 *
 * @package    blocks_groups
 * @category   external
 * @copyright  2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.9
 */
class block_groups_visibility_change extends external_api{

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
    public static function create_output_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'id of group'),
                'courseid' => new external_value(PARAM_INT, 'id of course'),
                'newelement' => new external_value(PARAM_TEXT, 'replace html-element'),
                'visibility' => new external_value(PARAM_INT, 'returns the visibility value')
            )
        );
    }
    /**
     *  Changed the Database and returns the updated html content.
     *
     * @params $groups
     * @return string
     */
    public static function create_output($groups) {
        global $DB, $PAGE, $CFG;
        $params = self::validate_parameters(self::create_output_parameters(), array('groups' => $groups));
        $transaction = $DB->start_delegated_transaction();
        // If an exception is thrown in the below code, all DB queries in this code will be rollback.
        $groupsuitable = $DB->get_record('groups', array('id' => $params['groups']['id'],
            'courseid' => $params['groups']['courseid']));
        $groupvisible = $DB->get_records('block_groups_hide', array('id' => $params['groups']['id'], ));
        // Takes changes in the Database.
        if (!empty($groupsuitable)) {
            if (empty($groupvisible)) {
                $DB->import_record('block_groups_hide', array('id' => $params['groups']['id']));
            }
            if (!empty($groupvisible)) {
                $DB->delete_records('block_groups_hide', array('id' => $params['groups']['id']));
            }
        }
        $transaction->allow_commit();
        $renderer = $PAGE->get_renderer('block_groups');
        $href = $CFG->wwwroot . '/blocks/groups/changevisibility.php?courseid=' . $params['groups']['courseid'] .
            '&groupid=' . $params['groups']['id'];
        $countmembers = count(groups_get_members($params['groups']['id']));
        $myvalueobject = groups_get_group($params['groups']['id']);
        $output = array('groups' => array('id' => $params['groups']['id'], 'courseid' => $params['groups']['courseid']));
        // Generates the Output component.
        if (empty($groupvisible)) {
//            ohne hidegroups geschrieben
            $output['groups']['newelement'] = $renderer->get_groupsarraynonempty($myvalueobject, $href, $countmembers);
            $output['groups']['visibility'] = 1;
        }
        if (!empty($groupvisible)) {
//            hide groups wird reingeschrieben
            $output['groups']['newelement'] = $renderer->get_groupsarrayempty($myvalueobject, $href, $countmembers);
            $output['groups']['visibility'] = 0;
        }
        return  $output['groups'];
    }
}
