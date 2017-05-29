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

define(['jquery', 'core/ajax', 'core/url', 'core/notification', 'core/str'], function($, ajax, url, notification) {
    /**
     * Methode to remove warnings
     * @param {int} identifier
     */
    var remove_warning = function(identifier){
        $('.block_groups').find('.warning' + identifier).remove();
    };
    /**
     * Removes the Spinner Class of a single group.
     * @param {int} groupid that identifies to which group the spinner belongs to.
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
     * Initialises Spinner for a single group.
     * @param {int} groupid
     */
    var add_spinner = function (groupid) {
        if($('.block_groups').find('.warning' + groupid).length > 0){
            remove_warning(groupid);
        }
        var imgurl = url.imageUrl("i/loading_small",'moodle');
        var spinner = document.createElement("img");
        spinner.className = 'spinner' + groupid + ' spinner';
        spinner.src = imgurl;
        spinner.hidden = false;
        $('.block_groups').find('.imggroup-' + groupid).before(spinner);
    };
    /**
     * Adds a warning(triangle with exclamation mark) in case the response is empty or the response throws an error.
     * @param {int} identifier
     */
    var add_warning = function (identifier){
        if($('.block_groups').find('.warning' + identifier).length > 0){
            remove_spinner(identifier);
            create_warning_message();
            return false;
        }
        var imgurl = url.imageUrl("i/warning", 'moodle');
        var warning = document.createElement("img");
        warning.className = 'warning' + identifier;
        warning.src = imgurl;
        remove_spinner(identifier);
        create_warning_message();
        ($('.block_groups').find('.imggroup-' + identifier).before(warning)).on('click', create_warning_message);
    };
    /**
     * Method that calls for an ajax script and replaces and/or changes the output components for a single group.
     */
    var changevisibility = function (event) {
        var groupid = $(this).data('groupid');
        if ($('.block_groups').find('.spinner' + $(this).data('groupid')).length > 0 ||
            $('.block_groups').find('.spinner-all').length > 0) {
            return false;
        }
        add_spinner($(this).data('groupid'));
        var promises = ajax.call([
            {
                methodname: 'block_groups_create_output', args: {
                    groups: {
                        id: $(this).data('groupid'),
                        courseid: event.data.courseid
                    }
                }
            }
        ]);
        $(document).ajaxError(function () {
            add_warning(groupid);
            return false;
        });
        promises[0].done(function (response) {
            if (response === null) {
                add_warning(groupid);
                return false;
            }
            if (response.error === true) {
                add_warning(groupid);
                return false;
            }
            $('.block_groups').find('.group-' + response.id).replaceWith(response.newelement);
            // Replaces the used element, therefore removes the spinner.
            if (response.visibility === 0) {
                $('.block_groups').find('.membergroup-' + response.id).removeClass('hiddengroups');
            }
            if (response.visibility === 1) {
                $('.block_groups').find('.membergroup-' + response.id).addClass('hiddengroups');
            }
            $('.block_groups').find('.group-' + response.id + ' .block_groups_toggle').on('click',
                {courseid: event.data.courseid}, changevisibility);
            remove_spinner(response.id);
        }).fail(function () {
            add_warning(groupid);
            return false;
        });
        return false;
    };
    var checkmember = function(response){
        var visibility = response.visibility;
        if (visibility === 2) {
            $('.block-groups-membergroup').removeClass('hiddengroups');
        }
        if (visibility === 1) {
            $('.block-groups-membergroup').addClass('hiddengroups');
        }
    };
    /**
     * Initialises spinners for all groups.
     */
    var add_spinners = function () {
        if($('.block_groups').find('.warningall').length > 0){
            remove_warning('all');
        }
        var imgurl = url.imageUrl("i/loading_small", 'moodle');
        var spinner = document.createElement("img");
        spinner.className = 'spinner-all spinner';
        spinner.src = imgurl;
        spinner.hidden = false;
        $('.block_groups').find('.imggroup').before(spinner);
    };
    /**
     * Removes all spinners.
     */
    var remove_spinners = function() {
        $('.block_groups').find('.spinner-all').remove();
    };

    var add_notification = function(type, text) {
        notification.addNotification({
            message: text,
            type: type
        });
    };
    /**
     * Method that calls for an ajax script and replaces and/or changes the output components for all groups.
     * @param {int} event
     */
    var changevisibilityall = function (event) {
        if ($('.block_groups').find('.spinner').length > 0) {
            return false;
        }
        add_spinners();
        // Calls for the externallib.
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
        $(document).ajaxError(function () {
            add_warning('all');
            return false;
        });
        // Response is processed.
        promises[0].done(function(response) {
            // Catch misleading responses.
            if (response === null) {
                add_warning('all');
                return false;
            }
            if (response.error === true) {
                add_warning('all');
                return false;
            }
            if(response.visibility === 0 ){
                // Is nearly impossible since group has to be deleted during the request, however it is
                // possible, therefore a special message is thrown.
                $('.block_groups').find('.content').replaceWith(response.newelement);
                add_notification('error', M.util.get_string('nogroups', 'block_groups'));
                return false;
            }
            // Old Elements are replaced and on click event added.
            $('.block_groups').find('.wrapperlistgroup').replaceWith(response.newelement);
            $('.block_groups').find('.block_groups_toggle').on('click', {courseid: event.data.courseid}, changevisibility);
            checkmember(response);
            // Outputvisibility 0->nogroups 1 -> hidden 2->visible 3-> all are hidden 4-> all are visible.
            switch (response.visibility) {
                case 1:
                    add_notification('success', M.util.get_string('groupschanged', 'block_groups', 'hidden'));
                    break;
                case 2:
                    add_notification('success', M.util.get_string('groupschanged', 'block_groups', 'visible'));
                    break;
                case 3:
                    add_notification('warning', M.util.get_string('allgroupsinstate', 'block_groups', 'hidden'));
                    break;
                case 4:
                    add_notification('warning', M.util.get_string('allgroupsinstate', 'block_groups', 'visible'));
                    break;
                default:
                    break;
            }
        }).fail(function () {
            add_warning('all');
            return false;
        });
        remove_spinners();
        return false;
    };

    /**
     * Calls for the main method. Either single groups are changed with block_groups_toggle or all groups with
     * block_groups_all_toggle.
     * @param {int} courseid
     */
    return {
        initialise: function(courseid){
            $('.block_groups_toggle').on('click', {courseid: courseid}, changevisibility);
            $('.block_groups_all_toggle').on('click', {courseid: courseid}, changevisibilityall);

        }
    };
});
