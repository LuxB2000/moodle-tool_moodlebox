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
 * Statistic collector for the Moodle version number.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox\local\statistics;

use tool_moodlebox\local\moodlebox_statistic;

/**
 * Collects the current Moodle version number as a string statistic.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_version_number extends moodlebox_statistic {

    /**
     * Initialise the statistic with its name and type.
     */
    public function __construct() {
        $this->set_statistic_name('moodle_version_number');
        $this->set_statistic_type('string');
    }

    /**
     * Return the current Moodle version number.
     *
     * @return string The Moodle version number from $CFG->version.
     */
    #[\Override]
    public function collecting_function(): string {
        return $this->get_version_number();
    }

    /**
     * Read the Moodle version number from the global configuration.
     *
     * @return string The Moodle version number.
     */
    private function get_version_number(): string {
        global $CFG;
        return $CFG->version;
    }
}
