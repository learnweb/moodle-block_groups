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
        ACTIONAREA: '.actions',
        ACTIVITYICON : 'img.activityicon',
        CONTENTAFTERLINK : 'div.contentafterlink',
        HIDE : 'a.editing_hide',
        SHOW : 'a.'+CSS.SHOW,
        SHOWHIDE : 'a.editing_showhide'
    };
define(['jquery','core/ajax'], function($, ajax) {
    return {
        initialise: function(courseid){
            $('.block_groups_toggle').on('click', this.changevisibility);
        },
        changevisibility: function (event) {
            var node = ev.target;
            var promises = ajax.call([
                { methodname: 'block_groups_create_output', args: {groups:{id: $(this).data('groupid'),
                    courseid: $(this).data('courseid')}}}
            ]);
            promises[0].done(function(response) {
                var action = node.getData('action');
                switch(action) {
                case
                    'hide': this.hidestatement(ev, node, action);
                        break;
                case
                    'show': this.showstatement();
                        break;
                default:
                    // Nothing to do here!
                    break;
                }
                }).fail(function(ex) {
                console.log('fail' , ex);
                });
            return false;
        },
        hidestatement: function(ev, node,  action){
            var element = activity;
            var value = this.handle_resource_dim(button, activity, action);

            // Send the request
            var data = {
                'class': 'resource',
                'field': 'visible',
                'value': value,
                'id': Y.Moodle.core_course.util.cm.getId(element)
            };
            var spinner = this.add_spinner(element);
            this.send_request(data, spinner);

            return this;
        },
        showstatement: function(){

        },
        add_spinner: function(activity) {
            var actionarea = activity.one(SELECTOR.ACTIONAREA);
            if (actionarea) {
                return M.util.add_spinner(Y, actionarea);
            }
            return null;
        }
    }
});