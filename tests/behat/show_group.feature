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
      | student4 | Student | 4 | student4@example.com | S4 |
      | student5 | Student | 5 | student5@example.com | S5 |
      | student6 | Student | 6 | student6@example.com | S6 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
      | student5 | C1 | student |
      | student6 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1   | C1 | G1   |
      | Group 1.0 | C1 | G1.0 |
      | Group 2   | C1 | G2   |
      | Group 3   | C1 | G3   |
      | Group 4   | C1 | G4   |
      | Group 5   | C1 | G5   |
    And the following "groupings" exist:
      | name | course | idnumber |
      | Grouping 1 | C1 | GG1 |
      | Grouping 2 | C1 | GG2 |
      | Grouping 3 | C1 | GG3 |
    And the following "group members" exist:
      | user     | group   |
      | student1 | G1 |
      | student2 | G1 |
      | student3 | G2 |
      | student4 | G2 |
      | student5 | G3 |
      | student6 | G3 |
      | teacher1  | G1 |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | G1    |
      | GG1      | G1.0  |
      | GG2      | G2    |
      | GG3      | G3    |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "groups" block
    And I log out

  Scenario: Teacher View
      Given I log in as "teacher1"
      And I follow "Course 1"
      And I click on "Groups " "css_element" in the "Groups and Groupings" "block"
      Then I should see "Group 2" in the "block_groups" "block"
      Then I should see "Group 1" in the "block_groups" "block"


