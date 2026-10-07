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
// require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
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

// == Render introduction and current statistics ==
echo $OUTPUT->box_start('generalbox', 'form');
echo '<h3>' . get_string('currentstatisticsheader', 'tool_moodlebox') . '</h3>';
echo $OUTPUT->box(get_string('statisticsinformation', 'tool_moodlebox'));
$currentstatistics = new moodlebox_statistics_container();
$currentstatistics->collect(); // collect data to dynamically parse the folder with statistic objects defined
foreach ($currentstatistics->get_fields_iterable() as $key => $statistic) {
    echo '<p>' . $statistic->get_name() . ': ' . $statistic->get_description() . ' - current value: ' . $statistic->get_value() . '</p>';
}
echo $OUTPUT->box_end();

// == Collect form data if form has been submitted ==
$restultofsend = null;
$errorofsend = null;
if ($data = $statisticsform->get_data()) {
    // find wich button has been clicked
    // do we want to save config ?
    // or to run the test ?
    if (isset($data->saveconfiguration)) {
        // Save configuration
        $config = [
            'maxfilesize' => $data->maxfilesize * 1024, // convert from kB to B
        ];
        statistics_lib::save_configuration_to_file($config);
    } elseif (isset($data->testcollectingstatistics)) {
        // Run test     
        // 1. If the file is not created, then create it.
        if (!statistics_lib::is_local_file_present()) {
            statistics_lib::create_local_file();
        }
        $reset = $data->reset;
        // 1.1 If reset is true, empty the file.
        if ($reset) {
            statistics_lib::empty_local_file();
        }
        // 2. Create a statistics container and collect new data.
        $statisticscontainer = new moodlebox_statistics_container();
        $statisticscontainer->collect();

        // 3. Get all the local statistics
        $localstatisticcontainers = statistics_lib::get_statistics_from_file();
        
        // 4. Send the statistics to the server.
        $allstatistics = array_merge($localstatisticcontainers, [$statisticscontainer]);
        try{
            statistics_lib::send_statistics($allstatistics);
            $restultofsend = 'success';
            // TODO: 4.2 clean ONLY the statistics from local file
        } catch (\Exception $e) {
            error_log('Error sending statistics: ' . $e->getMessage(), DEBUG_DEVELOPER);
            // TODO: save the error in the statistics container and save it as "resut of sending that day". in meta-data file ?
            $errorofsend = $e->getMessage();
            // 4.3. Append the statistics to the local file.
            statistics_lib::add_statistics_to_local_file($statisticscontainer);
        }
    }

    // Reset the form.
    $statisticsform = new statistics_form(); // TO FIX: checkbox is still checked
}

// == Render previous statistics ==
$collectedstatistics = statistics_lib::get_statistics_from_file();
echo $OUTPUT->box_start('generalbox', 'intro');
echo '<h3>' . get_string('previousstatisticsheader', 'tool_moodlebox') . '</h3>';
if (statistics_lib::is_local_file_present()) {
    echo '<p>' . get_string('statisticslocalfilepresent', 'tool_moodlebox') . '</p>';
    echo '<p>' . get_string('statisticscollectednotsent', 'tool_moodlebox', count($collectedstatistics)) . '</p>';
    if (count($collectedstatistics) > 0) {
        echo '<p>' . get_string('statisticslastcollected', 'tool_moodlebox', end($collectedstatistics)->get_date()) . '</p>';
        echo '<p>' . get_string('prevstatisticsintro', 'tool_moodlebox') . '</p>';
        echo '<pre>';
        foreach (end($collectedstatistics)->get_fields_iterable() as $statistic) {
            echo '<p><strong>' . $statistic->get_name() . '</strong> - value: ' .
            ($statistic->get_value() ? ' ' . $statistic->get_value() . ' ' : '<strong>null</strong>') .
            '</p>';
        }
        echo '</pre>';
    } else {
        echo '<p>' . get_string('statisticslastcollected', 'tool_moodlebox', 'never') . '</p>';
    }
} else {
    echo '<p>' . get_string('statisticslocalfilenotpresent', 'tool_moodlebox') . '</p>';
}
echo $OUTPUT->box_end();

// == Render result of sending statistics to server ==
if ($restultofsend) {
    echo $OUTPUT->box_start('generalbox', 'intro');
    echo '<p>' . get_string('statisticsresultofsend', 'tool_moodlebox', $restultofsend) . '</p>';
    echo $OUTPUT->box_end();
}
if ($errorofsend) {
    echo $OUTPUT->box_start('generalbox', 'intro');
    echo '<p>' . get_string('statisticserrorofsend', 'tool_moodlebox', $errorofsend) . '</p>';
    echo $OUTPUT->box_end();
}
        
echo $statisticsform->render();

echo $OUTPUT->footer();
