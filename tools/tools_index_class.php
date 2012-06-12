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


    /**
     * Tool definition class
     * 
     */
    class enrol_shebang_tools_index
    {

        /**
         * Tool name
         * @var string
         */
        public $name    = 'Index';
        /**
         * Tool action
         * 
         * @var string
         */
        public $action  = 'index';
        /**
         * Tool description
         * 
         * @var string
         */
        public $desc    = 'List the tools available';
        
        
        
        /**
         * Tool page request handler (GET & POST)
         * 
         * @return  void
         * @uses    $CFG
         * @uses    $SITE
         */
        public function handle_request()
        {
            global $CFG, $SITE;
            

            
            // Fix up the nav breadcrumbs
            $pagename       = get_string('LBL_TOOLS_INDEX', enrolment_plugin_shebang::PLUGIN_NAME);
            
            $navlinks       = array();
            $navlinks[]     = array('name' => $pagename, 'link' => null, 'type' => 'misc');
            $navigation     = build_navigation($navlinks);

            // Start the page
            print_header($SITE->fullname . ': ' . $pagename, $SITE->fullname . ': ' . $pagename, $navigation);
            echo '<div class="plugin">';

            // POST or GET, just display the index (list) interface
            print_heading_with_help(get_string('LBL_TOOLS_INDEX', enrolment_plugin_shebang::PLUGIN_NAME), 'tools', enrolment_plugin_shebang::PLUGIN_NAME);
            
            echo '<p align="center">';
            

            $table = new stdClass();
            $table->tablealign = 'center';
            $table->cellpadding = 5;
            $table->cellspacing = 0;
            $table->width = '80%';
            $table->align = array('right', 'left');
            $table->wrap = array('wrap', '');
            $table->head = array(get_string('name'), get_string('description'));
            $table->data = array();
            
            // List the tools present in the tools dir
            foreach(glob(dirname(__FILE__) . '/tools_*_class.php') as $file_path) {
                
                $name_parts = explode('_', basename($file_path, '.php'));
                if (($action = $name_parts[1]) == 'index') continue;
                
                include_once($file_path);

                $tool_class_name = enrolment_plugin_shebang::PLUGIN_NAME . "_tools_{$action}";
                $tool = new $tool_class_name();

                $table->data[] = array("<a href=\"{$CFG->wwwroot}/" . enrolment_plugin_shebang::PLUGIN_PATH . "/tools.php?action={$action}\">{$tool->name}</a>",
                                        $tool->desc);
                
            }
            print_table($table);

            
            echo '</p>';
            // Close up the page
            echo '</div>';
            print_footer();
            
        }
        
    }


