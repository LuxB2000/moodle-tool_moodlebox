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
 * Statistics library for tool_moodlebox.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox\local;

/**
 * Static helper library for persisting MoodleBox statistics to the Moodle data directory.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class statistics_lib {

    /** @var ?string Absolute path to the local JSON statistics file, or null before initialisation. */
    private static ?string $localfilepath = null;

    /**
     * Resolve and cache the path to the local statistics file.
     *
     * The dataroot is intentionally outside the webroot for security — uploaded files,
     * caches, and runtime data should never be web-accessible.
     */
    private static function init(): void {
        global $CFG;
        if (self::$localfilepath === null) {
            self::$localfilepath = $CFG->dataroot . '/admin/tool/moodlebox/statistics-metadata.json';
        }
    }

    /**
     * Check whether the local statistics file exists.
     *
     * @return bool True if the file is present, false otherwise.
     */
    public static function is_local_file_present(): bool {
        self::init();
        return file_exists(self::$localfilepath);
    }

    /**
     * Create the local statistics file with default empty content.
     *
     * Creates any missing parent directories as needed.
     */
    public static function create_local_file(): void {
        self::init();
        $content = [
            'statistics' => [],
            'configuration' => [
                'maxfilesize' => (\tool_moodlebox\local\plugin_config::DEFAULT_MAX_FILE_SIZE),
            ],
        ];
        $dir = dirname(self::$localfilepath);
        if (!is_dir($dir)) {
            make_writable_directory($dir);
        }
        file_put_contents(self::$localfilepath, json_encode($content));
    }

    /**
     * Reset the local statistics file to its default empty state.
     *
     * Deletes the existing file and recreates it with default content.
     */
    public static function empty_local_file(): void {
        self::init();
        unlink(self::$localfilepath);
        self::create_local_file();
    }

    /**
     * Append a statistics container entry to the local file.
     *
     * Loads the file, appends the serialised container to the "statistics" array,
     * then writes the result back to disk.
     *
     * @param moodlebox_statistics_container $statistics The container to persist.
     * @throws \coding_exception If the statistics array key is missing from the file.
     */
    public static function add_statistics_to_local_file(moodlebox_statistics_container $statistics): void {
        self::init();
        $content = json_decode(file_get_contents(self::$localfilepath), true);
        if (!isset($content['statistics'])) {
            throw new \coding_exception('Statistics array not found in local file');
        }
        $content['statistics'][] = $statistics->to_json();
        file_put_contents(self::$localfilepath, json_encode($content));
    }

    /**
     * Load and return all previously collected statistics containers from the local file.
     *
     * @return moodlebox_statistics_container[] Array of statistics containers.
     * @throws \coding_exception If the statistics array key is missing from the file.
     */
    public static function collect_statistics_from_file(): array {
        self::init();
        if (!file_exists(self::$localfilepath)) {
            return [];
        }
        $content = json_decode(file_get_contents(self::$localfilepath), true);
        if (!isset($content['statistics'])) {
            throw new \coding_exception('Statistics array not found in local file');
        }
        $result = [];
        foreach ($content['statistics'] as $statistic) {
            $result[] = new moodlebox_statistics_container($statistic);
        }
        return $result;
    }

    public static function get_configuration_from_file(): array {
        self::init();
        $content = json_decode(file_get_contents(self::$localfilepath), true);
        if (!isset($content['configuration'])) {
            throw new \coding_exception('Configuration array not found in local file');
        }
        return array_merge(
            ['maxfilesize' => plugin_config::DEFAULT_MAX_FILE_SIZE], // fallback
            $content['configuration'] ?? []
        );
    }

    public static function save_configuration_to_file($newconfig): void {
        self::init();
        $content = json_decode(file_get_contents(self::$localfilepath), true);
        if (!isset($content['configuration'])) {
            throw new \coding_exception('Configuration array not found in local file');
        }
        $content['configuration'] = $newconfig;
        file_put_contents(self::$localfilepath, json_encode($content));
    }
}
