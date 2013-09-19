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
     * This will define the widget argument passed to the
     * render() method, and the callback is determined by
     * the widget's class name prefixed withe 'render_'.
     */
    class enrol_shebang_tools_filelist implements renderable
    {

        /**
         * An array of the files in the uploads area
         *
         * @var array
         * @access protected
         */
        protected $_filelist;


        /**
         * Constructor
         *
         */
        function __construct()
        {
            $this->_filelist = get_file_storage()
            ->get_area_files(context_system::instance()->id,
              enrol_shebang_processor::PLUGIN_NAME, enrol_shebang_tools_import::PLUGIN_FILEAREA, false, 'timecreated');
        }


        /**
         * Accessor for $filelist
         *
         * @return array:
         */
        public function get_filelist()
        {
            return $this->_filelist;
        }

    } // class



    /**
     * Custom renderer for this plugin's tools sub-component.
     *
     */
    class enrol_shebang_tools_renderer extends plugin_renderer_base
    {

        /**
         * Options used when working up the HTML
         *
         * @var array
         * @access private
         */
        private $_options = array('action_links' => true);


        /**
         * Print a backup files tree
         * @param array $options
         * @return string
         */
        public function enrol_shebang_tools_filelist(array $options = null)
        {

            if (!empty($options)) {
                foreach ($options as $key => $value) {
                    if (key_exists($key, $this->_options)) {
                        $this->_options[$key] = $value;
                    }
                }
            }

            return $this->render(new enrol_shebang_tools_filelist());

        }


        /**
         * Emit the HTML to display the list of files in the uploads area.
         * This method is called by the parent class' render() method. It
         * passes in an object containing the list of files.
         *
         * @param  enrol_shebang_tools_filelist    $widget
         * @return string                          The rendered HTML
         */
        public function render_enrol_shebang_tools_filelist(enrol_shebang_tools_filelist $widget)
        {

            // Default table set up

            $table         = new html_table();
            $table->width  = '100%';
            $table->attributes['class'] = 'generaltable';

            $table->head  = array(get_string('file'), get_string('time'), get_string('size'));
            $table->data  = array();


            if ((boolean)$this->_options['action_links']) {
                $table->head[] = ' ';
                $table->head[] = ' ';
            }

            foreach ($widget->get_filelist() as $file) {

                if ($file->is_directory()) {
                    continue;
                }

                // Default row elements
                $row = array($file->get_filename(), userdate($file->get_timemodified()), display_size($file->get_filesize()));

                // Optional row elements

                if ((boolean)$this->_options['action_links']) {
                    // Each row in the table needs something to indicate
                    // uniquely which file the user wants to import, by
                    // a link (GET) with query str values, or by button
                    // (POST) with form values.
                    $params = array('task' => 'import', 'action' => 'import',
                                    'itemid' => $file->get_itemid());
                    $import_url = new moodle_url(enrol_shebang_processor::PLUGIN_PATH . '/tools.php', $params);
                    $row[] = html_writer::link($import_url, get_string('LBL_TOOLS_IMPORT_LINK_IMPORT', enrol_shebang_processor::PLUGIN_NAME));
                    $params = array('task' => 'import', 'action' => 'delete',
                                    'itemid' => $file->get_itemid());
                    $delete_url = new moodle_url(enrol_shebang_processor::PLUGIN_PATH . '/tools.php', $params);
                    $row[] = html_writer::link($delete_url, get_string('LBL_TOOLS_IMPORT_LINK_DELETE', enrol_shebang_processor::PLUGIN_NAME));
                }

                $table->data[] = $row;

            } // foreach

            $html = html_writer::table($table);

            return $html;

        } // render_enrol_shebang_tools_filelist

    } // class
