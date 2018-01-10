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

    require_once($CFG->libdir.'/formslib.php');
    require_once($CFG->libdir.'/uploadlib.php');
    require_once($CFG->dirroot.'/repository/lib.php');



    /**
     * Tool definition class
     *
     */
    class enrol_shebang_tools_import
    {

        const PLUGIN_NAME                       = 'enrol_shebang';
        const PLUGIN_FILEAREA                   = 'uploads';


        /**
         * Tool name
         * @var string
         * @access public
         */
        public $name    = 'Import File';
        /**
         * Tool task
         *
         * @var string
         * @access public
         */
        public $task  = 'import';
        /**
         * Tool description
         *
         * @var string
         * @access public
         */
        public $desc    = 'Upload and import an LMB/IMS XML file into the database.';
        /**
         * Valid actions
         *
         * @var array
         * @access private
         */
        private $_valid_actions = array('import', 'delete');



        /**
         * Tool page request handler (GETs & POSTs)
         *
         * @access  public
         * @return  void
         * @uses    $CFG, $SITE, $OUTPUT, $PAGE, $USER, $_REQUEST
         */
        public function handle_request()
        {
            global $CFG, $SITE, $OUTPUT, $PAGE, $USER, $_REQUEST;


            $system_context = context_system::instance();

            $admin_url  = new moodle_url("{$CFG->wwwroot}/admin/settings.php", array('section' => 'enrolsettingsshebang'));
            $index_url  = new moodle_url(enrol_shebang_processor::PLUGIN_PATH . '/tools.php');
            $import_url = new moodle_url(enrol_shebang_processor::PLUGIN_PATH . '/tools.php', array('task' => 'import'));

            $PAGE->set_context($system_context);
            $PAGE->set_title($SITE->fullname . ':' . get_string('LBL_TOOLS_IMPORT', self::PLUGIN_NAME));
            $PAGE->set_url($import_url);
            $PAGE->set_pagelayout('admin');
            $PAGE->set_heading($SITE->fullname);

            $PAGE->navbar->add(get_string('LBL_TOOLS_INDEX',  self::PLUGIN_NAME), $index_url);
            $PAGE->navbar->add(get_string('LBL_TOOLS_IMPORT', self::PLUGIN_NAME), null);

            navigation_node::override_active_url($admin_url);


            $data = new stdClass();

            $file_manager_options = array(
                'subdirs'        => 0,
                'maxfiles'		 => -1,
				'accepted_types' => '*.xml',
                'return_types'   => FILE_INTERNAL);

            // Fix up the form. Have not determined yet whether this is a
            // GET or POST, but the form will be used in either case.
            $mform = new enrol_shebang_tools_import_form($import_url, array('data' => $data, 'options' => $file_manager_options));
            // Use the form methods to determine the request method
            if (!$mform->is_submitted()) {

                echo $OUTPUT->header();
                echo $OUTPUT->heading_with_help('SHEBanG File Import', 'LBL_TOOLS_IMPORT', self::PLUGIN_NAME);

                // GET request, look for any query str params
                $action = optional_param('action', null, PARAM_ALPHA);
                $itemid = optional_param('itemid', null, PARAM_INT);

                // Validate the action and itemid
                if (   (!empty($action) && !in_array($action, $this->_valid_actions, true))
                    || empty($itemid) || !is_number($itemid)) {
                    $action = null;
                }

                if (null == $action) {

                    // Display the form with a filepicker and a list
                    // of files in this plugin's filearea
                    echo $OUTPUT->container_start();
                    $mform->display();
                    echo $PAGE->get_renderer(self::PLUGIN_NAME, 'tools')->enrol_shebang_tools_filelist();
                    echo $OUTPUT->container_end();

                } else {

                    switch ($action) {

                        case 'import':

                            $area_files  = get_file_storage()->get_area_files($system_context->id, self::PLUGIN_NAME, self::PLUGIN_FILEAREA, $itemid, null, false);
                            if (null != ($stored_file = array_shift($area_files))) {

                                // Now do the import work and emit some feedback
                                echo $OUTPUT->box_start();

                                $feedback = new progress_bar('shebang_pb', 500, true);
                                $plugin   = new enrol_shebang_processor();
                                $plugin->import_lmb_file($stored_file, $feedback);

                                echo $OUTPUT->box_end();
                                echo $OUTPUT->continue_button($index_url);

                                break;


                            } else {

                                // File not found
                                echo $OUTPUT->box(get_string('INF_TOOLS_IMPORT_FILENOTFOUND', self::PLUGIN_NAME));
                                echo $OUTPUT->continue_button($index_url);

                            }

                            break;

                        case 'delete':

                            // Delete the file
                            get_file_storage()->delete_area_files($system_context->id, self::PLUGIN_NAME, self::PLUGIN_FILEAREA, $itemid);
                            // Re-display the file list
                            echo $OUTPUT->container_start();
                            $mform->display();
                            echo $PAGE->get_renderer(self::PLUGIN_NAME, 'tools')->enrol_shebang_tools_filelist();
                            echo $OUTPUT->container_end();

                            break;

                    } // switch ($action)

                }

                echo $OUTPUT->footer();

            } elseif ($mform->is_cancelled()) {

                // POST request, but cancel button clicked. Clear out draft
                // file area to remove unused uploads, then send user back
                // to tools index.
                get_file_storage()->delete_area_files(context_user::instance($USER->id)->id, 'user', 'draft', file_get_submitted_draft_itemid('files_filemanager'));
                redirect($index_url);

             } elseif(null != ($formdata = $mform->get_data())) {

                 // POST request, submit button clicked. Save any uploaded
                 // files from the draft area into this module's filearea,
                 // keeping the (draft) itemid -- do this to prevent file
                 // from being stepped on from subsequent uploads.
                 file_save_draft_area_files($formdata->files_filemanager, $system_context->id, self::PLUGIN_NAME, self::PLUGIN_FILEAREA, $formdata->files_filemanager);
                 // File is in the module's upload filearea under the system
                 // context, so tidy up the user's draft area using the same
                 // itemid value
                 get_file_storage()->delete_area_files(context_user::instance($USER->id)->id, 'user', 'draft', $formdata->files_filemanager);
                 // At this point we should issue a redirect so the user-agent
                 // will GET a new page for two reasons: so the last request
                 // is not a POST in case the user clicks the browser refresh
                 // button; and a new draft itemid will be generated so that
                 // a subsequent upload doesn't overwrite the file just sent.
                 redirect($import_url);

             }

        } // handle_request


    } // class



    /**
     * The tool interface -- moodleform class definition for the plugin
     */
    class enrol_shebang_tools_import_form extends moodleform
    {

        const PLUGIN_NAME                       = 'enrol_shebang';


        /**
         * Define the form's contents
         *
         * @access public
         * @return void
         */
        public function definition()
        {

            $this->_form->addElement('header', 'general', get_string('LBL_TOOLS_IMPORT_UPLOAD', self::PLUGIN_NAME));
            $this->_form->addElement('filepicker', 'files_filemanager', null, null, $this->_customdata['options']);
            $this->add_action_buttons(true, get_string('LBL_TOOLS_IMPORT_SAVE', self::PLUGIN_NAME));

            $this->set_data($this->_customdata['data']);

        } // definition


    } // class
