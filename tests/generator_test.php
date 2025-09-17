<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * PHPUnit data generator tests
 * @package    block_groups
 * @category   test
 * @copyright  2016/17 N Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_groups;

/**
 * PHPUnit data generator testcase
 * @package    block_groups
 * @category   test
 * @group block_groups
 * @copyright  2016/17 N Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class generator_test extends \advanced_testcase {

    /**
     * Test generator.
     * @covers \block_groups_generator
     */
    public function test_generator(): void {
        global $DB;
        $this->resetAfterTest(true);

        $beforeblocks = $DB->count_records('block_instances');

        /** @var \block_groups_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('block_groups');
        $this->assertInstanceOf('block_groups_generator', $generator);
        $this->assertEquals('groups', $generator->get_blockname());

        $generator->create_instance();
        $this->assertEquals($beforeblocks + 1, $DB->count_records('block_instances'));
    }
}
