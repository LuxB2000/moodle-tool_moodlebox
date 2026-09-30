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
 * Test script for collecting statistics.
 * 
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat, original work by Nicolas Martignoni
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox\local;

require_once __DIR__ . '/../../require_moodle.php';
require_once($CFG->libdir . '/moodlelib.php');

defined('MOODLE_INTERNAL') || die;

abstract class moodlebox_statistic {
  public $name = '';
  public $type = '';
  public function set_statistic_name(string $name) {
    $this->name = $name;
  }
  // $type must be one of: 'string', 'number', 'boolean', 'array', 'object'
  public function set_statistic_type(string $type) {
    $this->type = $type;
  }
  public function to_array() : array {
    return [
      'name' => $this->name,
      'type' => $this->type,
      'value' => $this->collecting_function(),
    ];
  }
  /**
   * Collects the statistic value. The return value must be of the type specified in set_statistic_type.
   * 
   * @return mixed The collected statistic value.
   */
  abstract public function collecting_function() : mixed;
}

class moodlebox_statistics_container {
  private $creationdate = null;
  private $fields = [];

  public function __construct(string $jsonstring = null) {
    $this->creationdate = time();
    if ($jsonstring !== null) {
      $this->fields = json_decode($jsonstring, true);
    }
  }

  public function add_field($key, $value) {
    $this->fields[$key] = $value;
  }

  public function collect(): void {
    $statisticsdir = __DIR__ . '/statistics';
    $files = glob($statisticsdir . '/*.php');

    if ($files === false || empty($files)) {
      error_log('No statistics files found in ' . $statisticsdir);
      return;
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
        if (class_exists($fqcn, false) && is_subclass_of($fqcn, '\\tool_moodlebox\\local\\moodlebox_statistic')) {
            $instance = new $fqcn();
            $this->fields[$instance->name] = $instance->to_array();
        } else {
            error_log('Class ' . $fqcn . ' not found or not a subclass of moodlebox_statistic');
        }
    }
  }
  
  /**
   * Get the creation date of the statistics container.
   * 
   * @return string The creation date in ISO format.
   */
  public function get_date() : string {
    return date('c', $this->creationdate);
  }
  
  public function to_json() : string {
    $content = [
      'creationdate' => $this->get_date(),
    ];
    $content = array_merge($content, $this->fields);
    return json_encode($content);
  }
}

class statistics_lib {
  
  static $LOCAL_FILE_PATH = null;
  
  public static function init() {
    global $CFG;
    if (self::$LOCAL_FILE_PATH === null) {
      // The dataroot is intentionally outside the webroot for security — uploaded files, caches, and runtime data should never be web-accessible.
      self::$LOCAL_FILE_PATH = $CFG->dataroot . '/admin/tool/moodlebox/statistics-metadata.json';
    }
  }
  
  /**
   * Check if the local file is present.
   * 
   * @return bool True if the local file is present, false otherwise.
   */
  public static function is_local_file_present() : bool {
    self::init();
    return file_exists(self::$LOCAL_FILE_PATH);
  }

  /**
   * Create the local file with default content.
   */
  public static function create_local_file() : void {
    self::init();
    // Create the file with default content.
    $content = [
        'statistics' => [],
    ];
    // Ensure the directory exists before writing.
    $dir = dirname(self::$LOCAL_FILE_PATH);
    if (!is_dir($dir)) {
        make_writable_directory($dir);
    }
    error_log("Creating local file: " . self::$LOCAL_FILE_PATH);
    file_put_contents(self::$LOCAL_FILE_PATH, json_encode($content));
  }

  /**
   * Empty the local file.
   * This deletes the file and recreates it with default content.
   */
  public static function empty_local_file() : void {
    self::init();
    // delete the file
    unlink(self::$LOCAL_FILE_PATH);
    // recreate it
    self::create_local_file();
  }
    
  /**
   * Add statistics to the local file.
   * This loads the file, adds the input to the "statistics" array that should be present in it and saves it.
   */
  public static function add_statistics_to_local_file(moodlebox_statistics_container $statistics) : void {
    self::init();
    $content = json_decode(file_get_contents(self::$LOCAL_FILE_PATH), true);
    if (!isset($content['statistics'])) {
      throw new Exception('Statistics array not found in local file');
    }
    $content['statistics'][] = $statistics->to_json();
    file_put_contents(self::$LOCAL_FILE_PATH, json_encode($content));
  }

  /**
   * Collect statistics.
   * This should be called periodically to collect statistics.
   * 
   * @return array An array of moodlebox_statistics_container objects.
   */
  public static function collect_statistics() : array {
    self::init();
    // read the file
    $content = json_decode(file_get_contents(self::$LOCAL_FILE_PATH), true);
    if (!isset($content['statistics'])) {
      throw new Exception('Statistics array not found in local file');
    }
    $res = [];
    foreach ($content['statistics'] as $statistic) {
      $res[] = new moodlebox_statistics_container($statistic);
    }
    return $res;
  }
}

