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


namespace tool_moodlebox\form;
use moodleform;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

class statistics_form extends moodleform {
    public function definition() {

        // add a checkbox to reset the local file
        $mform = $this->_form;
        $mform->addElement('advcheckbox', 'reset', get_string('reset', 'tool_moodlebox'));

        $this->add_action_buttons(
            false, 
            get_string('testcollectingstatistics', 'tool_moodlebox')
        );
    }
}
