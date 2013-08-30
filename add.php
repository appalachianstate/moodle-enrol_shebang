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

    require('../../config.php');
    require_once("./lib.php");
    require_once("./add_form.php");



    $course_id = required_param('id', PARAM_INT);

    require_login($course_id);
    require_capability('moodle/course:enrolconfig', context_course::instance($course_id));

    $manage_page_url     = new moodle_url('/enrol/instances.php', array('id' => $course_id));
    $plugin_short_name   = 'shebang';
    $plugin_name         = 'enrol_shebang';

    if (!enrol_is_enabled($plugin_short_name)) {
        redirect($manage_page_url);
    }

    $plugin = enrol_get_plugin($plugin_short_name);
    if (null == $plugin->get_newinstance_link($course_id)) {
        // Either failed has_capability() checks or
        // enrol instance already present in course
        redirect($manage_page_url);
    }


    $PAGE->set_url('/enrol/{$plugin_short_name}/add.php', array('id' => $course_id));
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title(get_string('pluginname', $plugin_name));
    $PAGE->set_heading($COURSE->fullname);
    navigation_node::override_active_url($manage_page_url);


    // Prompt for (display current) idnumber
    $mform = new enrol_shebang_add_form(null, array('id' => $course_id, 'idnumber' => $COURSE->idnumber));

    if ($mform->is_cancelled()) {
        redirect($manage_page_url);
    } elseif ($data = $mform->get_data()) {
        $plugin->add_instance($COURSE, null);
        $DB->update_record('course', array('id' => $course_id, 'idnumber' => $data->idnumber));
        redirect($manage_page_url);
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', $plugin_name));
    $mform->display();
    echo $OUTPUT->footer();
