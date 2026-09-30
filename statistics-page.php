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
 * Statistics collection test page for the MoodleBox plugin.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox;

require_once __DIR__ . '/require_moodle.php';
require_once($CFG->libdir . '/moodlelib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/admin/tool/moodlebox/classes/form/statistics_form.php');
require_once($CFG->dirroot . '/admin/tool/moodlebox/classes/local/statistics-lib.php');

defined('MOODLE_INTERNAL') || die;
admin_externalpage_setup('tool_moodlebox_statistics');

$PAGE->set_url('/admin/tool/moodlebox/statistics.php');

// $PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$strheading = get_string('statisticssettingsheading', 'tool_moodlebox');
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

$statisticsform = new \tool_moodlebox\form\statistics_form();

echo $OUTPUT->header();

echo $OUTPUT->heading($strheading);


if ($data = $statisticsform->get_data()) {
  // Collect results from form
  $reset = $data->reset;
  // TODO: run the actual statistics collection and sending logic here.
  echo "<p>" . "This works !!" . "</p>";
  // Run the main statistics process
  // 1. if the file is not created, then create the file
  if (!\tool_moodlebox\local\statistics_lib::is_local_file_present()) {
    \tool_moodlebox\local\statistics_lib::create_local_file();
  }
  // 1.1 if reset is true, then empty the file
  if ($reset) {
    \tool_moodlebox\local\statistics_lib::empty_local_file();
  }
  // 2. create a statistics container
  $statistics = new \tool_moodlebox\local\moodlebox_statistics_container();
  $statistics->collect();
  // 3. happen the statistics to the local file
  \tool_moodlebox\local\statistics_lib::add_statistics_to_local_file($statistics);
  
  echo "<p>" . "Statistics added to local file" . "</p>";


  // reset the form
  $statisticsform = new \tool_moodlebox\form\statistics_form(); // TO FIX: checkbox is still checked
}

$collectedstatistics = \tool_moodlebox\local\statistics_lib::collect_statistics();
// Display the presence of the local file.
if (\tool_moodlebox\local\statistics_lib::is_local_file_present()) {
  echo "<p>" . "Local file present" . "</p>";
  echo "<p>" . "Statistics collected yet not sent: " . count($collectedstatistics) . "</p>";
  echo "<p>" . "Last statistics collected: " . end($collectedstatistics)->get_date() . "</p>";
} else {
  echo "<p>" . "Local file not present: no statistics collected yet" . "</p>";
}

echo $statisticsform->render();

echo $OUTPUT->footer();
