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
 * Plugin configuration constants for Tool MoodleBox configuration.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_moodlebox\local;

class plugin_config {
    // Developer-controlled — users cannot change these.
    // Update in code when releasing a new plugin version.
    // TODO: define the production server URL
    const STATISTICS_SERVER_URL = 'http://127.0.0.1:3000/statistics';

    // Defaults for user-configurable settings.
    // Used as fallback when no value is saved yet.
    const DEFAULT_MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB in bytes
}