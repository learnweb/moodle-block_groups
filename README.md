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
![Teachers' View_hidden](https://cloud.githubusercontent.com/assets/18289780/14320600/fa2933aa-fc15-11e5-9e91-5129e7f37f4f.png)</br>
The block displays all existing groups and groupings as well as all enrolled groups on request.
The number inside the brackets displays the number of members in a group or grouping.
Additionally groups can be hidden, illustrated by an eye icon and a change in opacity.
When the icon is clicked the visibility of groups changes. Javascript files exist to update the block.
When the block is installed for the first time all groups are hidden</br>
![Teachers' View](https://cloud.githubusercontent.com/assets/18289780/15298723/0871071c-1ba1-11e6-8fc9-2b0b1d58aaaf.png)</br>
In case the required change is not possible a warning message is displayed and the affected group is marked with a triangle.</br>
![Teachers' View](https://cloud.githubusercontent.com/assets/18289780/15849889/21be9d4c-2c95-11e6-967b-8daac7140892.png)

### Students' view
In the current state of the block groupings are not displayed to the students.</br>
Students are only able to view the groups they are enrolled in.</br>
![Students' View](https://cloud.githubusercontent.com/assets/18289780/14318694/6bcaae1a-fc0e-11e5-822b-75e5b45316d5.png)



