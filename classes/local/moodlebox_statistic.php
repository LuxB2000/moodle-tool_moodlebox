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
 * Abstract base class for a single MoodleBox statistic.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox\local;

/**
 * Abstract base class representing a single collectable statistic.
 *
 * Subclasses must implement {@see collecting_function()} to return the actual
 * value for the statistic they represent.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class moodlebox_statistic {

    /** @var string The identifier name of this statistic. */
    protected string $name = '';

    /** @var string The data type of this statistic value. One of: string, number, boolean, array, object. */
    protected string $type = '';

    /** @var string The description of this statistic. */
    protected string $description = '';

    /**
     * Set the identifier name of this statistic.
     *
     * @param string $name The statistic identifier name.
     */
    public function set_statistic_name(string $name): void {
        $this->name = $name;
    }

    /**
     * Set the data type of this statistic.
     *
     * Must be one of: string, number, boolean, array, object.
     *
     * @param string $type The data type of the statistic value.
     */
    public function set_statistic_type(string $type): void {
        $this->type = $type;
    }

    /**
     * Set the description of this statistic.
     *
     * @param string $description The description of the statistic.
     */
    public function set_description(string $description): void {
        $this->description = $description;
    }

    /**
     * Return the statistic as an associative array.
     *
     * @return array The statistic data with name, type and value keys.
     */
    public function to_array(): array {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'value' => $this->collecting_function(),
            // 'description' => $this->get_description(), // we don't want the description since it's a local string (translation is used when possible)
        ];
    }

    /**
     * Get the description of this statistic.
     *
     * @return string The description of the statistic.
     */
    public function get_description(): string {
        return $this->description;
    }

    public function get_name(): string {
        return $this->name;
    }

    public function get_value() : mixed {
        $value = $this->collecting_function();
        return $value;
    }

    // protected function
    protected function is_placeholder(string $string): bool {
      // placeholder is a string that contains [[ and ]]
      return str_contains($string, '[[') && str_contains($string, ']]');
    }

    /**
     * Collect and return the statistic value.
     *
     * The return value must match the type declared via {@see set_statistic_type()}.
     *
     * @return mixed The collected statistic value.
     */
    abstract public function collecting_function(): mixed;
}
