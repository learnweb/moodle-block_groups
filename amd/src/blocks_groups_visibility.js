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

define(['jquery', 'core/ajax', 'core/url', 'core/notification', 'core/str'], function($, ajax, url, notification, str) {
    /**
     * Methode to remove warnings
     * @param {(int|string)} identifier
     */
    var remove_warning = function(identifier) {
        $('.block_groups').find('.warning' + identifier).remove();
    };
    /**
     * Removes the Spinner Class of a single group.
     * @param {int} groupid that identifies to which group the spinner belongs to.
     */
    var remove_spinner = function(groupid) {
        var divgroup = $('.block_groups');
        divgroup.find('.spinner' + groupid).remove();
        divgroup.find('.imggroup-' + groupid).show();
    };
    /**
     * Removes all spinners.
     */
    var remove_spinners = function() {
        var divgroups = $('.block_groups');
        divgroups.find('.spinner-all').remove();
        divgroups.find('.imggroup').show();
    };
    /**
     * Creates a warning message.
     */
    var create_warning_message = function() {
        str.get_strings([
            {'key': 'errortitle', component: 'block_groups'},
            {'key': 'nochangeindatabasepossiblereload', component: 'block_groups'},
            {'key': 'yes'},
            {'key': 'no'}
        ]).done(function(s) {
            notification.confirm(s[0], s[1], s[2], s[3], reload_page);
        }).fail(notification.exception);
    };
    /**
     * Reloads the current page.
     */
    var reload_page = function() {
        location.reload(true);
    };
    /**
     * Initialises Spinner for a single group.
     * @param {int} groupid
     */
    var add_spinner = function(groupid) {
        var divgroups = $('.block_groups');
        if (divgroups.find('.warning' + groupid).length > 0) {
            remove_warning(groupid);
        }
        var imgurl = url.imageUrl("i/loading_small", 'moodle');
        var spinner = document.createElement("img");
        spinner.className = 'spinner' + groupid + ' spinner block-groups-spinner';
        spinner.src = imgurl;
        spinner.hidden = false;
        divgroups.find('.imggroup-' + groupid).before(spinner);
        divgroups.find('.imggroup-' + groupid).hide();
    };
    /**
     * Adds a warning(triangle with exclamation mark) in case the response is empty or the response throws an error.
     * @param {(int|string)} identifier
     */
    var add_warning = function(identifier) {
        var divgroups = $('.block_groups');
        var warningexist = divgroups.find('.warning' + identifier);
        if (warningexist.length) {
            remove_spinner(identifier);
            return false;
        } else {
            if (identifier === 'all') {
                remove_spinners();
            } else {
                remove_spinner(identifier);
            }
            var imgurl = url.imageUrl("i/warning", 'moodle');
            var warning = document.createElement("img");
            warning.className = 'warning' + identifier + ' block-groups-warning';
            warning.src = imgurl;
            create_warning_message();
            (divgroups.find('.imggroup-' + identifier).before(warning));
            divgroups.find('.imggroup-' + identifier).on('click', create_warning_message);
            divgroups.find('.warning' + identifier).on('click', create_warning_message);
        }
    };
    /**
     * Method that calls for an ajax script and replaces and/or changes the output components for a single group.
     * @param {*} event
     * @return {boolean}
     */
    var changevisibility = function(event) {
        var divgroups = $('.block_groups');

        var groupid = $(this).data('groupid');
        if (divgroups.find('.spinner' + $(this).data('groupid')).length > 0 ||
            divgroups.find('.spinner-all').length > 0) {
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
        $(document).ajaxError(function() {
            add_warning(groupid);
            return false;
        });
        promises[0].then(function(response) {
            if (response === null || response.error === true) {
                add_warning(groupid);
                return false;
            }
            divgroups.find('.group-' + response.id).replaceWith(response.newelement);
            // Replaces the used element, therefore removes the spinner.
            if (response.visibility === 0) {
                divgroups.find('.membergroup-' + response.id).removeClass('hiddengroups');
            }
            if (response.visibility === 1) {
                divgroups.find('.membergroup-' + response.id).addClass('hiddengroups');
            }
            divgroups.find('.group-' + response.id + ' .block_groups_toggle').on('click',
                {courseid: event.data.courseid}, changevisibility);
            remove_spinner(response.id);
        }).fail(function() {
            add_warning(groupid);
            return false;
        });
        return false;
    };
    var checkmember = function(response) {
        var membergroup = $('.block-groups-membergroup');
        var visibility = response.visibility;
        if (visibility === 2) {
            membergroup.removeClass('hiddengroups');
        }
        if (visibility === 1) {
            membergroup.addClass('hiddengroups');
        }
    };
    /**
     * Initialises spinners for all groups.
     */
    var add_spinners = function() {
        var divgroups = $('.block_groups');
        if (divgroups.find('.warningall').length > 0) {
            remove_warning('all');
        }
        var imgurl = url.imageUrl("i/loading_small", 'moodle');
        var spinner = document.createElement("img");
        spinner.className = 'spinner-all spinner block-groups-spinner';
        spinner.src = imgurl;
        spinner.hidden = false;
        divgroups.find('.imggroup').before(spinner);
        divgroups.find('.imggroup').hide();
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
     * @return {boolean}
     */
    var changevisibilityall = function(event) {
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
        $(document).ajaxError(function() {
            add_warning('all');
            return false;
        });
        // Response is processed.
        promises[0].then(function(response) {
            var divgroups = $('.block_groups');
            // Catch misleading responses.
            if (response === null || response.error === true || (typeof response.changedgroups !== 'undefined' &&
                response.changedgroups > 0)) {
                add_warning('all');
                return false;
            }
            var warningexist = divgroups.find('.block-groups-warning');
            if (typeof warningexist !== 'undefined' && warningexist > 0) {
                add_warning('all');
                return false;
            }
            if (response.visibility === 0) {
                // Is nearly impossible since group has to be deleted during the request, however it is
                // possible, therefore a special message is thrown.
                divgroups.find('.content').replaceWith(response.newelement);
                str.get_strings([
                    {key: 'nogroups', component: 'block_groups'}
                ]).done(function(s) {
                    add_notification('error', s[0]);
                }).fail(notification.exception);
                return false;
            }

            // Old Elements are replaced and on click event added.
            divgroups.find('.wrapperlistgroup').replaceWith(response.newelement);
            divgroups.find('.block_groups_toggle').on('click', {courseid: event.data.courseid}, changevisibility);
            checkmember(response);
            // Outputvisibility 0->nogroups 1 -> hidden 2->visible 3-> all are hidden 4-> all are visible.
            str.get_strings([
                {key: 'groupschanged', component: 'block_groups', param: 'hidden'},
                {key: 'groupschanged', component: 'block_groups', param: 'visible'},
                {key: 'allgroupsinstate', component: 'block_groups', param: 'hidden'},
                {key: 'allgroupsinstate', component: 'block_groups', param: 'visible'}
            ]).done(function(s) {
                switch (response.visibility) {
                    case 1:
                        add_notification('success', s[0]);
                        break;
                    case 2:
                        add_notification('success', s[1]);
                        break;
                    case 3:
                        add_notification('warning', s[2]);
                        break;
                    case 4:
                        add_notification('warning', s[3]);
                        break;
                    default:
                        break;
                }
            }).fail(notification.exception);
        }).fail(function() {
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
        initialise: function(courseid) {
            $('.block_groups_toggle').on('click', {courseid: courseid}, changevisibility);
            $('.block_groups_all_toggle').on('click', {courseid: courseid}, changevisibilityall);

        }
    };
});
