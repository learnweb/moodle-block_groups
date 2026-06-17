@block @block_groups @groups_change_grouping
Feature: Feature: Change group visibility by grouping in the groups block
  In order to change the visibility of groups in a grouping with one click
  As a course administrator
  I need to change the visibility of all groups in a grouping

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | T1       |
      | student1 | Student   | 1        | student1@example.com | S1       |
      | student2 | Student   | 2        | student2@example.com | S2       |
      | student3 | Student   | 3        | student3@example.com | S3       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "groups" exist:
      | name      | course | idnumber |
      | Group 1   | C1     | G1       |
      | Group 2   | C1     | G2       |
      | Group 3   | C1     | G3       |
      | Group 4   | C1     | G4       |
    And the following "groupings" exist:
      | name       | course | idnumber |
      | Grouping 1 | C1     | GG1      |
      | Grouping 2 | C1     | GG2      |
    And the following "group members" exist:
      | user     | group  |
      | student1 | G1     |
      | student2 | G1     |
      | student2 | G2     |
      | student3 | G3     |
      | student3 | G4     |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | G1    |
      | GG1      | G2    |
      | GG2      | G2    |
      | GG2      | G4    |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Groups and Groupings" block
    And I log out

  @javascript
  Scenario: Showing a grouping shows all groups in that grouping
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I click in the groups block on the grouping "Grouping 1" "show"
    Then I wait until the page is ready
    Then "Groups and Groupings" "block" should exist
    And I should see "All groups in this grouping are visible." in the "region-main" "region"
    And I log out

    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Group 1" in the "Groups" "block"
    And I log out

    When I log in as "student2"
    And I am on "Course 1" course homepage
    Then I should see "Group 1" in the "Groups" "block"
    And I should see "Group 2" in the "Groups" "block"
    And I log out

    When I log in as "student3"
    And I am on "Course 1" course homepage
    Then "Groups" "block" should not exist
    And I log out

  @javascript
  Scenario: Showing overlapping groupings shows shared and grouping-specific groups
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I click in the groups block on the grouping "Grouping 1" "show"
    And I click in the groups block on the grouping "Grouping 2" "show"
    Then I wait until the page is ready
    Then "Groups and Groupings" "block" should exist
    And I should see "All groups in this grouping are visible." in the "region-main" "region"
    And I log out

    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Group 1" in the "Groups" "block"
    And I log out

    When I log in as "student2"
    And I am on "Course 1" course homepage
    Then I should see "Group 1" in the "Groups" "block"
    And I should see "Group 2" in the "Groups" "block"
    And I log out

    When I log in as "student3"
    And I am on "Course 1" course homepage
    Then I should see "Group 4" in the "Groups" "block"
    And I should not see "Group 3" in the "Groups" "block"
    And I log out

  @javascript
  Scenario: Hiding a grouping hides all groups in that grouping
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I click in the groups block on the grouping "Grouping 1" "show"
    And I click in the groups block on the grouping "Grouping 2" "show"
    And I click in the groups block on the grouping "Grouping 1" "hide"
    Then I wait until the page is ready
    Then "Groups and Groupings" "block" should exist
    And I should see "All groups in this grouping are hidden." in the "region-main" "region"
    And I log out

    When I log in as "student2"
    And I am on "Course 1" course homepage
    Then "Groups" "block" should not exist
    And I log out

    When I log in as "student3"
    And I am on "Course 1" course homepage
    Then I should see "Group 4" in the "Groups" "block"
    And I should not see "Group 3" in the "Groups" "block"
    And I log out
