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
define(['jquery','core/ajax'], function($, ajax) {
    return {
        initialise: function(courseid){
            $('.block_groups_toggle').on('click', this.changevisibility);
        },
        changevisibility: function (event) {
            console.log('hallo');
            var promises = ajax.call([
                { methodname: 'block_groups_create_output', args: {groups:[{id: 1, courseid: 5}]}}
            ]);
            promises[0].done(function(response) {
                    console.log('ausgabe des webservice' + response);
                }).fail(function(ex) {
                console.log('fail' , ex);
                });
            return false;
        }
    }
});