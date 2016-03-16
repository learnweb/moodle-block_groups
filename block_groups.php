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
            $this->content = "";
            return $this->content;
        }

        $this->content = new stdClass;

        $allgroups = groups_get_all_groups($COURSE->id);

        if(empty($allgroups)){
            //block is not shown if it is empty
            $this->content->text = '';
        }

        $coursecontext = context_course::instance($COURSE->id);
        $access = has_capability('moodle/course:managegroups',  $coursecontext);

        if($access === TRUE){

            $groupstext = "";
            foreach ($allgroups as $g => $value) {

                if (is_object($value) && property_exists($value, 'name')) {
                    $groupstext .= " " . $value->name . "</br>";
                }
            }
            $this->content->text = $groupstext;
           // $this->contetnt->text .='<a href="'.$CFG->wwwroot.'/course/resources.php?id='.$course->id.'">''</a>';
        }
        //Auch für admin zugehörige Gruppen anzeigen
        else{
            if (!empty($allgroups)) {
                //

                $groups = groups_get_user_groups($COURSE->id, $USER->id);

                if (count($groups) === 0) {
                    // block is hidden in case the user is not member of a group
                    $this->content->text = "";
                } else {
                    $this->content->text = get_string('introduction', 'block_groups') . "</br>";
                    $groupstext = "";
                    foreach ($groups as $g => $value) {
                        if (is_object($value) && property_exists($value, 'name')) {
                            $groupstext .= " " . $value->name . "</br>";
                        }
                    }

                    if ($groupstext === "") {
                        $this->content->text = "";
                    } else {
                        $this->content->text = $groupstext;
                    }
                }
            }
        }
        return $this->content;
    }
}