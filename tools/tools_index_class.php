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


    /**
     * Tool definition class
     */
    class enrol_shebang_tools_index
    {

        /**
         * Tool name
         * @var string
         * @access public
         */
        public $name    = 'Index';

        /**
         * Tool action
         *
         * @var string
         * @access public
         */
        public $action  = 'index';

        /**
         * Tool description
         *
         * @var string
         * @access public
         */
        public $desc    = 'List the tools available';



        /**
         * Tool page request handler (GET & POST)
         *
         * @access  public
         * @return  void
         * @uses    $CFG, $SITE, $OUTPUT, $PAGE
         */
        public function handle_request()
        {
            global $CFG, $SITE, $OUTPUT, $PAGE;



            // POST or GET, just display the index (list) interface

            $admin_url  = new moodle_url("{$CFG->wwwroot}/admin/settings.php", array('section' => 'enrolsettingsshebang'));
            $index_url  = new moodle_url(enrol_shebang_processor::PLUGIN_PATH . '/tools.php');

            $PAGE->set_context(context_system::instance());
            $PAGE->set_title($SITE->fullname . ':' . get_string('LBL_TOOLS_INDEX', enrol_shebang_processor::PLUGIN_NAME));
            $PAGE->set_url($index_url);
            $PAGE->set_pagelayout('admin');
            $PAGE->set_heading($SITE->fullname);

            $PAGE->navbar->add(get_string('LBL_TOOLS_INDEX',  enrol_shebang_processor::PLUGIN_NAME), null);

            navigation_node::override_active_url($admin_url);

            echo $OUTPUT->header();

            echo $OUTPUT->heading(get_string('LBL_TOOLS_INDEX', enrol_shebang_processor::PLUGIN_NAME));

            $table = new html_table();
            $table->attributes = array('style' => 'width: 100%;');
            $table->align = array('right', 'left');
            $table->wrap = array('wrap', '');
            $table->head = array(get_string('name'), get_string('description'));
            $table->data = array();

            // List the tools present in the tools dir
            foreach(glob(dirname(__FILE__) . '/tools_*_class.php') as $file_path) {

                $name_parts = explode('_', basename($file_path, '.php'));
                if (($task = $name_parts[1]) == 'index') continue;

                include_once($file_path);

                $tool_class_name = enrol_shebang_processor::PLUGIN_NAME . "_tools_{$task}";
                $tool = new $tool_class_name();
                $tool_url = new moodle_url(enrol_shebang_processor::PLUGIN_PATH . "/tools.php", array('task' => $tool->task));
                $table->data[] = array("<a href=\"" . $tool_url->out() . "\">{$tool->name}</a>", $tool->desc);

            }
            echo html_writer::table($table);

            echo $OUTPUT->footer();

        } // handle_request


    } // class
