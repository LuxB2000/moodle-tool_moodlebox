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
 * Statistics collection form for the MoodleBox plugin.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form used to trigger statistics collection and optionally reset the local statistics file.
 *
 * @package    tool_moodlebox
 * @copyright  2026 Jerome Plumat
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class statistics_form extends moodleform {

    /**
     * Define the form elements.
     */
    public function definition() {
        $mform = $this->_form;

        // get current config
        $config = \tool_moodlebox\local\statistics_lib::get_configuration_from_file();

        // config
        $mform->addElement('header', 'config', get_string('config', 'tool_moodlebox'));
        $mform->addElement('text', 'maxfilesize', get_string('maxfilesize', 'tool_moodlebox'));
        $mform->setType('maxfilesize', PARAM_INT);
        $mform->setDefault(
            'maxfilesize',
            (int)($config['maxfilesize'] / 1024) // rendered with kB units
        );
        $mform->addHelpButton('maxfilesize', 'maxfilesize', 'tool_moodlebox');

        // reset
        $mform->addElement('advcheckbox', 'reset', get_string('reset', 'tool_moodlebox'));
        $mform->addHelpButton('reset', 'reset', 'tool_moodlebox');

        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'saveconfiguration',
            get_string('saveconfiguration', 'tool_moodlebox'));
        $buttonarray[] = $mform->createElement('submit', 'testcollectingstatistics',
            get_string('testcollectingstatistics', 'tool_moodlebox'));
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');
    }
}
