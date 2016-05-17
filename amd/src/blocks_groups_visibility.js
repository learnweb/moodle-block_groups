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
    //'core/config'], function(config)
    /**
     * Method that calls for an ajax script and replaces and/or changes the output components.
     */
    var changevisibility = function () {
        show_spinner($(this).data('groupid'));
        var promises = ajax.call([
            { methodname: 'block_groups_create_output', args: {groups:{id: $(this).data('groupid'),
                courseid: $(this).data('courseid')}}}
        ]);
        promises[0].done(function(response) {
            var newelement = response.newelement;
            $('.group-' + response.id).replaceWith(newelement);
            if(response.visibility === 1) {
                $('.membergroup-' + response.id).removeClass('hiddengroups');
            }
            if(response.visibility === 0) {
                $('.membergroup-' + response.id).addClass('hiddengroups');
            }
            $('.group-' + response.id + ' .block_groups_toggle').on('click', changevisibility);
            hide_spinner(response.id);
        });
        return false;
    };

    /**
     * Initialises Spinner.
     */
    var add_spinner = function (id) {
        var imgurl = url.imageUrl("i/loading_small",'moodle');
        // Check if spinner is already there
        if ($('.group-' + id).children('spinner')) {
            //return $('.group-' + id).children('spinner');
        }
        var spinner = document.createElement("img");
        spinner.className = 'spinner';
        spinner.src = imgurl;
        spinner.hidden = false;

        $('.group-' + id).append(spinner);
        return false;
    };
    /**
     * Shows the Spinner.
     */
    var show_spinner = function(id){
        add_spinner(id);
    };

    /**
     * Hides the Spinner.
     */
    var hide_spinner = function(id){
        $('.group-' + id).children('.spinner').hidden = true;
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