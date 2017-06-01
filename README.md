# moodle-block_groups
[![Build Status](https://travis-ci.org/learnweb/moodle-block_groups.svg?branch=master)](https://travis-ci.org/learnweb/moodle-block_groups)
[![codecov](https://codecov.io/gh/learnweb/moodle-block_groups/branch/master/graph/badge.svg)](https://codecov.io/gh/learnweb/moodle-block_groups)


A Moodle block to display groups and groupings to users. The plugin differentiates between the capability rights of users
to evaluate the appropriate amount of information to be displayed.

This plugin is written by [Jan Dageförde](https://github.com/Dagefoerde), [Tobias Reischmann](https://github.com/tobiasreischmann) and [Nina Herrmann](https://github.com/NinaHerrmann).



## Installation
This plugin should go into `blocks/groups`. Moodle plugin directory link is https://moodle.org/plugins/block_groups.

## Screenshots

### Teachers' view
In initial state value groups and groupings are not listed. </br> </br>
<img src="https://cloud.githubusercontent.com/assets/18289780/26582335/142e18d6-4541-11e7-86c8-e4423c55951d.png" width="500"></br> </br>

The block displays all existing groups and groupings as well as all enrolled groups on request.
The number inside the brackets displays the number of members in a group or grouping.
Additionally, groups can be hidden, illustrated by an eye icon and a change in opacity.
When the icon is clicked the visibility of groups changes. Moreover, all groups can be changed 
with one click.
Javascript files exist to update the block.
When the block is installed for the first time all groups are hidden. The following picture shows a course with 4 groups. 
Group 1 and Group 2.1 are hidden. The other groups are visible. </br> </br>
<img src="https://cloud.githubusercontent.com/assets/18289780/26582345/195400aa-4541-11e7-9d25-184ee8cbcc7d.png" width="500"></br> </br>

In case the required change is not possible a warning message is displayed and the affected group is marked with a triangle. </br></br>
<img src="https://cloud.githubusercontent.com/assets/18289780/26582340/162f7b84-4541-11e7-9a6a-36949f1b5edd.png" width="254">
<img src="https://cloud.githubusercontent.com/assets/18289780/26668032/276cea4a-46a8-11e7-8232-ca2fd62ea52d.png" width="320"></br></br>

### Students' view
In the current state of the block groupings are not displayed to the students. </br>
Students are only able to view the groups they are enrolled in. In case they are not enrolled in any visible group, no block is displayed. </br></br>
<img src="https://cloud.githubusercontent.com/assets/18289780/26583405/63beab3c-4545-11e7-941b-db39feebb205.png" width="500"></br>