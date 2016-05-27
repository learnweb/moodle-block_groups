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

define(['jquery','core/ajax','core/url'], function($, ajax, url) {
    /**
     * Method that calls for an ajax script and replaces and/or changes the output components.
     */
    var changevisibility = function () {
        add_spinner($(this).data('groupid'));
        var promises = ajax.call([
            { methodname: 'block_groups_create_output', args: {groups:{id: $(this).data('groupid'),
                courseid: $(this).data('courseid')}}}
        ]);
        promises[0].done(function(response) {
            var newelement = response.newelement;
            $('.group-' + response.id).replaceWith(newelement);
            // Replaces the used element, therefore removes the spinner.
            if(response.visibility === 1) {
                $('.membergroup-' + response.id).removeClass('hiddengroups');
            }
            if(response.visibility === 0) {
                $('.membergroup-' + response.id).addClass('hiddengroups');
            }
            $('.group-' + response.id + ' .block_groups_toggle').on('click', changevisibility);
            // In the unlikely case that javascript works very slow
            remove_spinner(response.id);
        });
        return false;
    };

    /**
     * Initialises Spinner.
     * @param id int that identifies the group id
     */
    var add_spinner = function (id) {
        var imgurl = url.imageUrl("i/loading_small",'moodle');
        var spinner = document.createElement("img");
        spinner.className = 'spinner' + id;
        spinner.src = imgurl;
        spinner.hidden = false;
        $('.imggroup-' + id).before(spinner);
        return false;
    };

    /**
     * Removes the Spinner Class
     * @param id int that identifies to which group the spinner belongs to.
     */
    var remove_spinner = function (id) {
        if($('.spinner' + id ).length > 0){
            $('.imggroup-' + id + 'img:first-child').remove();
        }
    };
    /**
     * Calls for the main method.
     */
    return {
        initialise: function(){
            $('.block_groups_toggle').on('click', changevisibility);

        }
    };
});