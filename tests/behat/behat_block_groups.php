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
 * Block Groups functionalities for behat-testing.
 *
 * @package   block_groups
 * @copyright 2016 N. Herrmann
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.


require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Block Groups functionalities for behat-testing.
 *
 * @package    block_groups
 * @category   test
 * @copyright  2016 N.Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_block_groups extends behat_base {
    /**
     * Enlarge hidden checkboxes
     *
     * @When /^I click on the "(?P<labelname_string>(?:[^"]|\\")*)" block groups label$/
     *
     * @param string $labelname identifier of the checkbox
     */
    public function i_click_on_the_block_groups_label($labelname) {
        $label = $this->find('xpath' , "//div[@class='wrapperblockgroupsandgroupingcheckbox']['checkboxgroup']//
                                following-sibling::label[contains(.,'" .$labelname. "')]");
        $label->click();
    }

    /**
     * Clicks on eye icon
     *
     * @When /^I click on the eye icon of group name "(?P<groupname_string>(?:[^"]|\\")*)"$/
     *
     * @param string $groupname identifier of the Group
     */
    public function i_click_on_the_eye_icon_of_group_name($groupname) {
        $eyeicon = $this->find('xpath' ,  "//div[@class='wrapperblockgroupsandgroupingcheckbox'][2]
              //li[contains(.,'". $groupname . "')]//child::a");
        $eyeicon->click();
    }
    /**
     * Clicks on all groups
     *
     * @When /^I click in the groups block on all groups "(?P<action_string>(?:[^"]|\\")*)"$/
     *
     * @param string $action identifier of the Group
     */
    public function i_click_in_the_groups_block_on_all_groups($action) {
        if ($action == 'hide') {
            $eyeicon = $this->find('xpath' ,
                "//span[@class='wrapperblockgroupsallgroups']//following::a[1]");
        }
        if ($action == 'show') {
            $eyeicon = $this->find('xpath' ,
                "//span[@class='wrapperblockgroupsallgroups']//following::a[2]");
        }
        $eyeicon->click();
    }
}