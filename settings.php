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

    require     (dirname(__FILE__) . '/version.php');
    require_once(dirname(__FILE__) . '/lib.php');
    require_once("{$CFG->dirroot}/course/lib.php");

    define('PLUGIN_NAME', 'enrol_shebang');
    define('PLUGIN_PATH', '/enrol/shebang');


    /**
     * Index (in order of appearance) of config values
     *
     * logging_onlyerrors
     * logging_logxml
     * logging_nologlock
     *
     * secure_username
     * secure_passwd
     * secure_method
     *
     * monitor_enabled
     * monitor_weekdays_0 ... monitor_weekdays_6
     * monitor_start_hour,
     * monitor_start_min
     * monitor_stop_hour,
     * monitor_stop_min
     * monitor_threshold
     * monitor_emails
     *
     * person_create
     * person_delete
     * person_username
     * person_username_failsafe
     * person_auth_method
     * person_shib_domain
     * person_password
     * person_password_changes
     * person_firstname_changes
     * person_lastname_changes
     * person_telephone
     * person_telephone_changes
     * person_address
     * person_address_changes
     * person_locality
     * person_locality_default
     * person_country
     * person_idnumber_sctid
     *
     * course_category
     * course_category_id
     * course_sections_equal_weeks
     * course_fullname_pattern
     * course_fullname_uppercase
     * course_fullname_changes
     * course_shortname_pattern
     * course_shortname_uppercase
     * course_shortname_changes
     * course_hidden
     * course_parent_striplead
     *
     * crosslist_enabled
     * crosslist_method
     * crosslist_groups
     * crosslist_fullname_prefix
     * crosslist_shortname_prefix
     * crosslist_hide_on_parent
     *
     * enroll_rolemap_01
     * enroll_rolemap_02
     * enroll_rolemap_03
     * enroll_rolemap_04
     * enroll_rolemap_05
     * enroll_rolemap_06
     * enroll_rolemap_07
     * enroll_rolemap_08
     */

    /* This file is included from admin/settings/plugins.php inside a
     * foreach loop with the $enrol => $enrolpath vars available. The
     * $settings var is assigned immediately before the include, with
     * $settings = new admin_settingpage(....
     */
    if ($ADMIN->fulltree) {

        $tools_url = new moodle_url(PLUGIN_PATH . '/tools.php');
        $argball = new stdClass();
        $argball->a = $OUTPUT->help_icon('LBL_DISCLAIMER', PLUGIN_NAME, get_string('LBL_DISCLAIMER', PLUGIN_NAME));
        $argball->b = $tools_url->out();
        $argball->c = get_string('LBL_VERSION', PLUGIN_NAME, $plugin->release);

        $settings->add(new admin_setting_heading(PLUGIN_NAME . '_description', '', get_string('description', PLUGIN_NAME, $argball)));

        /*
         * Logging
         */
        $settings->add(new admin_setting_heading(PLUGIN_NAME . '_logging', get_string('LBL_LOGGING', PLUGIN_NAME) . $OUTPUT->help_icon('LBL_LOGGING', PLUGIN_NAME), ''));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/logging_onlyerrors', get_string('LBL_LOGGING_ONLYERRORS', PLUGIN_NAME), '', '0'));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/logging_logxml',     get_string('LBL_LOGGING_LOGXML',     PLUGIN_NAME), '', '1'));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/logging_nologlock',  get_string('LBL_LOGGING_NOLOGLOCK',  PLUGIN_NAME), '', '0'));


        /*
         * (Connection) Security
         */
        $settings->add(new admin_setting_heading(PLUGIN_NAME . '_secure', get_string('LBL_SECURE', PLUGIN_NAME) . $OUTPUT->help_icon('LBL_SECURE', PLUGIN_NAME), ''));

        $settings->add(new admin_setting_configtext(PLUGIN_NAME . '/secure_username', get_string('LBL_SECURE_USERNAME', PLUGIN_NAME), '', ''));

        $settings->add(new admin_setting_configpasswordunmask(PLUGIN_NAME  . '/secure_passwd',   get_string('LBL_SECURE_PASSWD',   PLUGIN_NAME), '', ''));

        $settings->add(new admin_setting_configselect(PLUGIN_NAME          . '/secure_method',   get_string('LBL_SECURE_METHOD',   PLUGIN_NAME), '', enrol_shebang_plugin::OPT_SECURE_METHOD_DIGEST,
          array(
            enrol_shebang_plugin::OPT_SECURE_METHOD_BASIC  => get_string('LBL_SECURE_METHOD_BASIC',  PLUGIN_NAME),
            enrol_shebang_plugin::OPT_SECURE_METHOD_DIGEST => get_string('LBL_SECURE_METHOD_DIGEST', PLUGIN_NAME)
          )));


        /*
         * (Activity) Monitor
         */
        $settings->add(new admin_setting_heading(PLUGIN_NAME . '_monitor', get_string('LBL_MONITOR', PLUGIN_NAME) . $OUTPUT->help_icon('LBL_MONITOR', PLUGIN_NAME), ''));

        $settings->add(new admin_setting_configmulticheckbox(PLUGIN_NAME . '/monitor_weekdays', get_string('LBL_MONITOR_WEEKDAYS', PLUGIN_NAME), '', '',
            array(
              '0' => get_string('sun', 'calendar'),
              '1' => get_string('mon', 'calendar'),
              '2' => get_string('tue', 'calendar'),
              '3' => get_string('wed', 'calendar'),
              '4' => get_string('thu', 'calendar'),
              '5' => get_string('fri', 'calendar'),
              '6' => get_string('sat', 'calendar')
            )));
        $settings->add(new admin_setting_configtime(PLUGIN_NAME . '/monitor_start_hour', PLUGIN_NAME . '/monitor_start_min', get_string('LBL_MONITOR_START', PLUGIN_NAME), get_string('HELP_MONITOR_START', PLUGIN_NAME), array('h' => enrol_shebang_plugin::DEF_MONITOR_START_HOUR, 'm' => enrol_shebang_plugin::DEF_MONITOR_START_MIN)));

        $settings->add(new admin_setting_configtime(PLUGIN_NAME . '/monitor_stop_hour',  PLUGIN_NAME . '/monitor_stop_min',  get_string('LBL_MONITOR_STOP',  PLUGIN_NAME), get_string('HELP_MONITOR_STOP',  PLUGIN_NAME), array('h' => enrol_shebang_plugin::DEF_MONITOR_STOP_HOUR, 'm' => enrol_shebang_plugin::DEF_MONITOR_STOP_MIN)));

        $settings->add(new admin_setting_configselect(PLUGIN_NAME . '/monitor_threshold', get_string('LBL_MONITOR_THRESHOLD', PLUGIN_NAME), get_string('HELP_MONITOR_THRESHOLD', PLUGIN_NAME), enrol_shebang_plugin::DEF_MONITOR_THRESHOLD,
            array('15' => '15', '30' => '30', '45' => '45', '60' => '60', '120' => '120', '180' => '180', '240' => '240')));

        $settings->add(new admin_setting_configtext(PLUGIN_NAME . '/monitor_emails', get_string('LBL_MONITOR_EMAILS', PLUGIN_NAME), get_string('HELP_MONITOR_EMAILS', PLUGIN_NAME), ''));


        /*
         * Person messages
         */
        $settings->add(new admin_setting_heading(PLUGIN_NAME . '_person', get_string('LBL_PERSON', PLUGIN_NAME) . $OUTPUT->help_icon('LBL_PERSON', PLUGIN_NAME), ''));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/person_create',  get_string('LBL_PERSON_CREATE',   PLUGIN_NAME), '', '1'));

        $settings->add(new admin_setting_configselect(PLUGIN_NAME . '/person_delete',  get_string('LBL_PERSON_DELETE', PLUGIN_NAME), '', '',
            array(
                ''											    => get_string('LBL_PERSON_DELETE_NOACTION', PLUGIN_NAME),
                enrol_shebang_plugin::OPT_PERSON_DELETE_DELETE  => get_string('LBL_PERSON_DELETE_DELETE',   PLUGIN_NAME),
                enrol_shebang_plugin::OPT_PERSON_DELETE_UNENROL => get_string('LBL_PERSON_DELETE_UNENROL',  PLUGIN_NAME)
        )));

        $settings->add(new admin_setting_configselect(PLUGIN_NAME   . '/person_username', get_string('LBL_PERSON_USERNAME', PLUGIN_NAME), '', enrol_shebang_plugin::OPT_PERSON_USERNAME_USERID_LOGON,
            array(
              enrol_shebang_plugin::OPT_PERSON_USERNAME_EMAIL        => get_string('LBL_PERSON_USERNAME_EMAIL',        PLUGIN_NAME),
              enrol_shebang_plugin::OPT_PERSON_USERNAME_USERID_EMAIL => get_string('LBL_PERSON_USERNAME_USERID_EMAIL', PLUGIN_NAME),
              enrol_shebang_plugin::OPT_PERSON_USERNAME_USERID_LOGON => get_string('LBL_PERSON_USERNAME_USERID_LOGON', PLUGIN_NAME),
              enrol_shebang_plugin::OPT_PERSON_USERNAME_USERID_SCTID => get_string('LBL_PERSON_USERNAME_USERID_SCTID', PLUGIN_NAME)
            )));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/person_username_failsafe',  get_string('LBL_PERSON_USERNAME_FAILSAFE',   PLUGIN_NAME), '', ''));

        $userOptions = array();
        $authModules = get_enabled_auth_plugins();
        foreach ($authModules as $mod) {
            $userOptions[$mod] = get_string('pluginname', "auth_{$mod}");
        }
        $settings->add(new admin_setting_configselect(PLUGIN_NAME   . '/person_auth_method', get_string('LBL_PERSON_AUTH_METHOD', PLUGIN_NAME), '', enrol_shebang_plugin::DEF_PERSON_AUTH_METHOD, $userOptions));

        $settings->add(new admin_setting_configtext(PLUGIN_NAME     . '/person_shib_domain', get_string('LBL_PERSON_SHIB_DOMAIN', PLUGIN_NAME), '', ''));

        $settings->add(new admin_setting_configselect(PLUGIN_NAME   . '/person_password', get_string('LBL_PERSON_PASSWORD', PLUGIN_NAME), '', '',
            array(
              ''                                                     => get_string('LBL_PERSON_PASSWORD_RANDOM',       PLUGIN_NAME),
              enrol_shebang_plugin::OPT_PERSON_USERNAME_USERID_LOGON => get_string('LBL_PERSON_PASSWORD_USERID_LOGON', PLUGIN_NAME),
              enrol_shebang_plugin::OPT_PERSON_PASSWORD_USERID_SCTID => get_string('LBL_PERSON_PASSWORD_USERID_SCTID', PLUGIN_NAME)
            )));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/person_password_changes',  get_string('LBL_PERSON_PASSWORD_CHANGES',  PLUGIN_NAME), '', '0'));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/person_firstname_changes', get_string('LBL_PERSON_FIRSTNAME_CHANGES', PLUGIN_NAME), '', '1'));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/person_lastname_changes',  get_string('LBL_PERSON_LASTNAME_CHANGES',  PLUGIN_NAME), '', '1'));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/person_telephone',         get_string('LBL_PERSON_TELEPHONE',         PLUGIN_NAME), '', '1'));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/person_telephone_changes', get_string('LBL_PERSON_TELEPHONE_CHANGES', PLUGIN_NAME), '', '1'));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/person_address',           get_string('LBL_PERSON_ADDRESS',           PLUGIN_NAME), '', '1'));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/person_address_changes',   get_string('LBL_PERSON_ADDRESS_CHANGES',   PLUGIN_NAME), '', '1'));

        $settings->add(new admin_setting_configselect(PLUGIN_NAME   . '/person_locality', get_string('LBL_PERSON_LOCALITY', PLUGIN_NAME), '', enrol_shebang_plugin::OPT_PERSON_LOCALITY_IFF,
            array(
                enrol_shebang_plugin::OPT_PERSON_LOCALITY_MSG => get_string('LBL_PERSON_LOCALITY_MSG', PLUGIN_NAME),
                enrol_shebang_plugin::OPT_PERSON_LOCALITY_DEF => get_string('LBL_PERSON_LOCALITY_DEF', PLUGIN_NAME),
                enrol_shebang_plugin::OPT_PERSON_LOCALITY_IFF => get_string('LBL_PERSON_LOCALITY_IFF', PLUGIN_NAME)
            )));

        $settings->add(new admin_setting_configtext(PLUGIN_NAME     . '/person_locality_default', get_string('LBL_PERSON_LOCALITY_DEFAULT', PLUGIN_NAME), '', ''));

        $settings->add(new admin_setting_configselect(PLUGIN_NAME   . '/person_country', get_string('LBL_PERSON_COUNTRY', PLUGIN_NAME), '', enrol_shebang_plugin::DEF_PERSON_COUNTRY, get_string_manager()->get_list_of_countries()));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/person_idnumber_sctid',   get_string('LBL_PERSON_IDNUMBER_SCTID',   PLUGIN_NAME), '', '0'));

        /*
         * Course-section (group) messages
         */
        $settings->add(new admin_setting_heading(PLUGIN_NAME . '_course', get_string('LBL_COURSE', PLUGIN_NAME) . $OUTPUT->help_icon('LBL_COURSE', PLUGIN_NAME), ''));

        $settings->add(new admin_setting_configselect(PLUGIN_NAME . '/course_category', get_string('LBL_COURSE_CATEGORY', PLUGIN_NAME), '', enrol_shebang_plugin::OPT_COURSE_CATEGORY_TERM,
          array(
            enrol_shebang_plugin::OPT_COURSE_CATEGORY_TERM => get_string('LBL_COURSE_CATEGORY_TERM', PLUGIN_NAME),
            enrol_shebang_plugin::OPT_COURSE_CATEGORY_DEPT => get_string('LBL_COURSE_CATEGORY_DEPT', PLUGIN_NAME),
            enrol_shebang_plugin::OPT_COURSE_CATEGORY_NEST => get_string('LBL_COURSE_CATEGORY_NEST', PLUGIN_NAME),
            enrol_shebang_plugin::OPT_COURSE_CATEGORY_PICK => get_string('LBL_COURSE_CATEGORY_PICK', PLUGIN_NAME)
          )));

        $categoryArray = array(); $parentArray = array();
        make_categories_list($categoryArray, $parentArray);
        $settings->add(new admin_setting_configselect(PLUGIN_NAME . '/course_category_id', get_string('LBL_COURSE_CATEGORY_ID', PLUGIN_NAME), '', '', $categoryArray));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/course_sections_equal_weeks', get_string('LBL_COURSE_SECTIONS_EQUAL_WEEKS', PLUGIN_NAME), '', '1'));

        $token_list = '';
        foreach (enrol_shebang_plugin::$courseNameTokens as $token) {
            $ar = explode('/', $token);
            if ($token_list) $token_list .= ', ';
            $token_list .= $ar[1];
        }
        $settings->add(new admin_setting_configtext(PLUGIN_NAME . '/course_fullname_pattern', get_string('LBL_COURSE_FULLNAME_PATTERN', PLUGIN_NAME), $token_list, ''));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/course_fullname_uppercase', get_string('LBL_COURSE_FULLNAME_UPPERCASE', PLUGIN_NAME), '', '1'));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/course_fullname_changes', get_string('LBL_COURSE_FULLNAME_CHANGES', PLUGIN_NAME), '', '1'));

        $token_list = '';
        foreach (enrol_shebang_plugin::$courseNameTokens as $token) {
            $ar = explode('/', $token);
            if ($token_list) $token_list .= ', ';
            $token_list .= $ar[1];
        }
        $settings->add(new admin_setting_configtext(PLUGIN_NAME . '/course_shortname_pattern', get_string('LBL_COURSE_SHORTNAME_PATTERN', PLUGIN_NAME), $token_list, ''));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/course_shortname_uppercase', get_string('LBL_COURSE_SHORTNAME_UPPERCASE', PLUGIN_NAME), '', '1'));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/course_shortname_changes', get_string('LBL_COURSE_SHORTNAME_CHANGES', PLUGIN_NAME), '', '1'));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/course_hidden', get_string('LBL_COURSE_HIDDEN', PLUGIN_NAME), '', '0'));

        $settings->add(new admin_setting_configselect(PLUGIN_NAME . '/course_parent_striplead', get_string('LBL_COURSE_PARENT_STRIPLEAD', PLUGIN_NAME), '', '0',
            array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10')));


        /*
         * Crosslist course messages
         */
        $settings->add(new admin_setting_heading(PLUGIN_NAME . '_crosslist', get_string('LBL_CROSSLIST', PLUGIN_NAME) . $OUTPUT->help_icon('LBL_CROSSLIST', PLUGIN_NAME), ''));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/crosslist_enabled', get_string('LBL_CROSSLIST_ENABLED', PLUGIN_NAME), '', ''));

        $settings->add(new admin_setting_configselect(PLUGIN_NAME   . '/crosslist_method', get_string('LBL_CROSSLIST_METHOD', PLUGIN_NAME), '', enrol_shebang_plugin::OPT_CROSSLIST_METHOD_META,
            array(
               enrol_shebang_plugin::OPT_CROSSLIST_METHOD_META  => get_string('LBL_CROSSLIST_METHOD_META',   PLUGIN_NAME),
               enrol_shebang_plugin::OPT_CROSSLIST_METHOD_MERGE => get_string('LBL_CROSSLIST_METHOD_MERGE',  PLUGIN_NAME)
            )));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/crosslist_groups', get_string('LBL_CROSSLIST_GROUPS', PLUGIN_NAME), '', ''));

        $settings->add(new admin_setting_configtext(PLUGIN_NAME . '/crosslist_fullname_prefix', get_string('LBL_CROSSLIST_FULLNAME_PREFIX', PLUGIN_NAME), '', ''));

        $settings->add(new admin_setting_configtext(PLUGIN_NAME . '/crosslist_shortname_prefix', get_string('LBL_CROSSLIST_SHORTNAME_PREFIX', PLUGIN_NAME), '', ''));

        $settings->add(new admin_setting_configcheckbox(PLUGIN_NAME . '/crosslist_hide_on_parent', get_string('LBL_CROSSLIST_HIDE_ON_PARENT', PLUGIN_NAME), '', ''));


        /*
         * Enrollment messages
         */
        $settings->add(new admin_setting_heading(PLUGIN_NAME . '_enroll', get_string('LBL_ENROLL', PLUGIN_NAME) . $OUTPUT->help_icon('LBL_ENROLL', PLUGIN_NAME), ''));

        $userOptions = array('' => get_string('LBL_ENROLL_ROLE_NOMAP', PLUGIN_NAME)) + get_assignable_roles(get_context_instance(CONTEXT_COURSE, SITEID));
        foreach(array('01','02','03','04','05','06','07','08') as $role_num) {
            $settings->add(new admin_setting_configselect(PLUGIN_NAME . '/enroll_rolemap_' . $role_num, get_string('LBL_ENROLL_ROLEMAP_' . $role_num, PLUGIN_NAME), '', '', $userOptions));
        }

    } // if ($ADMIN->fulltree)


