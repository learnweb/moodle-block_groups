@block @block_groups @groups_default_settings
Feature: Group block default visibility settings
  In order to test that group visibility settings work properly
  As a teacher
  I need to verify that groups are shown to students according to the default visibility setting

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Groups and Groupings" block
    And I log out

  @javascript
  Scenario: Default setting is 0 - student should not see hidden group
    Given the group default visibility setting is "0"
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then "Groups" "block" should not exist

  @javascript
  Scenario: Default setting is 1 - student should directly see group
    Given the group default visibility setting is "1"
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then "Groups" "block" should exist
    And I should see "Group 1" in the "Groups" "block"
