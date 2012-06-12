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

    require_once($CFG->libdir.'/formslib.php');
    require_once($CFG->libdir.'/uploadlib.php');



    /**
     * Tool definition class
     *
     */
    class enrol_shebang_tools_import
    {

        /**
         * Tool name
         * @var string
         * @access public
         */
        public $name    = 'Import File';
        /**
         * Tool action
         *
         * @var string
         * @access public
         */
        public $action  = 'import';
        /**
         * Tool description
         *
         * @var string
         * @access public
         */
        public $desc    = 'Upload and import an LMB/IMS XML file into the database.';



        /**
         * Tool page request handler (GETs & POSTs)
         *
         * @access  public
         * @return  void
         * @uses    $CFG, $SITE, $OUTPUT, $PAGE
         */
        public function handle_request()
        {
            global $CFG, $SITE, $OUTPUT, $PAGE;



            $context = get_system_context();

            $admin_url  = new moodle_url(enrol_shebang_plugin::PLUGIN_PATH . "/admin/settings.php", array('section' => 'enrolsettingsshebang'));
            $index_url  = new moodle_url(enrol_shebang_plugin::PLUGIN_PATH . '/tools.php');
            $import_url = new moodle_url(enrol_shebang_plugin::PLUGIN_PATH . '/tools.php', array('action' => 'import'));

            $PAGE->set_context($context);
            $PAGE->set_title($SITE->fullname . ':' . get_string('LBL_TOOLS_IMPORT', enrol_shebang_plugin::PLUGIN_NAME));
            $PAGE->set_url($admin_url);
            $PAGE->set_pagelayout('admin');
            $PAGE->set_heading($SITE->fullname);

            $PAGE->navbar->add(get_string('LBL_TOOLS_INDEX',  enrol_shebang_plugin::PLUGIN_NAME), $index_url);
            $PAGE->navbar->add(get_string('LBL_TOOLS_IMPORT', enrol_shebang_plugin::PLUGIN_NAME), null);

            navigation_node::override_active_url($admin_url);

            echo $OUTPUT->header();

            $data = new stdClass();
            $data->returnurl = $index_url;

            $options = array('subdirs'        => 0,
                             'maxfiles'		  => 1,
            				 'accepted_types' => '*.xml',
                             'return_types'   => FILE_INTERNAL);

            file_prepare_standard_filemanager($data, 'files', $options, $context, enrol_shebang_plugin::PLUGIN_NAME, 'uploads', 0);
            $mform = new enrol_shebang_tools_import_form($import_url, array('data' => $data, 'options' => $options));
            if (!$mform->is_submitted()) {
                $mform->display();
            } elseif ($mform->is_cancelled()) {
                redirect($index_url);
            } elseif($formdata = $mform->get_data()) {

                // File has already been uploaded by filemanager/filepicker and
                // placed in user draft area with an itemid (previously unused),
                // and with our maxfiles option of 1, we should only have only
                // the single file in this logical directory, but we need to know
                // the filename

                // Get the draft area itemid from the form data
                $draftarea_itemid = $formdata->files_filemanager;

                // Use that to get all the files (all one of them) in that
                // logical directory
                $draftarea_files = file_get_drafarea_files($draftarea_itemid, '/');

                // The 'list' property of the returned object is an array of file
                // info objects, each object having a filename property.
                if (!$draftarea_files->list) {
                    print_error('ERR_DATAFILE_NOFILE', enrol_shebang_plugin::PLUGIN_NAME, $index_url->out(true));
                }
                $filename = $draftarea_files->list[0]->filename;

                // Move the file from the draft area (newly uploaded) to an area
                // specific to our plugin and change the itemid to 0
                $formdata = file_postupdate_standard_filemanager($formdata, 'files', $options, $context, enrol_shebang_plugin::PLUGIN_NAME, 'uploads', 0);

                // Use a filestorage object to get the uploaded file
                $fs = get_file_storage();
                $file_instance = $fs->get_file($context->id, enrol_shebang_plugin::PLUGIN_NAME, 'uploads', 0, '/', $filename);

                // Because the actual filesystem location of the pool files is
                // restricted, we have to go out of band (read cheat) to get the
                // location.
                $contenthash = $file_instance->get_contenthash();
                $file_instance_path = (isset($CFG->filedir) ? $CFG->filedir : "{$CFG->dataroot}/filedir")
                                    . "/{$contenthash[0]}{$contenthash[1]}/{$contenthash[2]}{$contenthash[3]}/$contenthash";

                // Now do the import work and emit some feedback
                echo $OUTPUT->heading(get_string('LBL_TOOLS_IMPORT', enrol_shebang_plugin::PLUGIN_NAME));
                echo $OUTPUT->box_start();
                ob_flush(); flush();

                $feedback = new progress_bar('shebang_pb', 500, true);
                $plugin   = new enrol_shebang_plugin();
              //$plugin->import_lmb_file($file_instance_path, $feedback);

                echo $OUTPUT->continue_button(new moodle_url(enrol_shebang_plugin::PLUGIN_PATH . "/tools.php"));
                echo $OUTPUT->box_end();

            }

            echo $OUTPUT->footer();

        } // handle_request


    } // class



    /**
     * The tool interface -- moodleform class definition for the plugin
     */
    class enrol_shebang_tools_import_form extends moodleform
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

            $this->_form->addElement('header', 'general', get_string('LBL_TOOLS_IMPORT', enrol_shebang_plugin::PLUGIN_NAME));
            $this->_form->addElement('filepicker', 'files_filemanager', get_string('LBL_TOOLS_IMPORT_FILE', enrol_shebang_plugin::PLUGIN_NAME), null, $options);
            $this->_form->addElement('hidden', 'returnurl', $data->returnurl);

            $this->add_action_buttons(true, get_string('LBL_TOOLS_IMPORT_SUBMIT', enrol_shebang_plugin::PLUGIN_NAME));
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
