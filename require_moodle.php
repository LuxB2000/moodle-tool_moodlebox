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
 * Moodle bootstrap helper for symlinked development setups.
 *
 * PHP's __FILE__ resolves symbolic links to the real filesystem path, so the
 * standard dirname(dirname(dirname(dirname(__FILE__)))) pattern fails when the
 * plugin directory is a symlink pointing outside the Moodle tree.
 *
 * Usage in every plugin page file:
 *   require_once __DIR__ . '/require_moodle.php';
 *
 * For a symlinked setup, create a gitignored local_moodle_path.php next to
 * this file containing:
 *   <?php define('MOODLE_ROOT', '/path/to/your/moodle');
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * TODO : remove
 * This is dev stuff, to be removed before release
 */

// Standard install: plugin lives directly inside Moodle at admin/tool/moodlebox/.
$moodleroot = dirname(dirname(dirname(dirname(__FILE__))));

if (!file_exists($moodleroot . '/config.php')) {
    // Symlinked dev setup: __FILE__ resolved to the real repo path, so
    // dirname(x4) missed the Moodle root. Try the local override.
    $localoverride = __DIR__ . '/local_moodle_path.php';
    if (file_exists($localoverride)) {
        require_once $localoverride;
        $moodleroot = MOODLE_ROOT;
    } else {
        throw new \RuntimeException(
            "Cannot locate Moodle's config.php. " .
            "In a symlinked dev setup, create local_moodle_path.php with: " .
            "define('MOODLE_ROOT', '/absolute/path/to/moodle');"
        );
    }
}

require_once $moodleroot . '/config.php';
