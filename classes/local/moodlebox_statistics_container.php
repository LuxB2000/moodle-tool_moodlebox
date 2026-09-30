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
 * Container for a set of MoodleBox statistics.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox\local;

class generic_statistic extends \tool_moodlebox\local\moodlebox_statistic {
    private mixed $value = null;
    public function __construct($value, $name, $type) {
        $this->set_statistic_name($name);
        $this->set_statistic_type($type);
        $this->set_description('A generic statistic used when loaded from JSON file');
        $this->value = $value;
    }
    
    #[\Override]
    public function collecting_function(): mixed {
        return $this->value;
    }
}

/**
 * Holds and serialises a collection of MoodleBox statistics gathered at a single point in time.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlebox_statistics_container {

    /** @var int Unix timestamp at which this container was created. */
    private int $creationdate;

    /** @var array Collected statistic fields, keyed by statistic name. */
    private array $fields = [];

    /**
     * Create a new statistics container.
     *
     * @param ?string $jsonstring Optional JSON string to pre-populate fields from a previously
     *     serialised container. When null a fresh, empty container is created.
     *  If set, this input will be used to reconstruct the container with generic statistics objects
     */
    public function __construct(?string $jsonstring = null) {
        $this->creationdate = time();
        if ($jsonstring !== null) {

            if (!json_validate($jsonstring)) {
                error_log("[moodblebox_statistics_container::__construct] Invalid JSON string: " . $jsonstring);
                return;
            }

            $raw = json_decode($jsonstring, true);
            foreach ($raw as $key => $statvalue) {
                $type = $statvalue['type'];
                if ($type !== 'string' && $type !== 'number') {
                    error_log("[moodblebox_statistics_container::__construct] Invalid type: " . $type . " for key: " . $key);
                    continue;
                }
                $value = $statvalue['value'];
                if (!is_numeric($value) && !is_string($value)) {
                    error_log("[moodblebox_statistics_container::__construct] Invalid value: " . $value . " for key: " . $key);
                    continue;
                }
                $name = $statvalue['name'];
                if (!is_string($name) || $name === '') {
                    error_log("[moodblebox_statistics_container::__construct] Invalid name: " . $name . " for key: " . $key);
                    continue;
                }
                $this->fields[$key] = new generic_statistic($value, $name, $type);
            }
        }
    }

    public function get_fields_iterable() {
        return $this->fields;
    }

    /**
     * Discover and run all statistic collectors found in the statistics/ sub-directory.
     *
     * Each PHP file in the statistics/ directory is expected to define a concrete subclass
     * of {@see moodlebox_statistic} whose class name matches the filename (without .php).
     * The method explicitly requires every file because Moodle's autoloader caches class
     * locations at install time and will not pick up files added dynamically.
     */
    public function collect(): void {
        $statisticsdir = __DIR__ . '/statistics';
        $files = glob($statisticsdir . '/*.php');

        if ($files === false || empty($files)) {
            return  ;
        }

        foreach ($files as $file) {
            // Explicitly require the file — do not rely on Moodle's autoloader,
            // which caches class locations at install time and won't see files
            // added dynamically to the statistics/ folder.
            require_once $file;
            // Derive FQCN from filename: moodle_version_number.php
            //   → \tool_moodlebox\local\statistics\moodle_version_number
            $classname = basename($file, '.php');
            $fqcn = '\\tool_moodlebox\\local\\statistics\\' . $classname;
            // Guard: only instantiate concrete subclasses of moodlebox_statistic.
            // Pass false to class_exists() — no autoloader needed, file is already loaded.
            $isvalidstatistic = class_exists($fqcn, false)
                && is_subclass_of($fqcn, '\\tool_moodlebox\\local\\moodlebox_statistic');
            if ($isvalidstatistic) {
                $instance = new $fqcn();
                $this->fields[$instance->get_name()] = $instance;
            }
        }
    }

    /**
     * Return the creation date of this container in ISO 8601 format.
     *
     * @return string The creation date formatted as ISO 8601.
     */
    public function get_date(): string {
        return date('c', $this->creationdate);
    }

    /**
     * Serialise this container to a JSON string.
     *
     * @return string JSON representation of the container, including the creation date and all fields.
     */
    public function to_json(): string {
        $creationDateRecord = [
            'name' => 'creationdate',
            'type' => 'string',
            'value' => $this->get_date(),
        ];
        $content = ['creationdate' => $creationDateRecord];
        foreach ($this->fields as $field) {
            $content[$field->get_name()] = $field->to_array();
        }
        return json_encode($content);
    }
}
