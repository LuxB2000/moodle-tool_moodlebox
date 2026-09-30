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
 * Statistics collection page for the MoodleBox plugin.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once __DIR__ . '/require_moodle.php';
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/classes/form/statistics_form.php');
require_once(__DIR__ . '/classes/local/statistics_lib.php');
require_once(__DIR__ . '/classes/local/moodlebox_statistics_container.php');


use tool_moodlebox\form\statistics_form;
use tool_moodlebox\local\moodlebox_statistics_container;
use tool_moodlebox\local\statistics_lib;

admin_externalpage_setup('tool_moodlebox_statistics');

$PAGE->set_url('/admin/tool/moodlebox/statistics.php');
$PAGE->set_pagelayout('admin');
$strheading = get_string('statisticssettingsheading', 'tool_moodlebox');
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

$statisticsform = new statistics_form();

echo $OUTPUT->header();
echo $OUTPUT->heading($strheading);

echo $OUTPUT->box_start('generalbox', 'intro');
echo $OUTPUT->box(get_string('statisticsinformation', 'tool_moodlebox'));
echo $OUTPUT->box_end();

if ($data = $statisticsform->get_data()) {
    $reset = $data->reset;
    // TODO: run the actual statistics collection and sending logic here.
    echo '<p>' . get_string('statisticstestok', 'tool_moodlebox') . '</p>';
    // 1. If the file is not created, then create it.
    if (!statistics_lib::is_local_file_present()) {
        statistics_lib::create_local_file();
    }
    // 1.1 If reset is true, empty the file.
    if ($reset) {
        statistics_lib::empty_local_file();
    }
    // 2. Create a statistics container and collect data.
    $statistics = new moodlebox_statistics_container();
    $statistics->collect();
    // 3. Append the statistics to the local file.
    statistics_lib::add_statistics_to_local_file($statistics);

    echo '<p>' . get_string('statisticsaddedtolocalfile', 'tool_moodlebox') . '</p>';

    // Reset the form.
    $statisticsform = new statistics_form(); // TO FIX: checkbox is still checked
}

$collectedstatistics = statistics_lib::collect_statistics();
// Display the presence of the local file.
if (statistics_lib::is_local_file_present()) {
    echo '<p>' . get_string('statisticslocalfilepresent', 'tool_moodlebox') . '</p>';
    echo '<p>' . get_string('statisticscollectednotsent', 'tool_moodlebox', count($collectedstatistics)) . '</p>';
    echo '<p>' . get_string('statisticslastcollected', 'tool_moodlebox', end($collectedstatistics)->get_date()) . '</p>';
} else {
    echo '<p>' . get_string('statisticslocalfilenotpresent', 'tool_moodlebox') . '</p>';
}

echo $statisticsform->render();

echo $OUTPUT->footer();
