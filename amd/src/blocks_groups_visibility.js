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
 * @copyright  2016/17 N Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/url', 'core/notification', 'core/str'], function($, ajax, url, notification, str) {
    /**
     * Methode to remove warnings
     * @param {(Number|String)} identifier
     */
    var removeWarning = function(identifier) {
        $('.block_groups').find('.warning' + identifier).remove();
    };
    /**
     * Removes the Spinner Class of a single group.
     * @param {Number} groupid that identifies to which group the spinner belongs to.
     */
    var removeSpinner = function(groupid) {
        var divgroup = $('.block_groups');
        divgroup.find('.spinner' + groupid).remove();
        divgroup.find('.imggroup-' + groupid).show();
    };
    /**
     * Removes all spinners.
     */
    var removeSpinners = function() {
        var divgroups = $('.block_groups');
        divgroups.find('.spinner-all').remove();
        divgroups.find('.imggroup').show();
    };
    /**
     * Reloads the current page.
     */
    var reloadPage = function() {
        location.reload(true);
    };
    /**
     * Creates a warning message.
     */
    var createWarningMessage = function() {

        str.get_strings([
            {'key': 'errortitle', component: 'block_groups'},
            {'key': 'nochangeindatabasepossiblereload', component: 'block_groups'},
            {'key': 'yes'},
            {'key': 'no'}
        ]).done(function(s) {
            notification.confirm(s[0], s[1], s[2], s[3], reloadPage);
        }).fail(notification.exception);
    };
    /**
     * Initialises Spinner for a single group.
     * @param {Number} groupid
     */
    var addSpinner = function(groupid) {
        var divgroups = $('.block_groups');
        if (divgroups.find('.warning' + groupid).length > 0) {

            removeWarning(groupid);
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
     * @param {(Number|String)} identifier
     * @returns {Boolean} false
     */
    var addWarning = function(identifier) {
        var divgroups = $('.block_groups');
        var warningexist = divgroups.find('.warning' + identifier);
        if (warningexist.length) {
            removeSpinner(identifier);
            return false;
        } else {
            if (identifier === 'all') {
                removeSpinners();
            } else {
                removeSpinner(identifier);
            }
            var imgurl = url.imageUrl("i/warning", 'moodle');
            var warning = document.createElement("img");
            warning.className = 'warning' + identifier + ' block-groups-warning';
            warning.src = imgurl;
            createWarningMessage();
            (divgroups.find('.imggroup-' + identifier).before(warning));
            divgroups.find('.imggroup-' + identifier).on('click', createWarningMessage);
            divgroups.find('.warning' + identifier).on('click', createWarningMessage);
        }
        return false;
    };
    /**
     * Method that calls for an ajax script and replaces and/or changes the output components for a single group.
     * @param {*} event
     * @return {Boolean}
     */
    var changevisibility = function(event) {
        var divgroups = $('.block_groups');

        var groupid = $(this).data('groupid');
        if (divgroups.find('.spinner' + $(this).data('groupid')).length > 0 ||
            divgroups.find('.spinner-all').length > 0) {
            return false;
        }
        addSpinner($(this).data('groupid'));
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
            addWarning(groupid);
            return false;
        });

        promises[0].then(function(response) {
            if (response === null || response.error === true) {
                addWarning(groupid);
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
            removeSpinner(response.id);
            return false;
        }).fail(function() {
            addWarning(groupid);
            return false;
        });
        return false;
    };
    var checkMember = function(response) {
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
    var addSpinners = function() {
        var divgroups = $('.block_groups');
        if (divgroups.find('.warningall').length > 0) {
            removeWarning('all');
        }
        var imgurl = url.imageUrl("i/loading_small", 'moodle');
        var spinner = document.createElement("img");
        spinner.className = 'spinner-all spinner block-groups-spinner';
        spinner.src = imgurl;
        spinner.hidden = false;
        divgroups.find('.imggroup').before(spinner);
        divgroups.find('.imggroup').hide();
    };

    var addNotification = function(type, text) {
        notification.addNotification({
            message: text,
            type: type
        });
    };
    /**
     * Method that calls for an ajax script and replaces and/or changes the output components for all groups.
     * @param {Number} event
     * @return {Boolean}
     */
    var changeVisibilityAll = function(event) {
        if ($('.block_groups').find('.spinner').length > 0) {
            return false;
        }
        var warningexist = $('.block_groups').find('.block-groups-warning');
        if (typeof warningexist !== 'undefined' && warningexist.length > 0) {
            addWarning('all');
            return false;
        }
        addSpinners();
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
            addWarning('all');
            return false;
        });
        // Response is processed.
        promises[0].then(function(response) {
            var divgroups = $('.block_groups');
            // Catch misleading responses.
            if (response === null || response.error === true) {
                addWarning('all');
                return false;
            }
            if (response.visibility === 0) {
                // Is nearly impossible since group has to be deleted during the request, however it is
                // possible, therefore a special message is thrown.
                divgroups.find('.content').replaceWith(response.newelement);
                str.get_strings([
                    {key: 'nogroups', component: 'block_groups'}
                ]).done(function(s) {
                    addNotification('error', s[0]);
                }).fail(notification.exception);
                return false;
            }

            // Old Elements are replaced and on click event added.
            divgroups.find('.wrapperlistgroup').replaceWith(response.newelement);
            divgroups.find('.block_groups_toggle').on('click', {courseid: event.data.courseid}, changevisibility);
            checkMember(response);
            // Outputvisibility 0->nogroups 1 -> hidden 2->visible 3-> all are hidden 4-> all are visible.
            str.get_strings([
                {key: 'groupschangedhidden', component: 'block_groups'},
                {key: 'groupschangedvisible', component: 'block_groups'},
                {key: 'allgroupsinstatehidden', component: 'block_groups'},
                {key: 'allgroupsinstatevisible', component: 'block_groups'}
            ]).done(function(s) {
                switch (response.visibility) {
                    case 1:
                        addNotification('success', s[0]);
                        break;
                    case 2:
                        addNotification('success', s[1]);
                        break;
                    case 3:
                        addNotification('warning', s[2]);
                        break;
                    case 4:
                        addNotification('warning', s[3]);
                        break;
                    default:
                        break;
                }
            }).fail(notification.exception);
            return false;
        }).fail(function() {
            addWarning('all');
            return false;
        });
        removeSpinners();
        return false;
    };

    /**
     * Calls for the main method. Either single groups are changed with block_groups_toggle or all groups with
     * block_groups_all_toggle.
     * @param {Number} courseid
     */
    return {
        initialise: function(courseid) {
            $('.block_groups_toggle').on('click', {courseid: courseid}, changevisibility);
            $('.block_groups_all_toggle').on('click', {courseid: courseid}, changeVisibilityAll);

        }
    };
});
