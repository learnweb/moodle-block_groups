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
 *
 *
 * @package block_groups
 * @category
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->libdir/externallib.php");

class block_groups_visibility_change extends external_api{

    public static function create_output_parameters() {
        return new external_function_parameters(
            array(
                'groups' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'id of group'),
                            'courseid' => new external_value(PARAM_INT, 'id of course'),
                        )
                    )
                )
            )
        );
    }
    public static function create_output_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'id of group'),
                    'courseid' => new external_value(PARAM_INT, 'id of course'),
                    'visibility' => new external_value(PARAM_INT, 'Visibility of Course'),
                )
            )
        );
    }
    public static function create_output($groups) {
        global $CFG, $DB;
        require_once("$CFG->dirroot/group/lib.php");
        $params = self::validate_parameters(self::create_output_parameters(), array('groups' => $groups));
        $transaction = $DB->start_delegated_transaction();
        // If an exception is thrown in the below code, all DB queries in this code will be rollback.
        foreach ($params['groups'] as $group) {
            $groupsuitable = $DB->get_record('groups', array('id' => $group->id, 'courseid' => $group->courseid));
            $groupvisible = $DB->get_records('block_groups_hide', array('id' => $group->id, ));
            if (!empty($groupsuitable)) {
                if (empty($groupvisible)) {
                    $DB->import_record('block_groups_hide', array('id' => $group->id));
                }
                if (!empty($groupvisible)) {
                    $DB->delete_records('block_groups_hide', array('id' => $group->id));
                }
            }
        }
        $transaction->allow_commit();
        return $groups;
    }
/*    public function create_output_is_allowed_from_ajax() {
        return true;
    }*/
}
