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
 * Scheduled task: check internet connectivity.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task that checks whether an internet connection is available.
 *
 * When no connection is detected the task exits silently so that it does not
 * fill the Moodle log with noise. When a connection is detected a debug-level
 * message is written to the PHP error log.
 *
 * The default schedule is every 2 minutes (* /2 * * * *). Administrators can
 * change the schedule at Site administration > Server > Scheduled tasks.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collect_and_send_statistics extends \core\task\scheduled_task {

    /**
     * Return the human-readable name of this task.
     *
     * @return string Localised task name.
     */
    public function get_name(): string {
        return get_string('checkinternet_taskname', 'tool_moodlebox');
    }

    /**
     * Execute the task.
     *
     * Probes an external host to determine whether the device has internet
     * access. Exits immediately (no-op) when offline.
     */
    public function execute(): void {
      // ====
      // 1. If it is time to collect statistics, collect them
      // collect statitistics and append to the file local file
      // TODO: save in file what was the last time we collected statistics
      // ====
    //   if (time() % (30 * 24 * 60 * 60) == 0) { // Once a month
    //     // Collect statistics and append to the file
    //   }

      // ====
      // 2. If there is any statistics to send, check if there is an internet connection
      // ====
    //   if (!\tool_moodlebox\local\utils::has_internet_connection()) {
    //     // No internet — exit silently, nothing to do.
    //     return;
    //   }

      // ====
      // 3. If there is an internet connection, send the statistics
      // ====

      

        error_log('we have an internet connection');
    }
}
