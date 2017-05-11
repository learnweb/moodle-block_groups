// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
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
 * Javascript module for block_groups
 *
 * @package    block_groups
 * @copyright  2016 Nina Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery','core/ajax','core/url','core/notification'], function($, ajax, notification, url) {
    /**
     * Methode to remove warnings
     * @param int $groupid
     */
    var remove_warning = function(groupid){
        $('.block_groups').find('.warning' + groupid).remove();
    };
    /**
     * Removes the Spinner Class
     * @param int $id that identifies to which group the spinner belongs to.
     */
    var remove_spinner = function (groupid) {
        $('.block_groups').find('.spinner' + groupid).remove();
    };
    /**
     * Creates a warning message.
     */
    var create_warning_message = function (){
        notification.alert(M.util.get_string('errortitle', 'block_groups'),
            M.util.get_string('nochangeindatabasepossible', 'block_groups'),
            M.util.get_string('errorbutton', 'block_groups'));
    };
    /**
     * Initialises Spinner.
     * @param int $groupid
     */
    var add_spinner = function (action) {
        if($('.block_groups').find('.warning' + action).length > 0){
            remove_warning(action);
        }
        var imgurl = url.imageUrl("i/loading_small",'moodle');
        var spinner = document.createElement("img");
        spinner.className = 'spinner' + action;
        spinner.src = imgurl;
        spinner.hidden = false;
        $('.block_groups').find('.wrapperlistgroup').before(spinner);
    };
    /**
     * Adds a warning(triangle with exclamation mark) in case the response is empty or the response throws an error.
     * @param int $groupid
     */
    var add_warning = function (action){
        if($('.block_groups').find('.warning' + action).length > 0){
            remove_spinner(action);
            create_warning_message();
            return false;
        }
        var imgurl = url.imageUrl("i/warning",'moodle');
        var warning = document.createElement("img");
        warning.className = 'warning' + action;
        warning.src = imgurl;
        remove_spinner(action);
        ($('.block_groups').find('.wrapperlistgroup').before(warning)).on('click', create_warning_message);
    };
    var checkmember = function(){
        if ($(this).data('action') == 'show') {
            $('.block_groups').find('.hiddengroups').removeClass('hiddengroups');
        }
        if ($(this).data('action') == 'hide') {
            $('.block_groups').find('.membergroup-').addClass('hiddengroups');
        }
    }
    /**
     * Method that calls for an ajax script and replaces and/or changes the output components.
     */
    var changevisibilityall = function (event) {
        // Add_spinner($(this).data('action'));

        var promises = ajax.call([
            {
                methodname: 'block_groups_create_allgroups_output', args: {
                    groups: {
                        action: $(this).data('action'),
                        courseid: event.data.courseid
                    }
                }
            }
        ]);

        promises[0].done(function (response) {
            $('.block_groups').find('.wrapperlistgroup').replaceWith(response.newelement);
            // Replaces the used element, therefore removes the spinner.
            // TODO: Change list groups does not work yet
            var exists = false;
            try { $(this).data('changedgroups'); exists = true;} catch(e) {}
            if (exists) {
                var partsOfStr = $(this).data('changedgroups').split(',');
                partsOfStr.each(checkmember());
            }
        }).fail(function () {
            // Add_warning($(this).data('action'));
            return false;
        });
        return false;
    };

    /**
     * Calls for the main method.
     */

    return {
        initialise: function(courseid){
            $('.block_groups_all_toggle').on('click', {courseid: courseid}, changevisibilityall);
        }
    };
});
