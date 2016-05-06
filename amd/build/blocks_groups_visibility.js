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
/**
 * @module block_overview/helloworld
 */
var CSS = {
        ACTIVITYINSTANCE : 'activityinstance',
        AVAILABILITYINFODIV : 'div.availabilityinfo',
        DIMCLASS : 'dimmed',
        DIMMEDTEXT : 'dimmed_text',
        HIDE : 'hide',
        SECTIONHIDDENCLASS : 'hidden',
        SHOW : 'editing_show',
    },
// The CSS selectors we use.
    SELECTOR = {
        ACTIVITYLI: 'li.activity',
        ACTIONAREA: '.actions',
        ACTIVITYICON : 'img.activityicon',
        CONTENTAFTERLINK : 'div.contentafterlink',
        HIDE : 'a.editing_hide',
        SHOW : 'a.'+CSS.SHOW,
        SHOWHIDE : 'a.editing_showhide'
    };
define(['jquery','core/ajax'], function($, ajax) {

    var changevisibility = function (ev) {
        var node = ev.target;
        var promises = ajax.call([
            { methodname: 'block_groups_create_output', args: {groups:{id: $(this).data('groupid'),
                courseid: $(this).data('courseid')}}}
        ]);
        //works only with ancestor
        var action = $(this).data('action');
        //completelink = node.getAncestorByClassName('block_groups_toggle');

        promises[0].done(function(response) {
            switch(action) {
                case
                'hide':
                    hidegroup(ev, node, action);
                    break;
                case
                'show':
                    hidegroup(ev, node, action);
                    break;
                default:
                    // Nothing to do here!
                    break;
            }
        }).fail(function(ex) {
            console.log('fail' , ex);
        });
        return false;
    };
    /**
     * Sets the frame for hiding groups
     * @param ev
     * @param node
     * @param action
     */
    function hidegroup (ev, node, action) {
        console.log('whats wrong with you?');
        var value = handle_resource_dim(ev, action);

        /*
         var spinner = add_spinner(element);
         send_request(data, spinner);*/
    };
    /**
     * Dims the titel of a group
     * @param activity
     * @param action
     */
    function handle_resource_dim (ev, action){
        var node = ev.target;
        var lastaction = action;
        var nextaction = (action === 'hide') ? 'show': 'hide';
        // Update button info.
        console.log(node);
                    ev.getElementsByTagName('block_toggle_groups').setAttribute('src', M.util.image_url('t/' + nextaction));
        //setAttrs({'src': M.util.image_url('t/' + nextaction)});
    };
    function add_spinner(activity) {
        var actionarea = activity.one(SELECTOR.ACTIONAREA);
        if (actionarea) {
            return M.util.add_spinner(Y, actionarea);
        }
        return null;
    };

    return {
        initialise: function(courseid){
            $('.block_groups_toggle').on('click', changevisibility);
        }
    }
});