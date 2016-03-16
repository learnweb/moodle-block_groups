<?php
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
 * Created by IntelliJ IDEA.
 * User: nina
 * Date: 16.03.16
 * Time: 09:23
 */
class block_groups extends block_base
{

    /**Initialises the block*/
    public function init(){
        $this->title = get_string('pluginname','block_groups');
    }
    /** Returns the content object
     *
     * @return stdObject
     */

    public function get_content()
    {
        global $USER, $DB, $COURSE;
        if($this->content !== null){
            return $this ->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        /*$this->content->footer = html_writer::link(new moodle_url('/group.index.php', array('id' => $COURSE->id)),
            'Gruppe ');*/
        $this->content->groups = '';

        $allgroups = groups_get_all_groups($COURSE->id);

        if(empty($allgroups)){
            //studierenden nicht anzeigen
            $this->content->text =get_string('groups:nogroups','block_groups');
        }
        if(!empty($allgroups)){
            //
            $this->content->text =get_string('groups:introduction','block_groups');
            $groups = groups_get_user_groups($COURSE->id, $USER->id);
            foreach($groups as $g => $value){


            }

        }
        return $this->content;
    }


}