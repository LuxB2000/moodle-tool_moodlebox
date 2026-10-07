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
 * Statistic collector for the MoodleBox image version number.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox\local\statistics;

use tool_moodlebox\local\moodlebox_statistic;

/**
 * Collects the current MoodleBox image version number as a string statistic.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlebox_tool_version_number extends moodlebox_statistic {

    /**
     * Initialise the statistic with its name and type.
     */
    public function __construct() {
        $description = "The current MoodleBox Tool plugin version number";
        $langstring = get_string('moodlebox_tool_version_numberdescription', 'tool_moodlebox');
        if (!$this->is_placeholder($langstring)) {
            $description = $langstring;
        }
        $this->set_statistic_name('moodlebox_tool_version_number');
        $this->set_statistic_type('string');
        $this->set_description($description);
    }

    /**
     * Return the current MoodleBox image version number.
     *
     * @return string The MoodleBox image version number.
     */
    #[\Override]
    public function collecting_function(): string {
        return $this->get_version_number();
    }

    /**
     * Read the MoodleBox tool plugin version number via the Moodle plugin manager.
     *
     * @return string The MoodleBox tool plugin release string (e.g. '3.3.1'), or empty string if unavailable.
     */
    private function get_version_number(): string {
        $plugininfo = \core_plugin_manager::instance()->get_plugin_info('tool_moodlebox');
        if ($plugininfo === null) {
            return '';
        }
        return $plugininfo->release ?? '';
    }
}
