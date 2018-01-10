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


            // Basic capability requirement
            if (!has_capability('moodle/course:enrolconfig', context_course::instance($courseid))) {
                return null;
            }

            // Only one enrol instance per course
            if ($DB->record_exists('enrol', array('courseid' => $courseid, 'enrol' => $this->get_name()))) {
                return null;
            }

            // Need the course to see if idnumber present
            // when checking this next capability
            $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
            $edit_idnumber = has_capability('moodle/course:changeidnumber', context_course::instance($courseid));

            if (empty($course->idnumber)) {
                if (!$edit_idnumber) {
                    return null;
                }
            } else {
                // Check if idnumber is used elsewhere
                $others = $DB->record_exists_select('course', 'idnumber = :idnumber AND id != :id', array('idnumber' => $course->idnumber, 'id' => $course->id));
                if ($others && !$edit_idnumber) {
                    return null;
                }
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
         * @see enrol_plugin::can_hide_show_instance()
         */
        public function can_hide_show_instance($instance)
        {

            $context = context_course::instance($instance->courseid);
            return has_capability('enrol/shebang:config', $context);

        }


        /***
         * {@inheritDoc}
         * @see enrol_plugin::is_cron_required()
         */
        public function is_cron_required()
        {
            return false;
        }


    } // class


    /**
     * Download files from the enrol_shebang plugin
     *
     * @param stdClass $course course object
     * @param stdClass $cm course module object
     * @param stdClass $context context object
     * @param string $filearea file area
     * @param array $args extra arguments
     * @param bool $forcedownload whether or not force download
     * @param array $options additional options affecting the file serving
     * @return mixed false if file not found, otherwise send file and exit
     */
    function enrol_shebang_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array())
    {

        if ($context->contextlevel != CONTEXT_SYSTEM) {
            return false;
        }

        // No anonymous access for this page
        require_login();

        // Must be a site administrator
        require_capability('moodle/site:config', context_system::instance());

        if (!$file = get_file_storage()->get_file_by_hash(sha1("/{$context->id}/enrol_shebang/{$filearea}/" . implode('/', $args)))) {
            return false;
        }

        if ($file->is_directory()) {
            return false;
        }

        // Send it
        send_stored_file($file, 0, 0, true, $options);

    }
