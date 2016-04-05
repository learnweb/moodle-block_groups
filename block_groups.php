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
    //public $content_type = BLOCK_TYPE_TREE;
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

        global $COURSE;

        if($this->content !== null){
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';

        $coursecontext = context_course::instance($COURSE->id);
        $access = has_capability('moodle/course:managegroups',  $coursecontext);

        if($access === TRUE){
            $this->content->text .= $this->get_content_teaching();
        }

        $this->content->text .= $this->get_content_groupmembers();
        return $this->content;
    }


    /**
     *
     * Returns a List of all existing groups and groupings
     *
     * @return string
     */
    private function get_content_teaching(){
        global  $COURSE, $CFG, $OUTPUT;
        $grouparray = array();
        $groupingarray = array();
        $allgroups = groups_get_all_groups($COURSE->id);
        $allgroupings = groups_get_all_groupings($COURSE->id);
        $groupstext = '';

        foreach ($allgroups as $g => $value) {
            if (is_object($value) && property_exists($value, 'name')) {
                $a =count(groups_get_members($value->id));
                $grouparray[$g] = $value->name . get_string('brackets','block_groups', $a);
            }
        }
        foreach ($allgroupings as $g => $value) {
            if (is_object($value) && property_exists($value, 'name')) {
                $a =count(groups_get_grouping_members($value->id));
                $groupingarray[$g] = $value->name  . get_string('brackets','block_groups', $a) ;
            }
        }

        if(count($grouparray) == 0) {
            $groupstext = '';
            return $groupstext;
        }
        else{
            $contentcheckbox ='';
            if(!(empty($groupingarray)) ){
                // select_option icon aendern t/up
                $contentgrouping = html_writer::tag('label',get_string('groupings','block_groups'),array('for'=>"checkboxgrouping"));
                $contentgrouping .= html_writer::alist($groupingarray);
                $contentgrouping2 = html_writer::tag('input', $contentgrouping, array('type'=>"checkbox",'value'=>"1", 'id'=>"checkboxgrouping", 'name'=>"checkboxgrouping"));
                $contentcheckbox .= html_writer::tag('div', $contentgrouping2, array('class' => "checkboxgrouping"));

            }

            $contentgroups = html_writer::tag('label',get_string('groups','block_groups'),array('for'=>"checkboxgroup") );
            $contentgroups .= html_writer::alist($grouparray);
            $contentgroups2 = html_writer::tag('input', $contentgroups, array('type'=>"checkbox",'value'=>"1", 'id'=>"checkboxgroup", 'name'=>"checkboxgroup"));
            $contentcheckbox .= html_writer::tag('div', $contentgroups2, array('class'=>"checkboxgroup"));
            $groupstext .= html_writer::tag('div', $contentcheckbox,array('class'=>'checkbox'));
            $courseshown = $this->page->course->id;
            $groupstext .= '<a href="' . $CFG->wwwroot . '/group/index.php?id=' . $courseshown . '">'. get_string('modify', 'block_groups'). '</a></br>';

            return $groupstext;
        }
    }


    /**
     *
     * Returns all groups where the current user has a valid membership.
     *
     * @return string
     */

    private function get_content_groupmembers(){
        global  $COURSE;

        $memberarray = array();
        $allgroups = groups_get_my_groups();

        foreach ($allgroups as $allgroupnr => $valueall) {
                if ($valueall->courseid == $COURSE->id) {
                    $memberarray[] = $valueall->name;
                }
        }

        if (empty($memberarray)) {
            $groupstext ='';
            return $groupstext;
        }
        $membercontent = get_string('member', 'block_groups');
        $membercontent .= html_writer::alist($memberarray);
        $groupstext = html_writer::tag('div', $membercontent,array('class'=>'memberlist'));
        return $groupstext;
    }

}