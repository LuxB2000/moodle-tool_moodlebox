<?php
// This file is part of Moodle - https://moodle.org/
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
 * Scheduled task definitions for tool_moodlebox.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        // Run the scripts regularly to check internet connectivity and send statistics.
        // TODO : IDEA : If recent stats have been sent, no need to check every two minutes. But if statistics accumulate,
        //        we should check more often. Possible ?
        // TODO : find correct default value
        'classname'  => \tool_moodlebox\task\collect_and_send_statistics::class,
        'blocking'   => 0,
        'minute'     => '*/2',
        'hour'       => '*',
        'day'        => '*',
        'month'      => '*',
        'dayofweek'  => '*',
    ],
];
