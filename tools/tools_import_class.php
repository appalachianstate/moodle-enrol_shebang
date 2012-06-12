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
     * @package     enrol/shebang
     */

    require_once($CFG->libdir.'/formslib.php');
    
    if (!defined('FORMID_FILE')) define ('FORMID_FILE', 'lmbfile');

    
    /**
     * Tool definition class
     *
     */
    class enrol_shebang_tools_import
    {

        /**
         * Tool name
         * @var string
         */
        public $name    = 'Import File';
        /**
         * Tool action
         * 
         * @var string
         */
        public $action  = 'import';
        /**
         * Tool description
         * 
         * @var string
         */
        public $desc    = 'Upload and import an LMB/IMS XML file into the database.';        
        

        
        /**
         * Tool page request handler (GETs & POSTs)
         * 
         * @return  void
         * @uses    $CFG
         * @uses    $SITE
         */
        public function handle_request()
        {
            global $CFG, $SITE;



            $pagename       = get_string('LBL_TOOLS_IMPORT', enrolment_plugin_shebang::PLUGIN_NAME);
            
            $navlinks       = array();
            $navlinks[]     = array('name' => get_string('LBL_TOOLS_INDEX', enrolment_plugin_shebang::PLUGIN_NAME),  'link' => "{$CFG->wwwroot}/". enrolment_plugin_shebang::PLUGIN_PATH . "/tools.php", 'type' => 'misc');
            $navlinks[]     = array('name' => get_string('LBL_TOOLS_IMPORT', enrolment_plugin_shebang::PLUGIN_NAME), 'link' => null, 'type' => 'misc');
            $navigation     = build_navigation($navlinks);            

            // Start the page
            print_header($SITE->fullname . ': ' . $pagename, $SITE->fullname . ': ' . $pagename, $navigation);
            echo '<div class="plugin">';

            
            
            if (false === ($data = data_submitted())) {

                // No data submitted, must be a GET
                print_heading_with_help(get_string('LBL_TOOLS_IMPORT', enrolment_plugin_shebang::PLUGIN_NAME), 'tools', enrolment_plugin_shebang::PLUGIN_NAME);
                // Display the form
                $mform = new enrol_shebang_tools_import_form("{$CFG->wwwroot}/" . enrolment_plugin_shebang::PLUGIN_PATH . "/tools.php?action={$this->action}");
                $mform->display();
   
            } elseif (isset($data->cancel)) {

                redirect("{$CFG->wwwroot}/" . enrolment_plugin_shebang::PLUGIN_PATH . "/tools.php");

            } else {
                
                // Handle the POSTed data
                if (!confirm_sesskey()) {
                    print_error('invalidsesskey', 'error');
                }

                echo "<p align=\"center\">";                    
                print_simple_box_start();
                
                // Use the upload_manager to check for the presence and size of the input
                // file--it will emit a message using notify() if no file was selected or
                // there is another problem, so don't need to print_error()
                $um = new upload_manager(FORMID_FILE, true, false, null, false, 0);
                if ($um->preprocess_files()) {

                    print_string('INF_TOOLS_IMPORT_BEGIN', enrolment_plugin_shebang::PLUGIN_PATH);
                    echo '<br />';
                    ob_flush(); flush();

                    $timestamp  = date("YmdHis");
                    $inputfile  = "{$CFG->dataroot}/" . enrolment_plugin_shebang::PLUGIN_NAME . enrolment_plugin_shebang::PLUGIN_DATADIR_IMPORT . "/upload_{$timestamp}.xml";
                    move_uploaded_file($um->files[FORMID_FILE]['tmp_name'], $inputfile);

                    // Do the work and emit some feedback
                    $plugin = new enrolment_plugin_shebang();
                    $plugin->import_lmb_file($inputfile, array($this, 'progress_callback'));

                } // $um->preprocess_files

                print_continue("{$CFG->wwwroot}/". enrolment_plugin_shebang::PLUGIN_PATH . "/tools.php");
                print_simple_box_end();
                echo '</p>';
                
            }
            
            // Close up the page
            echo '</div>';

            print_footer();            
            
        } // handle_request

        
        
        /**
         * Progress callback routine -- periodically receives status update with percentage completed
         * 
         * @param   string      $percentage
         * @return  void
         */
        public function progress_callback($percentage)
        {
            
            print_string('INF_TOOLS_IMPORT_STATUS', enrolment_plugin_shebang::PLUGIN_PATH, $percentage);
            echo '<br />';
            ob_flush(); flush();
            
        } // progress_callback

        
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
         * @uses $CFG
         */
        public function definition()
        {
            global $CFG;


            
            $this->_form->addElement('header', 'general', '');

            // The file upload/browse
            $this->set_upload_manager(new upload_manager(FORMID_FILE, true, false, null, false, 0));
            $this->_form->addElement('file', FORMID_FILE, get_string('LBL_TOOLS_IMPORT_FILE', enrolment_plugin_shebang::PLUGIN_PATH), array('size' => '40'));

            $this->add_action_buttons(true, get_string('LBL_TOOLS_IMPORT_SUBMIT', enrolment_plugin_shebang::PLUGIN_PATH));

        } // definition

    } // class

