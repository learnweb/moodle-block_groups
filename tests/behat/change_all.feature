@block @block_groups @groups_change_all
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
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Groups and Groupings" block
    And I log out

  Scenario: Change all groups show does show all groups
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I click in the groups block on all groups "show"
    Then I wait "3" seconds
    Then "Groups and Groupings" "block" should exist
    Then I should see "Group 1" in the "Groups and Groupings" "block"
    Then I should see "All groups are visible." in the "region-main" "region"
    Given I am on homepage
    When I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Group 1" in the "Groups" "block"
    Given I am on homepage
    When I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    Then I should see "Group 1" in the "Groups" "block"
    And I am on "Course 1" course homepage
    Then I should see "Group 2" in the "Groups" "block"
    Given I am on homepage
    And I log out

  Scenario: Change all groups hide does hide all groups
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I click in the groups block on all groups "show"
    And I am on "Course 1" course homepage
    When I click in the groups block on all groups "hide"
    Then I should see "All groups are hidden." in the "region-main" "region"
    And I am on "Course 1" course homepage
    And I click on the eye icon of group name "Group 2"
    Then "Groups and Groupings" "block" should exist
    Given I am on homepage
    When I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    Then I should not see "Group 1" in the "Groups" "block"
    Given I am on homepage
    And I log out