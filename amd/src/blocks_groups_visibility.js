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

define(['jquery','core/ajax'], function($, ajax) {

    /**
     * Method that calls for an ajax script and replaces and/or changes the output components.
     */
    var changevisibility = function () {
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

        });
        return false;
    };
    /**
     * Initialises Spinner.
     */
    var add_spinner = function () {
        var WAITICON = {'pix':"i/loading_small",'component':'moodle'};
        // TODO check which variable the spinner should be stick on.
        // Check if spinner is already there
        if ($('.block_groups_toggle').one('.spinner')) {
            return $('.block_groups_toggle').one('.spinner');
        }

        var spinner = $('.block_groups_toggle').create('<img />')
            .setAttribute('src', $('.block_groups_toggle').util.image_url(WAITICON.pix, WAITICON.component))
            .addClass('spinner')
            .addClass('iconsmall')
            .hide();

        $('.block_groups_toggle').append(spinner);
        return spinner;
    };
    /**
     * Manages the Spinner.
     */
    var handle_spinner = function () {
        var url = 0,
            config = 0;
        // TODO replace with j query method.
        var transaction = Y.io._map['io:0'] || new IO();
        return transaction.send.apply(transaction, [url, config]);
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