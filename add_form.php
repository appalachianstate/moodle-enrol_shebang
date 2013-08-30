<?php

    /**
     * SHEBanG enrolment plugin/module for SunGard HE Banner(r) data import
     *
     * This program is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or (at
     * your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but
     * WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
     * General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with this program. If not, see <http://www.gnu.org/licenses/>.
     *
     * @author      Fred Woolard <woolardfa@appstate.edu>
     * @copyright   (c) 2010 Appalachian State Universtiy, Boone, NC
     * @license     GNU General Public License version 3
     * @package     enrol
     * @subpackage  shebang
     */

    defined('MOODLE_INTERNAL') || die();

    require_once("{$CFG->libdir}/formslib.php");



    class enrol_shebang_add_form extends moodleform
    {


        public function definition()
        {

            $mform = $this->_form;
            $mform->addElement('header', 'header', get_string('pluginname', 'enrol_shebang'));

            $mform->addElement('hidden', 'id', $this->_customdata['id']);
            $mform->setType('id', PARAM_INT);

            // idnumber text box
            // -------------------------------------------------------------------------------
            $mform->addElement('text','idnumber', get_string('idnumbercourse'),'maxlength="100"  size="10"');
            $mform->addRule('idnumber', get_string('required'), 'required', null, 'client');
            $mform->addHelpButton('idnumber', 'idnumbercourse');
            $mform->setType('idnumber', PARAM_RAW);
            $mform->getElement('idnumber')->setValue($this->_customdata['idnumber']);

            $this->add_action_buttons(true, get_string('addinstance', 'enrol'));

        }



        function validation($data, $files)
        {
            global $DB;


            $errors = array();

            $idnumber = trim($data['idnumber']);
            if (empty($idnumber)) {
                $errors['idnumber'] = get_string('missingfield', 'error', 'idnumber');
            }

            // Check if idnumber is used elsewhere
            if ($DB->record_exists_select('course', 'idnumber = :idnumber AND id != :id', array('idnumber' => $idnumber, 'id' => $this->_customdata['id']))) {
                $errors['idnumber'] = get_string('idnumbertaken', 'error');
            }

            return $errors;

        }

    }
