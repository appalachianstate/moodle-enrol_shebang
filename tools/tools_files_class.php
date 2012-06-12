<?php

    defined('MOODLE_INTERNAL') || die();

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

    require_once($CFG->libdir  .  '/formslib.php');
    require_once($CFG->libdir  .  '/uploadlib.php');
    require_once($CFG->dirroot .  '/repository/lib.php');


    /**
     * Tool definition class
     *
     */
    class enrol_shebang_tools_files
    {

        /**
         * Tool name
         * @var string
         * @access public
         */
        public $name    = 'Manage Files';
        /**
         * Tool action
         *
         * @var string
         * @access public
         */
        public $action  = 'files';
        /**
         * Tool description
         *
         * @var string
         * @access public
         */
        public $desc    = 'Manage files uploaded files.';



        /**
         * Files tool page request handler (GETs & POSTs)
         *
         * @access  public
         * @return  void
         * @uses    $CFG, $SITE, $OUTPUT, $PAGE
         */
        public function handle_request()
        {
            global $CFG, $SITE, $OUTPUT, $PAGE;



            $context = get_system_context();

            $admin_url = new moodle_url(enrol_shebang_plugin::PLUGIN_PATH . '/admin/settings.php', array('section' => 'enrolsettingsshebang'));
            $index_url = new moodle_url(enrol_shebang_plugin::PLUGIN_PATH . '/tools.php');
            $files_url = new moodle_url(enrol_shebang_plugin::PLUGIN_PATH . '/tools.php', array('action' => 'files'));

            $PAGE->set_context($context);
            $PAGE->set_title($SITE->fullname . ':' . get_string('LBL_TOOLS_FILES', enrol_shebang_plugin::PLUGIN_NAME));
            $PAGE->set_url($admin_url);
            $PAGE->set_heading($SITE->fullname);

            $PAGE->navbar->add(get_string('LBL_TOOLS_INDEX', enrol_shebang_plugin::PLUGIN_NAME), $index_url);
            $PAGE->navbar->add(get_string('LBL_TOOLS_FILES', enrol_shebang_plugin::PLUGIN_NAME), null);

            navigation_node::override_active_url($admin_url);

            echo $OUTPUT->header();

            $data = new stdClass();
            $data->returnurl = $index_url;

            $options = array('subdirs'        => false,
                             'maxfiles'       => 100,
                             'accepted_types' => '*.xml',
                             'return_types'   => FILE_INTERNAL);

            file_prepare_standard_filemanager($data, 'files', $options, $context, enrol_shebang_plugin::PLUGIN_NAME, 'uploads', 0);
            $mform = new enrol_shebang_tools_files_form($files_url, array('data' => $data, 'options' => $options));
            if (!$mform->is_submitted()) {
                // Handle the GET, display the form
                $mform->display();
            } elseif ($mform->is_cancelled()) {
                // POSTed, but cancel button clicked
                redirect($index_url);
            } elseif ($formdata = $mform->get_data()) {
                // Handle the POSTed data
                file_postupdate_standard_filemanager($formdata, 'files', $options, $context, enrol_shebang_plugin::PLUGIN_NAME, 'uploads', 0);
                redirect($index_url);
            }

            echo $OUTPUT->footer();

        } // handle_request


    } // class



    /**
     * The tool interface -- moodleform class definition for the plugin
     *
     */
    class enrol_shebang_tools_files_form extends moodleform
    {

        /**
         * Define the form's contents
         *
         * @access public
         * @return void
         */
        public function definition()
        {
            $data    = $this->_customdata['data'];
            $options = $this->_customdata['options'];

            $this->_form->addElement('filemanager', 'files_filemanager', get_string('files'), null, $options);
            $this->_form->addElement('hidden', 'returnurl', $data->returnurl);

            $this->add_action_buttons(true, get_string('savechanges'));
            $this->set_data($data);

        } // definition



		/**
         * Validate the submitted form data (file upload)
         *
         * @access public
         * @return array
         * @uses $CFG
         */
        public function validation($data, $files)
        {
            global $CFG;



            $errors = array();
            $fileinfo = file_get_draft_area_info($data['files_filemanager']);
            if ($fileinfo['filesize'] > $CFG->userquota) {
                $errors['files_filemanager'] = get_string('userquotalimit', 'error');
            }

            return $errors;

        } // validation


    } // class
