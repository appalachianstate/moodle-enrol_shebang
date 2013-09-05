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
     * Enrol plugin class
     */
    class enrol_shebang_plugin extends enrol_plugin
    {


        const PLUGIN_NAME                       = 'enrol_shebang';
        const PLUGIN_PATH                       = '/enrol/shebang';



        /**
         * @see enrol_plugin::allow_unenrol()
         */
        public function allow_unenrol(stdClass $instance)
        {

            return true;

        }



        /**
         * @see enrol_plugin::allow_unenrol_user()
         */
        public function allow_unenrol_user(stdClass $instance, stdClass $ue)
        {

            return true;

        }



        /**
         * @see enrol_plugin::get_user_enrolment_actions()
         */
        public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue)
        {

            $actions = array();

            // Check for bailout, simplify it recognizing this plugin allows
            // manual unenrol given the capability is granted
            if (!has_capability("enrol/shebang:unenrol", $manager->get_context())) {
                return $actions;
            }

            // Set the user enrol id amongst the page's
            // URL query string params
            $params = $manager->get_moodlepage()->url->params();
            $params['ue'] = $ue->id;
            $url = new moodle_url('/enrol/unenroluser.php', $params);

            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url, array('class' => 'unenrollink'));

            return $actions;

        } // get_user_enrolment_actions



        /**
         * @see enrol_plugin::get_newinstance_link()
         */
        public function get_newinstance_link($courseid)
        {
            global $DB;


            // Only one enrol instance per course
            if ($DB->record_exists('enrol', array('courseid' => $courseid, 'enrol' => $this->get_name()))) {
                return null;
            }

            if (!has_capability('moodle/course:enrolconfig', context_course::instance($courseid))) {
                return null;
            }

            return new moodle_url(self::PLUGIN_PATH . '/add.php', array('id' => $courseid));

        } // get_newinstance_link



        /**
         * @see enrol_plugin::course_edit_validation()
         */
        public function course_edit_validation($instance, array $data, $context)
        {
            global $DB;


            // Validate the course idnumber. If no instance
            // it is a new course, so nothing to do

            $errors = array();

            if (null == $instance) {
                return $errors;
            }

            $idnumber = trim($data['idnumber']);
            if (empty($idnumber)) {
                $errors['idnumber'] = get_string('missingfield', 'error', 'idnumber');
                return $errors;
            }

            // Check if idnumber is used elsewhere
            if ($DB->record_exists_select('course', 'idnumber = :idnumber AND id != :id', array('idnumber' => $idnumber, 'id' => $data['id']))) {
                $errors['idnumber'] = get_string('idnumbertaken', 'error');
            }

            return $errors;

        } // course_edit_validation



        /**
         * @see enrol_plugin::cron()
         */
        public function cron()
        {
            global $CFG;
            require_once(dirname(__FILE__) . '/locallib.php');


            mtrace(get_string('INF_CRON_START', self::PLUGIN_NAME));

            // Monitor message activity
            try {
                $processor = new enrol_shebang_processor();
                $processor->cron_monitor_activity();
            }
            catch (Exception $exc) {
                $info = get_exception_info($exc);
                mtrace(bootstrap_renderer::early_error($info->message, $info->moreinfourl, $info->link, $info->backtrace, $info->debuginfo, $info->errorcode));
            }


            // Next task(s)...


            // Our work is done here
            mtrace(get_string('INF_CRON_FINISH', self::PLUGIN_NAME));

        } // cron


    } // class
