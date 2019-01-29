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
    require_once(dirname(__FILE__) . '/locallib.php');
    require_once("{$CFG->libdir}/accesslib.php");



    /**
     * Index (in order of appearance) of config values
     *
     * responses_200_on_error
     * responses_notify_on_error
     * responses_emails
     *
     * logging_onlyerrors
     * logging_logxml
     * logging_nologlock
     * logging_dirpath
     *
     * secure_username
     * secure_passwd
     * secure_method
     *
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
     * person_nickname_prefer
     * person_fullname_desc
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

        $tools_url = new moodle_url('/enrol/shebang/tools.php');
        $args = new stdClass();
        $args->a = get_string('LBL_VERSION', $plugin->component, $plugin->release);
        $args->b = $tools_url->out();

        $settings->add(new admin_setting_heading("{$plugin->component}_description", get_string('LBL_DISCLAIMER', $plugin->component) . $OUTPUT->help_icon('LBL_DISCLAIMER', $plugin->component), get_string('description', $plugin->component, $args)));

        /*
         * Server Responses
         */
        $settings->add(new admin_setting_heading("{$plugin->component}_responses",                        get_string('LBL_RESPONSES',                 $plugin->component), ''));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/responses_200_on_error",    get_string('LBL_RESPONSES_200_ON_ERROR',    $plugin->component), get_string('HELP_RESPONSES_200_ON_ERROR',    $plugin->component), '0'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/responses_notify_on_error", get_string('LBL_RESPONSES_NOTIFY_ON_ERROR', $plugin->component), get_string('HELP_RESPONSES_NOTIFY_ON_ERROR', $plugin->component), '0'));
        $settings->add(new admin_setting_configtext("{$plugin->component}/responses_emails",              get_string('LBL_RESPONSES_EMAILS',          $plugin->component), get_string('HELP_RESPONSES_EMAILS',          $plugin->component), ''));


        /*
         * Logging
         */
        $settings->add(new admin_setting_heading("{$plugin->component}_logging",                   get_string('LBL_LOGGING',            $plugin->component) . $OUTPUT->help_icon('LBL_LOGGING', $plugin->component), ''));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/logging_onlyerrors", get_string('LBL_LOGGING_ONLYERRORS', $plugin->component), '', '0'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/logging_logxml",     get_string('LBL_LOGGING_LOGXML',     $plugin->component), '', '1'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/logging_nologlock",  get_string('LBL_LOGGING_NOLOGLOCK',  $plugin->component), '', '0'));
        $settings->add(new admin_setting_configdirectory("{$plugin->component}/logging_dirpath",   get_string('LBL_LOGGING_DIRPATH',    $plugin->component), '', ''));


        /*
         * (Connection) Security
         */
        $settings->add(new admin_setting_heading("{$plugin->component}_secure",                     get_string('LBL_SECURE',          $plugin->component) . $OUTPUT->help_icon('LBL_SECURE', $plugin->component), ''));
        $settings->add(new admin_setting_configtext("{$plugin->component}/secure_username",         get_string('LBL_SECURE_USERNAME', $plugin->component), '', ''));
        $settings->add(new admin_setting_configpasswordunmask("{$plugin->component}/secure_passwd", get_string('LBL_SECURE_PASSWD',   $plugin->component), '', ''));
        $settings->add(new admin_setting_configselect("{$plugin->component}/secure_method",         get_string('LBL_SECURE_METHOD',   $plugin->component), '', enrol_shebang_processor::OPT_SECURE_METHOD_DIGEST,
          array(
            enrol_shebang_processor::OPT_SECURE_METHOD_BASIC  => get_string('LBL_SECURE_METHOD_BASIC',  $plugin->component),
            enrol_shebang_processor::OPT_SECURE_METHOD_DIGEST => get_string('LBL_SECURE_METHOD_DIGEST', $plugin->component)
          )));


        /*
         * (Activity) Monitor
         */
        $settings->add(new admin_setting_heading("{$plugin->component}_monitor",                get_string('LBL_MONITOR',           $plugin->component) . $OUTPUT->help_icon('LBL_MONITOR', $plugin->component), ''));
        $settings->add(new admin_setting_configselect("{$plugin->component}/monitor_threshold", get_string('LBL_MONITOR_THRESHOLD', $plugin->component), get_string('HELP_MONITOR_THRESHOLD', $plugin->component), enrol_shebang_processor::DEF_MONITOR_THRESHOLD,
            array('15' => '15', '30' => '30', '45' => '45', '60' => '60', '120' => '120', '180' => '180', '240' => '240')));
        $settings->add(new admin_setting_configtext("{$plugin->component}/monitor_emails",      get_string('LBL_MONITOR_EMAILS',    $plugin->component), get_string('HELP_MONITOR_EMAILS',    $plugin->component), ''));


        /*
         * Person messages
         */
        $settings->add(new admin_setting_heading("{$plugin->component}_person",                get_string('LBL_PERSON',        $plugin->component) . $OUTPUT->help_icon('LBL_PERSON', $plugin->component), ''));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_create",  get_string('LBL_PERSON_CREATE', $plugin->component), '', '1'));
        $settings->add(new admin_setting_configselect("{$plugin->component}/person_delete",    get_string('LBL_PERSON_DELETE', $plugin->component), '', '',
            array(
                ''                                                 => get_string('LBL_PERSON_DELETE_NOACTION', $plugin->component),
                enrol_shebang_processor::OPT_PERSON_DELETE_DELETE  => get_string('LBL_PERSON_DELETE_DELETE',   $plugin->component),
                enrol_shebang_processor::OPT_PERSON_DELETE_UNENROL => get_string('LBL_PERSON_DELETE_UNENROL',  $plugin->component)
        )));

        $settings->add(new admin_setting_configselect("{$plugin->component}/person_username", get_string('LBL_PERSON_USERNAME', $plugin->component), '', enrol_shebang_processor::OPT_PERSON_USERNAME_USERID_LOGON,
            array(
              enrol_shebang_processor::OPT_PERSON_USERNAME_EMAIL        => get_string('LBL_PERSON_USERNAME_EMAIL',        $plugin->component),
              enrol_shebang_processor::OPT_PERSON_USERNAME_USERID_EMAIL => get_string('LBL_PERSON_USERNAME_USERID_EMAIL', $plugin->component),
              enrol_shebang_processor::OPT_PERSON_USERNAME_USERID_LOGON => get_string('LBL_PERSON_USERNAME_USERID_LOGON', $plugin->component),
              enrol_shebang_processor::OPT_PERSON_USERNAME_USERID_SCTID => get_string('LBL_PERSON_USERNAME_USERID_SCTID', $plugin->component)
            )));

        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_username_failsafe",  get_string('LBL_PERSON_USERNAME_FAILSAFE', $plugin->component), '', ''));

        $userOptions = array();
        $authModules = get_enabled_auth_plugins();
        foreach ($authModules as $mod) {
            $userOptions[$mod] = get_string('pluginname', "auth_{$mod}");
        }
        $settings->add(new admin_setting_configselect("{$plugin->component}/person_auth_method", get_string('LBL_PERSON_AUTH_METHOD', $plugin->component), '', enrol_shebang_processor::DEF_PERSON_AUTH_METHOD, $userOptions));
        $settings->add(new admin_setting_configtext("{$plugin->component}/person_shib_domain",   get_string('LBL_PERSON_SHIB_DOMAIN', $plugin->component), '', ''));
        $settings->add(new admin_setting_configselect("{$plugin->component}/person_password",    get_string('LBL_PERSON_PASSWORD',    $plugin->component), '', '',
            array(
              ''                                                        => get_string('LBL_PERSON_PASSWORD_RANDOM',       $plugin->component),
              enrol_shebang_processor::OPT_PERSON_USERNAME_USERID_LOGON => get_string('LBL_PERSON_PASSWORD_USERID_LOGON', $plugin->component),
              enrol_shebang_processor::OPT_PERSON_PASSWORD_USERID_SCTID => get_string('LBL_PERSON_PASSWORD_USERID_SCTID', $plugin->component)
            )));

        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_password_changes",  get_string('LBL_PERSON_PASSWORD_CHANGES',  $plugin->component), '', '0'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_firstname_changes", get_string('LBL_PERSON_FIRSTNAME_CHANGES', $plugin->component), '', '1'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_lastname_changes",  get_string('LBL_PERSON_LASTNAME_CHANGES',  $plugin->component), '', '1'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_telephone",         get_string('LBL_PERSON_TELEPHONE',         $plugin->component), '', '1'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_telephone_changes", get_string('LBL_PERSON_TELEPHONE_CHANGES', $plugin->component), '', '1'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_address",           get_string('LBL_PERSON_ADDRESS',           $plugin->component), '', '1'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_address_changes",   get_string('LBL_PERSON_ADDRESS_CHANGES',   $plugin->component), '', '1'));

        $settings->add(new admin_setting_configselect("{$plugin->component}/person_locality",            get_string('LBL_PERSON_LOCALITY',          $plugin->component), '', enrol_shebang_processor::OPT_PERSON_LOCALITY_IFF,
            array(
                enrol_shebang_processor::OPT_PERSON_LOCALITY_MSG => get_string('LBL_PERSON_LOCALITY_MSG', $plugin->component),
                enrol_shebang_processor::OPT_PERSON_LOCALITY_DEF => get_string('LBL_PERSON_LOCALITY_DEF', $plugin->component),
                enrol_shebang_processor::OPT_PERSON_LOCALITY_IFF => get_string('LBL_PERSON_LOCALITY_IFF', $plugin->component)
            )));

        $settings->add(new admin_setting_configtext("{$plugin->component}/person_locality_default",    get_string('LBL_PERSON_LOCALITY_DEFAULT',  $plugin->component), '', ''));
        $settings->add(new admin_setting_configselect("{$plugin->component}/person_country",           get_string('LBL_PERSON_COUNTRY',           $plugin->component), '', enrol_shebang_processor::DEF_PERSON_COUNTRY, get_string_manager()->get_list_of_countries()));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_idnumber_sctid",  get_string('LBL_PERSON_IDNUMBER_SCTID',    $plugin->component), '', '0'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_nickname_prefer", get_string('LBL_PERSON_NICKNAME_PREFER',   $plugin->component), '', '0'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/person_fullname_desc",   get_string('LBL_PERSON_FULLNAME_DESC',     $plugin->component), '', '1'));

        /*
         * Course-section (group) messages
         */
        $settings->add(new admin_setting_heading("{$plugin->component}_course",                get_string('LBL_COURSE',             $plugin->component) . $OUTPUT->help_icon('LBL_COURSE', $plugin->component), ''));
        $settings->add(new admin_setting_configtext("{$plugin->component}/course_term_filter", get_string('LBL_COURSE_TERM_FILTER', $plugin->component), '', ''));
        $settings->add(new admin_setting_configselect("{$plugin->component}/course_category",  get_string('LBL_COURSE_CATEGORY',    $plugin->component), '', enrol_shebang_processor::OPT_COURSE_CATEGORY_TERM,
          array(
            enrol_shebang_processor::OPT_COURSE_CATEGORY_TERM => get_string('LBL_COURSE_CATEGORY_TERM', $plugin->component),
            enrol_shebang_processor::OPT_COURSE_CATEGORY_DEPT => get_string('LBL_COURSE_CATEGORY_DEPT', $plugin->component),
            enrol_shebang_processor::OPT_COURSE_CATEGORY_NEST => get_string('LBL_COURSE_CATEGORY_NEST', $plugin->component),
            enrol_shebang_processor::OPT_COURSE_CATEGORY_PICK => get_string('LBL_COURSE_CATEGORY_PICK', $plugin->component)
          )));

        $categoryArray = \core_course_category::make_categories_list();
        $settings->add(new admin_setting_configselect("{$plugin->component}/course_category_id",            get_string('LBL_COURSE_CATEGORY_ID',          $plugin->component), '', '1', $categoryArray));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/course_sections_equal_weeks", get_string('LBL_COURSE_SECTIONS_EQUAL_WEEKS', $plugin->component), '', '1'));

        $token_list = '';
        foreach (enrol_shebang_processor::$courseNameTokens as $token) {
            $ar = explode('/', $token);
            if ($token_list) $token_list .= ', ';
            $token_list .= $ar[1];
        }
        $settings->add(new admin_setting_configtext("{$plugin->component}/course_fullname_pattern",       get_string('LBL_COURSE_FULLNAME_PATTERN',   $plugin->component), $token_list, ''));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/course_fullname_uppercase", get_string('LBL_COURSE_FULLNAME_UPPERCASE', $plugin->component), '', '1'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/course_fullname_changes",   get_string('LBL_COURSE_FULLNAME_CHANGES',   $plugin->component), '', '1'));

        $token_list = '';
        foreach (enrol_shebang_processor::$courseNameTokens as $token) {
            $ar = explode('/', $token);
            if ($token_list) $token_list .= ', ';
            $token_list .= $ar[1];
        }
        $settings->add(new admin_setting_configtext("{$plugin->component}/course_shortname_pattern",       get_string('LBL_COURSE_SHORTNAME_PATTERN',   $plugin->component), $token_list, ''));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/course_shortname_uppercase", get_string('LBL_COURSE_SHORTNAME_UPPERCASE', $plugin->component), '', '1'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/course_shortname_changes",   get_string('LBL_COURSE_SHORTNAME_CHANGES',   $plugin->component), '', '1'));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/course_hidden",              get_string('LBL_COURSE_HIDDEN',              $plugin->component), '', '0'));
        $settings->add(new admin_setting_configselect("{$plugin->component}/course_parent_striplead",      get_string('LBL_COURSE_PARENT_STRIPLEAD',    $plugin->component), '', '0',
            array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10')));


        /*
         * Crosslist course messages
         */
        $settings->add(new admin_setting_heading("{$plugin->component}_crosslist",                       get_string('LBL_CROSSLIST',                  $plugin->component) . $OUTPUT->help_icon('LBL_CROSSLIST', $plugin->component), ''));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/crosslist_enabled",        get_string('LBL_CROSSLIST_ENABLED',          $plugin->component), '', ''));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/crosslist_groups",         get_string('LBL_CROSSLIST_GROUPS',           $plugin->component), '', ''));
        $settings->add(new admin_setting_configtext("{$plugin->component}/crosslist_fullname_prefix",    get_string('LBL_CROSSLIST_FULLNAME_PREFIX',  $plugin->component), '', ''));
        $settings->add(new admin_setting_configtext("{$plugin->component}/crosslist_shortname_prefix",   get_string('LBL_CROSSLIST_SHORTNAME_PREFIX', $plugin->component), '', ''));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/crosslist_hide_on_parent", get_string('LBL_CROSSLIST_HIDE_ON_PARENT',   $plugin->component), '', ''));


        /*
         * Enrollment messages
         */
        $settings->add(new admin_setting_heading("{$plugin->component}_enrollments",                        get_string('LBL_ENROLLMENTS',                 $plugin->component) . $OUTPUT->help_icon('LBL_ENROLLMENTS', $plugin->component), ''));
        $settings->add(new admin_setting_configcheckbox("{$plugin->component}/enrollments_delete_inactive", get_string('LBL_ENROLLMENTS_DELETE_INACTIVE', $plugin->component), '', '0'));


        /*
         * Enrollment role mappsing
         */
        $settings->add(new admin_setting_heading("{$plugin->component}_enroll", get_string('LBL_ENROLL', $plugin->component) . $OUTPUT->help_icon('LBL_ENROLL', $plugin->component), ''));

        $userOptions = array('' => get_string('LBL_ENROLL_ROLE_NOMAP', $plugin->component)) + get_assignable_roles(context_course::instance(SITEID));
        foreach(array('01','02','03','04','05','06','07','08') as $role_num) {
            $settings->add(new admin_setting_configselect("{$plugin->component}/enroll_rolemap_" . $role_num, get_string('LBL_ENROLL_ROLEMAP_' . $role_num, $plugin->component), '', '', $userOptions));
        }

    } // if ($ADMIN->fulltree)
