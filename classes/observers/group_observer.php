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
 * Event observers for the groups block.
 *
 * @package    block_groups
 * @copyright  2026 Lena Herfeldt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_groups\observers;

/**
 * Observer for group events
 */
class group_observer {
    /**
     * Handles the group_created event.
     * Automatically adds new groups to block_groups_hide table if show_groups_default_setting is enabled.
     *
     * @param \core\event\group_created $event
     */
    public static function group_created(\core\event\group_created $event) {
        global $DB;

        // Checking if the setting is enabled.
        $showalways = get_config('block_groups', 'show_groups_default_setting');

        if (!$showalways) {
            return;
        }

        $groupid = $event->objectid;

        // Insert directly into the block_groups_hide table to make it visible from the start.
        if (!$DB->record_exists('block_groups_hide', ['id' => $groupid])) {
            $DB->insert_record_raw('block_groups_hide', ['id' => $groupid], true, false, true);
        }
    }

    /**
     * Handles the group_deleted event.
     * Automatically deletes the groups from the block_groups_hide table when a group itself has been deleted.
     *
     * @param \core\event\group_deleted $event
     */
    public static function group_deleted(\core\event\group_deleted $event) {
        global $DB;

        $groupid = $event->objectid;

        // Remove the group from the block_groups_hide table.
        $DB->delete_records('block_groups_hide', ['id' => $groupid]);
    }
}
