@block @block_groups @groups_hide @javascript
Feature: Hide a group in a group block
  In order to hide groups for students
  As a user
  In need to hide groups

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
      | Group 1   | C1 | G1   |
      | Group 2   | C1 | G2   |
      | Group 3   | C1 | G3   |
    And the following "groupings" exist:
      | name | course | idnumber |
      | Grouping 1 | C1 | GG1 |
      | Grouping 2 | C1 | GG2 |
    And the following "group members" exist:
      | user     | group   |
      | student1 | G1 |
      | student2 | G1 |
      | student2 | G2 |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | G1    |
      | GG2      | G2    |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "groups" block
    And I log out

  Scenario: The modify link leads to the modify group page
    Given I log in as "teacher1"
    And I follow "Course 1"
    When I click on "modify groups" "link" in the "Groups and Groupings" "block"
    Then I should see "Group 2" in the "#groupeditform" "css_element"
    Then I should see "Group 1" in the "#groupeditform" "css_element"
    Then I should see "Group 3" in the "#groupeditform" "css_element"

  Scenario: Students do not see block in groups when he is not member of a visible group
    Given I log in as "student1"
    And I follow "Course 1"
    Then "block_groups" "block" should not exist

  Scenario: Students do not see group when it is hidden again
    Given I log in as "teacher1"
    And I follow "Course 1"
    When I click on the "Groups" block groups label
    And I click on the eye icon of group name "Group 1"
    Then "Groups and Groupings" "block" should exist
    Then I should see "Group 1" in the "Groups and Groupings" "block"
    Given I am on homepage
    When I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then I should see "Group 1" in the "Groups and Groupings" "block"
    Given I am on homepage
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    When I click on the "Groups" block groups label
    And I click on the eye icon of group name "Group 1"
    And I click on the eye icon of group name "Group 2"
    Then I should see "Group 1" in the "Groups and Groupings" "block"
    Given I am on homepage
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then "block_groups" "block" should not exist
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    Then I should not see "Group 1" in the "Groups and Groupings" "block"
    Then I should see "Group 2" in the "Groups and Groupings" "block"
    And I log out



