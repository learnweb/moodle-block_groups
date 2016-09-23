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

define(['jquery','core/ajax','core/url','core/notification'], function($, ajax, url, notification) {
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
    var add_spinner = function (groupid) {
        if($('.block_groups').find('.warning' + groupid).length > 0){
            remove_warning(groupid);
        }
        var imgurl = url.imageUrl("i/loading_small",'moodle');
        var spinner = document.createElement("img");
        spinner.className = 'spinner' + groupid;
        spinner.src = imgurl;
        spinner.hidden = false;
        $('.block_groups').find('.imggroup-' + groupid).before(spinner);
    };
    /**
     * Adds a warning(triangle with exclamation mark) in case the response is empty or the response throws an error.
     * @param int $groupid
     */
    var add_warning = function (groupid){
        if($('.block_groups').find('.warning' + groupid).length > 0){
            remove_spinner(groupid);
            create_warning_message();
            return false;
        }
        var imgurl = url.imageUrl("i/warning",'moodle');
        var warning = document.createElement("img");
        warning.className = 'warning' + groupid;
        warning.src = imgurl;
        remove_spinner(groupid);
        ($('.block_groups').find('.imggroup-' + groupid).before(warning)).on('click', create_warning_message);
    };
    /**
     * Method that calls for an ajax script and replaces and/or changes the output components.
     */
    var changevisibility = function (event) {
        var groupid = $(this).data('groupid');
        if($('.block_groups').find('.spinner' + $(this).data('groupid')).length > 0){
            return false;
        }
        add_spinner($(this).data('groupid'));
        var promises = ajax.call([
            { methodname: 'block_groups_create_output', args: {
                groups: {
                    id: $(this).data('groupid'),
                    courseid: event.data.courseid
                }
                }
            }
        ]);
        $(document).ajaxError(function() {
            add_warning(groupid);
            return false;
        });
        promises[0].done(function(response) {
            if(response === null){
                add_warning(groupid);
                return false;
            }
            if(response.error === true){
                add_warning(groupid);
                return false;
            }
            $('.block_groups').find('.group-' + response.id).replaceWith(response.newelement);
            // Replaces the used element, therefore removes the spinner.
            if(response.visibility === 0) {
                $('.block_groups').find('.membergroup-' + response.id).removeClass('hiddengroups');
            }
            if(response.visibility === 1) {
                $('.block_groups').find('.membergroup-' + response.id).addClass('hiddengroups');
            }
            $('.block_groups').find('.group-' + response.id + ' .block_groups_toggle').on('click', {courseid: event.data.courseid},
                changevisibility);
            remove_spinner(response.id);
        }).fail(function () {
            add_warning(groupid);
            return false;
        });
        return false;
    };

    /**
     * Calls for the main method.
     */
    return {
        initialise: function(courseid){
            $('.block_groups_toggle').on('click', {courseid: courseid}, changevisibility);
        }
    };
});
