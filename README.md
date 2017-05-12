# moodle-block_groups *(release_candidate)* 
[![Build Status](https://travis-ci.org/learnweb/moodle-block_groups.svg?branch=master)](https://travis-ci.org/learnweb/moodle-block_groups)
[![codecov](https://codecov.io/gh/learnweb/moodle-block_groups/branch/master/graph/badge.svg)](https://codecov.io/gh/learnweb/moodle-block_groups)


A Moodle block to display groups and groupings to users. The plugin differentiates between the capability rights of users
to evaluate the appropriate amount of information to be displayed.

This plugin is written by [Jan Dageförde](https://github.com/Dagefoerde), [Tobias Reischmann](https://github.com/tobiasreischmann) and [Nina Herrmann](https://github.com/NinaHerrmann).



## Installation
This plugin should go into `blocks/groups`. Moodle plugin directory link is https://moodle.org/plugins/block_groups.

## Screenshots

### Teachers' view
In initial state value groups and groupings are not listed.</br>
![Teachers' View_hidden](https://cloud.githubusercontent.com/assets/18289780/25997177/4959b3a0-371b-11e7-84dd-42fe09a67659.png)</br>
The block displays all existing groups and groupings as well as all enrolled groups on request.
The number inside the brackets displays the number of members in a group or grouping.
Additionally groups can be hidden, illustrated by an eye icon and a change in opacity.
When the icon is clicked the visibility of groups changes. Javascript files exist to update the block.
When the block is installed for the first time all groups are hidden</br>
![Teachers' View](https://cloud.githubusercontent.com/assets/18289780/25997181/4e3f328c-371b-11e7-9de7-00fde7a99885.png)</br>
In case the required change is not possible a warning message is displayed and the affected group is marked with a triangle.</br>
![Teachers' View](https://cloud.githubusercontent.com/assets/18289780/25997183/4ffe9982-371b-11e7-9d3b-7fe121ba3f9f.png)

### Students' view
In the current state of the block groupings are not displayed to the students.</br>
Students are only able to view the groups they are enrolled in.</br>
![Students' View](https://cloud.githubusercontent.com/assets/18289780/25997179/4beb7658-371b-11e7-923d-ac84cdbb4878.png)