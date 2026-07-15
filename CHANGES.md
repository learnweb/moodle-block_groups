CHANGELOG
=========

Changes in v5.2-r1
----------------------------------

- support for Moodle 5.2
- Added controls for the groupings, so teachers can show or hide all groups that belong to one grouping with one click
- Deleted or invalid group references are now cleaned up, so the table only contains existing groups
  - For this, an observer for the \core\event\group_deleted event was introduced. When this event is observed, the group will be removed from the table

Changes in v5.0-r1 (since v4.5-r1)
----------------------------------

- support for Moodle 5.0
- add CHANGES.md for user-visible changes
