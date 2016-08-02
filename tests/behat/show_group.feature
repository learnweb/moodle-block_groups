@block @block_groups @groups_show @javascript
Feature: Make a group visible in a group block
  In order to let students see a group
  As a user
  In need to make groups visible

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
      | student2 | Student | 2 | student2@example.com | S2 |
      | student3 | Student | 3 | student3@example.com | S3 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1   | C1 | 1   |
      | Group 2   | C1 | 2   |
    And the following "groupings" exist:
      | name | course | idnumber |
      | Grouping 1 | C1 | GG1 |
      | Grouping 2 | C1 | GG2 |
    And the following "group members" exist:
      | user     | group   |
      | student1 | 1 |
      | student2 | 1 |
      | student3 | 2 |
      | teacher1  | 1 |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | 1    |
      | GG2      | 2    |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "groups" block
    And I log out


  Scenario: Teacher sees a list of all groups and groupings when they click on the label
      Given I log in as "teacher1"
      And I follow "Course 1"
      When I click on "//div[@class='wrapperblockgroupsandgroupingcheckbox']['checkboxgroup']//following-sibling::label[contains(.,'Groups')]" "xpath_element"
      And I click on the "grouping" block groups label
      Then I should see "Group 2" in the "block_groups" "block"
      Then I should see "Group 1" in the "block_groups" "block"
      Then I should see "Grouping 2" in the "block_groups" "block"
      Then I should see "Grouping 1" in the "block_groups" "block"

  Scenario: Click on eye icon, only enrolled students are able to see the block
      Given I log in as "teacher1"
      And I follow "Course 1"
      When I click on "//div[@class='wrapperblockgroupsandgroupingcheckbox']['checkboxgroup']//following-sibling::label[contains(.,'Groups')]" "xpath_element"
      And I click on ".rightalign" "css_element" in the "//div[@class='wrapperblockgroupsandgroupingcheckbox'][2]/ul/li[contains(.,'Group 1')]" "xpath_element"
      And I log out
      And I log in as "student1"
      And I follow "Course 1"
      Then I should see "Group 1" in the "block_groups" "block"
      Then I should not see "Group 2" in the "block_groups" "block"
      And I log out
      And I log in as "student3"
      And I follow "Course 1"
      Then "block_groups" "block" should not exist





