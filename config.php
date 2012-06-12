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
     * Called (included) from the enrolment_plugin_shebang class'
     * config_form method. So, in here, we can access all the
     * member vars, such as $this->pluginConfigs, where we have
     * our module's config
     */

    include(dirname(__FILE__) . '/version.php');

    /**
     * Index (in order of appearance) of config values
     *
     * logging_onlyerrors
     * logging_logxml
     * logging_nologlock
     * secure_username
     * secure_passwd
     * secure_method
     * monitor_enabled
     * monitor_weekdays_0 ... monitor_weekdays_6
     * monitor_start_hour,
     * monitor_start_min
     * monitor_stop_hour,
     * monitor_stop_min
     * monitor_threshold
     * monitor_emails
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
     * crosslist_enabled
     * crosslist_method
     * crosslist_groups
     * crosslist_fullname_prefix
     * crosslist_shortname_prefix
     * crosslist_hide_on_parent
     * enroll_rolemap_01
     * enroll_rolemap_02
     * enroll_rolemap_03
     * enroll_rolemap_04
     * enroll_rolemap_05
     * enroll_rolemap_06
     * enroll_rolemap_07 
     * enroll_rolemap_08
     */

    if (!defined('HELP_TOOLTIP'))               define('HELP_TOOLTIP',                  'More detail about this option');
    if (!defined('DEFAULT_MONITOR_START_HOUR')) define('DEFAULT_MONITOR_START_HOUR',    '09');
    if (!defined('DEFAULT_MONITOR_STOP_HOUR'))  define('DEFAULT_MONITOR_STOP_HOUR',     '17');
    if (!defined('DEFAULT_MONITOR_THRESHOLD'))  define('DEFAULT_MONITOR_THRESHOLD',     '30');
    if (!defined('MONITOR_MINUTES_INTERVAL'))   define('MONITOR_MINUTES_INTERVAL',      '15');
    if (!defined('DEFAULT_COUNTRY'))            define('DEFAULT_COUNTRY',               'US');

?>

    <table cellspacing="0" cellpadding="5" border="0" class="boxaligncenter">

        <tr><th colspan="3" align="center"><?php helpbutton('disclaimer', get_string('LBL_DISCLAIMER', self::PLUGIN_NAME), self::PLUGIN_NAME, true, true); ?></th></tr>

        <tr><td colspan="3" align="center">
                <?php echo print_string('LBL_VERSION', self::PLUGIN_NAME, $plugin->release); ?><br />
                <br />
                <a href="<?php echo "{$this->globalConfigs->wwwroot}/" . self::PLUGIN_PATH . "/tools.php"; ?>">Administrative Utilities</a>
            </td>
        </tr>

        <!-- ###########
         logging section
        ################ -->
        <tr><th colspan="3" align="center"><br /><?php print_string('LBL_LOGGING', self::PLUGIN_NAME); helpbutton('logging', HELP_TOOLTIP, self::PLUGIN_NAME); ?></th></tr>

        <!-- logging_onlyerrors -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_LOGGING_LOGERRS', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="logging_onlyerrors" <?php if ((isset($this->pluginConfigs->logging_onlyerrors)) && ($this->pluginConfigs->logging_onlyerrors)) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- logging_logxml -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_LOGGING_LOGXML', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="logging_logxml" <?php if ((isset($this->pluginConfigs->logging_logxml)) && ($this->pluginConfigs->logging_logxml)) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- logging_nologlock -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_LOGGING_NOLOGLOCK', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="logging_nologlock" <?php if ((isset($this->pluginConfigs->logging_nologlock)) && ($this->pluginConfigs->logging_nologlock)) echo "checked"; ?>/>
            </td>
        </tr>


        <!-- ###########
         secure section
        ################ -->
        <tr><th colspan="3" align="center"><br /><?php print_string('LBL_SECURE', self::PLUGIN_NAME); helpbutton('secure', HELP_TOOLTIP, self::PLUGIN_NAME); ?></th></tr>

        <!-- secure_username -->
        <tr valign="top">
            <td align="right" width="50%"><?php print_string('LBL_SECURE_USERNAME', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="text" size="25" name="secure_username" value="<?php echo isset($this->pluginConfigs->secure_username) ? $this->pluginConfigs->secure_username : ''; ?>"/>
            </td>
        </tr>

        <!-- secure_passwd -->
        <tr valign="top">
            <td align="right" width="50%"><?php print_string('LBL_SECURE_PASSWD', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="password" size="25" name="secure_passwd" value="<?php echo isset($this->pluginConfigs->secure_passwd) ? $this->pluginConfigs->secure_passwd : ''; ?>"/>
            </td>
        </tr>

        <!-- secure_method -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_SECURE_METHOD', self::PLUGIN_NAME) ?>:</td>
            <td colspan="2">
            <?php choose_from_menu(array(self::OPT_SECURE_METHOD_BASIC  => get_string('LBL_SECURE_METHOD_BASIC',  self::PLUGIN_NAME),
                                         self::OPT_SECURE_METHOD_DIGEST => get_string('LBL_SECURE_METHOD_DIGEST', self::PLUGIN_NAME)),
                                   'secure_method',
                                   isset($this->pluginConfigs->secure_method) ? $this->pluginConfigs->secure_method : self::OPT_SECURE_METHOD_DIGEST,
                                   ''); ?>
            </td>
        </tr>

        <!-- ###########
         monitor section
        ################ -->
        <tr><th colspan="3" align="center"><br /><?php print_string('LBL_MONITOR', self::PLUGIN_NAME); helpbutton('monitor', HELP_TOOLTIP, self::PLUGIN_NAME); ?></th></tr>

        <!-- monitor_enabled -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_MONITOR_ENABLED', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2" >
                <input type="checkbox" value="1" name="monitor_enabled" <?php if (isset($this->pluginConfigs->monitor_enabled) && $this->pluginConfigs->monitor_enabled) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- monitor_weekdays -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_MONITOR_WEEKDAYS', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2" >
                <?php echo get_string('sun', 'calendar'); ?>:&nbsp;<input type="checkbox" value="1" name="monitor_weekdays_0" <?php if (isset($this->pluginConfigs->monitor_weekdays_0) && $this->pluginConfigs->monitor_weekdays_0) echo "checked"; ?>/>
                <?php echo get_string('mon', 'calendar'); ?>:&nbsp;<input type="checkbox" value="1" name="monitor_weekdays_1" <?php if (isset($this->pluginConfigs->monitor_weekdays_1) && $this->pluginConfigs->monitor_weekdays_1) echo "checked"; ?>/>
                <?php echo get_string('tue', 'calendar'); ?>:&nbsp;<input type="checkbox" value="1" name="monitor_weekdays_2" <?php if (isset($this->pluginConfigs->monitor_weekdays_2) && $this->pluginConfigs->monitor_weekdays_2) echo "checked"; ?>/>
                <?php echo get_string('wed', 'calendar'); ?>:&nbsp;<input type="checkbox" value="1" name="monitor_weekdays_3" <?php if (isset($this->pluginConfigs->monitor_weekdays_3) && $this->pluginConfigs->monitor_weekdays_3) echo "checked"; ?>/>
                <?php echo get_string('thu', 'calendar'); ?>:&nbsp;<input type="checkbox" value="1" name="monitor_weekdays_4" <?php if (isset($this->pluginConfigs->monitor_weekdays_4) && $this->pluginConfigs->monitor_weekdays_4) echo "checked"; ?>/>
                <?php echo get_string('fri', 'calendar'); ?>:&nbsp;<input type="checkbox" value="1" name="monitor_weekdays_5" <?php if (isset($this->pluginConfigs->monitor_weekdays_5) && $this->pluginConfigs->monitor_weekdays_5) echo "checked"; ?>/>
                <?php echo get_string('sat', 'calendar'); ?>:&nbsp;<input type="checkbox" value="1" name="monitor_weekdays_6" <?php if (isset($this->pluginConfigs->monitor_weekdays_6) && $this->pluginConfigs->monitor_weekdays_6) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- monitor_start_hour, monitor_start_min -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_MONITOR_START', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <?php print_time_selector('monitor_start_hour',
                                          'monitor_start_min',
                                           mktime(isset($this->pluginConfigs->monitor_start_hour) ? $this->pluginConfigs->monitor_start_hour : DEFAULT_MONITOR_START_HOUR,
                                                  isset($this->pluginConfigs->monitor_start_min)  ? $this->pluginConfigs->monitor_start_min  : 0,  0, 0, 0, 0),
                                           MONITOR_MINUTES_INTERVAL); ?>
            </td>
        </tr>

        <!-- monitor_stop_hour, monitor_stop_min -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_MONITOR_STOP', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <?php print_time_selector('monitor_stop_hour',
                                          'monitor_stop_min',
                                          mktime(isset($this->pluginConfigs->monitor_stop_hour) ? $this->pluginConfigs->monitor_stop_hour : DEFAULT_MONITOR_STOP_HOUR,
                                                 isset($this->pluginConfigs->monitor_stop_min)  ? $this->pluginConfigs->monitor_stop_min  : 0,  0, 0, 0, 0),
                                          MONITOR_MINUTES_INTERVAL); ?>
            </td>
        </tr>

        <!-- monitor_threshold -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_MONITOR_THRESHOLD', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="text" size="5" name="monitor_threshold" value="<?php echo isset($this->pluginConfigs->monitor_threshold) && !empty($this->pluginConfigs->monitor_threshold) ? $this->pluginConfigs->monitor_threshold : DEFAULT_MONITOR_THRESHOLD; ?>"/>&nbsp;<?php print_string('minutes'); ?>
            </td>
        </tr>

        <!-- monitor_emails -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_MONITOR_EMAILS', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="text" size="50" name="monitor_emails" value="<?php echo isset($this->pluginConfigs->monitor_emails) ? $this->pluginConfigs->monitor_emails : ''; ?>"/><br />
                <small><i><?php print_string('LBL_MONITOR_USECOMMAS', self::PLUGIN_NAME); ?></i></small>
            </td>
        </tr>



        <!-- ###########
         person section
        ################ -->
        <tr><th colspan="3" align="center"><br /><?php print_string('LBL_PERSON', self::PLUGIN_NAME); helpbutton('person', HELP_TOOLTIP, self::PLUGIN_NAME); ?></th></tr>

        <!-- person_create -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_CREATE', self::PLUGIN_NAME) ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="person_create" <?php if (isset($this->pluginConfigs->person_create) && $this->pluginConfigs->person_create) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- person_delete -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_DELETE', self::PLUGIN_NAME) ?>:</td>
            <td colspan="2">
            <?php choose_from_menu(array('' => get_string('LBL_PERSON_DELETE_NOACTION', self::PLUGIN_NAME),
                                         self::OPT_PERSON_DELETE_UNENROL => get_string('LBL_PERSON_DELETE_UNENROL', self::PLUGIN_NAME),
                                         self::OPT_PERSON_DELETE_DELETE  => get_string('LBL_PERSON_DELETE_DELETE',  self::PLUGIN_NAME)),
                                   'person_delete',
                                   isset($this->pluginConfigs->person_delete) ? $this->pluginConfigs->person_delete : '',
                                   ''); ?>
            </td>
        </tr>

        <!-- person_username -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_USERNAME', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
            <?php choose_from_menu(array(self::OPT_PERSON_USERNAME_EMAIL        => get_string('LBL_PERSON_USERNAME_EMAIL',        self::PLUGIN_NAME),
                                         self::OPT_PERSON_USERNAME_USERID_EMAIL => get_string('LBL_PERSON_USERNAME_USERID_EMAIL', self::PLUGIN_NAME),
                                         self::OPT_PERSON_USERNAME_USERID_LOGON => get_string('LBL_PERSON_USERNAME_USERID_LOGON', self::PLUGIN_NAME),
                                         self::OPT_PERSON_USERNAME_USERID_SCTID => get_string('LBL_PERSON_USERNAME_USERID_SCTID', self::PLUGIN_NAME)),
                                   'person_username',
                                   isset($this->pluginConfigs->person_username) ? $this->pluginConfigs->person_username : self::OPT_PERSON_USERNAME_USERID_LOGON,
                                   ''); ?>
            </td>
        </tr>

        <!-- person_username_failsafe -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_USERNAME_FAILSAFE', self::PLUGIN_NAME); ?>:<br /><small><i><?php print_string('LBL_PERSON_USERNAME_FAILSAFE_INFO', self::PLUGIN_NAME); ?></i></small></td>
            <td colspan="2">
                <input type="checkbox" value="1" name="person_username_failsafe" <?php if (isset($this->pluginConfigs->person_username_failsafe) && $this->pluginConfigs->person_username_failsafe) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- person_auth_method -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_AUTH_METHOD', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <?php $authModules = get_enabled_auth_plugins();
                      $userOptions = array();
                      foreach ($authModules as $mod) {
                          $userOptions[$mod] = get_string("auth_{$mod}title", 'auth');
                      }
                      choose_from_menu($userOptions, 'person_auth_method', isset($this->pluginConfigs->person_auth_method) ? $this->pluginConfigs->person_auth_method : 'nologin'); ?>
            </td>
        </tr>

        <!-- person_shib_domain -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_SHIB_DOMAIN', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="text" size="25" name="person_shib_domain" value="<?php echo isset($this->pluginConfigs->person_shib_domain) ? $this->pluginConfigs->person_shib_domain : ''; ?>"/>
            </td>
        </tr>

        <!-- person_password -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_PASSWORD', self::PLUGIN_NAME); ?>:<br /><small><i><?php print_string('LBL_PERSON_PASSWORD_INFO', self::PLUGIN_NAME); ?></i></small></td>
            <td colspan="2">
            <?php choose_from_menu(array(''                                      => get_string('LBL_PERSON_PASSWORD_RANDOM',        self::PLUGIN_NAME),
                                         self::OPT_PERSON_PASSWORD_USERID_LOGON  => get_string('LBL_PERSON_PASSWORD_USERID_LOGON',  self::PLUGIN_NAME),
                                         self::OPT_PERSON_PASSWORD_USERID_SCTID  => get_string('LBL_PERSON_PASSWORD_USERID_SCTID',  self::PLUGIN_NAME)),
                                   'person_password',
                                   isset($this->pluginConfigs->person_password) ? $this->pluginConfigs->person_password : '',
                                   ''); ?>
            </td>
        </tr>

        <!-- person_password_changes -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_PASSWORD_CHANGES', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="person_password_changes" <?php if (isset($this->pluginConfigs->person_password_changes) && $this->pluginConfigs->person_password_changes) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- person_firstname_changes -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_FIRSTNAME_CHANGES', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="person_firstname_changes" <?php if (isset($this->pluginConfigs->person_firstname_changes) && $this->pluginConfigs->person_firstname_changes) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- person_lastname_changes -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_LASTNAME_CHANGES', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="person_lastname_changes" <?php if (isset($this->pluginConfigs->person_lastname_changes) && $this->pluginConfigs->person_lastname_changes) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- person_telephone -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_TELEPHONE', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="person_telephone" <?php if (isset($this->pluginConfigs->person_telephone) && $this->pluginConfigs->person_telephone) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- person_telephone_changes -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_TELEPHONE_CHANGES', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="person_telephone_changes" <?php if (isset($this->pluginConfigs->person_telephone_changes) && $this->pluginConfigs->person_telephone_changes) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- person_address -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_ADDRESS', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="person_address" <?php if (isset($this->pluginConfigs->person_address) && $this->pluginConfigs->person_address) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- person_address_changes -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_PERSON_ADDRESS_CHANGES', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="person_address_changes" <?php if (isset($this->pluginConfigs->person_address_changes) && $this->pluginConfigs->person_address_changes) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- person_locality, person_locality_default -->
        <tr valign="top">
            <td align="right"><?php  print_string('LBL_PERSON_LOCALITY', self::PLUGIN_NAME) ?>:<br />
            <br />
            <?php  print_string('LBL_PERSON_LOCALITY_DEFAULT', self::PLUGIN_NAME) ?>:
            </td>
            <td colspan="2">
            <?php choose_from_menu(array(self::OPT_PERSON_LOCALITY_MSG => get_string('LBL_PERSON_LOCALITY_MSG', self::PLUGIN_NAME),
                                         self::OPT_PERSON_LOCALITY_DEF => get_string('LBL_PERSON_LOCALITY_DEF', self::PLUGIN_NAME),
                                         self::OPT_PERSON_LOCALITY_IFF => get_string('LBL_PERSON_LOCALITY_IFF', self::PLUGIN_NAME)),
                                   'person_locality',
                                   isset($this->pluginConfigs->person_locality) ? $this->pluginConfigs->person_locality : self::OPT_PERSON_LOCALITY_IFF,
                                   ''); ?><br />
            <br />
            <input type="text" size="25" name="person_locality_default" value="<?php echo isset($this->pluginConfigs->person_locality_default) ? $this->pluginConfigs->person_locality_default : ''; ?>"/>
            </td>
        </tr>

        <!-- person_country -->
        <tr valign="top">
            <td align="right"><?php  print_string('LBL_PERSON_COUNTRY', self::PLUGIN_NAME) ?>:</td>
            <td colspan="2">
            <?php choose_from_menu(get_list_of_countries(),
                                   'person_country',
                                   isset($this->pluginConfigs->person_country) ? $this->pluginConfigs->person_country : DEFAULT_COUNTRY,
                                   ''); ?>
            </td>
        </tr>


        <!-- ###########
         coursesection (course) section
        ################ -->
        <tr><th colspan="3" align="center"><br /><?php print_string('LBL_COURSE', self::PLUGIN_NAME); helpbutton('course', HELP_TOOLTIP, self::PLUGIN_NAME); ?></th></tr>

        <!-- course_category -->
        <tr valign="top">
            <td align="right"><?php  print_string('LBL_COURSE_CATEGORY', self::PLUGIN_NAME) ?>:<br />
            <br />
            <?php  print_string('LBL_COURSE_CATEGORY_ID', self::PLUGIN_NAME) ?>:
            </td>
            <td colspan="2">
            <?php choose_from_menu(array(self::OPT_COURSE_CATEGORY_TERM => get_string('LBL_COURSE_CATEGORY_TERM', self::PLUGIN_NAME),
                                         self::OPT_COURSE_CATEGORY_DEPT => get_string('LBL_COURSE_CATEGORY_DEPT', self::PLUGIN_NAME),
                                         self::OPT_COURSE_CATEGORY_NEST => get_string('LBL_COURSE_CATEGORY_NEST', self::PLUGIN_NAME),
                                         self::OPT_COURSE_CATEGORY_PICK => get_string('LBL_COURSE_CATEGORY_PICK', self::PLUGIN_NAME)),
                                   'course_category',
                                   isset($this->pluginConfigs->course_category) ? $this->pluginConfigs->course_category : self::OPT_COURSE_CATEGORY_TERM,
                                   ''); ?><br />
            <br />
            <?php $categoryArray = array(); $parentArray = array();
                  make_categories_list($categoryArray, $parentArray);
                  choose_from_menu($categoryArray,
                                   'course_category_id',
                                   isset($this->pluginConfigs->course_category_id) ? $this->pluginConfigs->course_category_id : '',
                                   ''); ?>
            </td>
        </tr>

        <!-- course_sections_equal_weeks -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_COURSE_SECTIONS_EQUAL_WEEKS', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="course_sections_equal_weeks" <?php if (isset($this->pluginConfigs->course_sections_equal_weeks) && $this->pluginConfigs->course_sections_equal_weeks) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- course_fullname_pattern -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_COURSE_FULLNAME_PATTERN', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="text" size="25" name="course_fullname_pattern" value="<?php echo isset($this->pluginConfigs->course_fullname_pattern) ? $this->pluginConfigs->course_fullname_pattern : ''; ?>"/><br />
                <small>
                <?php
                    $token_list = '';
                    foreach (self::$courseNameTokens as $token) {
                        $ar = explode('/', $token);
                        if ($token_list) $token_list .= ', ';
                        $token_list .= $ar[1];
                    }
                    echo $token_list;
                ?>
                </small>
            </td>
        </tr>

        <!-- course_fullname_uppercase -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_COURSE_FULLNAME_UPPERCASE', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="course_fullname_uppercase" <?php if (isset($this->pluginConfigs->course_fullname_uppercase) && $this->pluginConfigs->course_fullname_uppercase) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- course_fullname_changes -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_COURSE_FULLNAME_CHANGES', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="course_fullname_changes" <?php if (isset($this->pluginConfigs->course_fullname_changes) && $this->pluginConfigs->course_fullname_changes) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- course_shortname_pattern -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_COURSE_SHORTNAME_PATTERN', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="text" size="25" name="course_shortname_pattern" value="<?php echo isset($this->pluginConfigs->course_shortname_pattern) ? $this->pluginConfigs->course_shortname_pattern : ''; ?>"/><br />
                <small>
                <?php
                    $token_list = '';
                    foreach (self::$courseNameTokens as $token) {
                        $ar = explode('/', $token);
                        if ($token_list) $token_list .= ', ';
                        $token_list .= $ar[1];
                    }
                    echo $token_list;
                ?>
                </small>                
            </td>
        </tr>

        <!-- course_shortname_uppercase -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_COURSE_SHORTNAME_UPPERCASE', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="course_shortname_uppercase" <?php if (isset($this->pluginConfigs->course_shortname_uppercase) && $this->pluginConfigs->course_shortname_uppercase) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- course_shortname_changes -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_COURSE_SHORTNAME_CHANGES', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="course_shortname_changes" <?php if (isset($this->pluginConfigs->course_shortname_changes) && $this->pluginConfigs->course_shortname_changes) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- course_hidden -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_COURSE_HIDDEN', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="course_hidden" <?php if (isset($this->pluginConfigs->course_hidden) && $this->pluginConfigs->course_hidden) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- course_parent_striplead -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_COURSE_PARENT_STRIPLEAD', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="text" size="5" name="course_parent_striplead" value="<?php echo isset($this->pluginConfigs->course_parent_striplead) && !empty($this->pluginConfigs->course_parent_striplead) ? $this->pluginConfigs->course_parent_striplead : ''; ?>"/>&nbsp;characters
            </td>
        </tr>
        
        
        <!-- ###########
         cross-listing
        ################ -->
        <tr><th colspan="3" align="center"><br /><?php print_string('LBL_CROSSLIST', self::PLUGIN_NAME); helpbutton('crosslist', HELP_TOOLTIP, self::PLUGIN_NAME); ?></th></tr>

        <!-- crosslist_enabled -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_CROSSLIST_ENABLED', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="crosslist_enabled" <?php if (isset($this->pluginConfigs->crosslist_enabled) && $this->pluginConfigs->crosslist_enabled) echo "checked"; ?>/>
            </td>
        </tr>

        <!-- crosslist_method -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_CROSSLIST_METHOD', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
            <?php choose_from_menu(array(self::OPT_CROSSLIST_METHOD_META  => get_string('LBL_CROSSLIST_METHOD_META',   self::PLUGIN_NAME),
                                         self::OPT_CROSSLIST_METHOD_MERGE => get_string('LBL_CROSSLIST_METHOD_MERGE',  self::PLUGIN_NAME)),
                                   'crosslist_method',
                                   isset($this->pluginConfigs->crosslist_method) ? $this->pluginConfigs->crosslist_method : self::OPT_CROSSLIST_METHOD_META,
                                   ''); ?>
            </td>
        </tr>

        <!-- crosslist_groups -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_CROSSLIST_GROUPS', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="crosslist_groups" <?php if (isset($this->pluginConfigs->crosslist_groups) && $this->pluginConfigs->crosslist_groups) echo "checked"; ?>/>
            </td>
        </tr>
        
        <!-- crosslist_fullname_prefix -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_CROSSLIST_FULLNAME_PREFIX', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="text" size="25" name="crosslist_fullname_prefix" value="<?php echo isset($this->pluginConfigs->crosslist_fullname_prefix) ? $this->pluginConfigs->crosslist_fullname_prefix : ''; ?>"/><br />
            </td>
        </tr>        
        
        <!-- crosslist_shortname_prefix -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_CROSSLIST_SHORTNAME_PREFIX', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="text" size="25" name="crosslist_shortname_prefix" value="<?php echo isset($this->pluginConfigs->crosslist_shortname_prefix) ? $this->pluginConfigs->crosslist_shortname_prefix : ''; ?>"/><br />
            </td>
        </tr>       
        
        <!-- crosslist_hide_on_parent -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_CROSSLIST_HIDE_ON_PARENT', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2">
                <input type="checkbox" value="1" name="crosslist_hide_on_parent" <?php if (isset($this->pluginConfigs->crosslist_hide_on_parent) && $this->pluginConfigs->crosslist_hide_on_parent) echo "checked"; ?>/>
            </td>
        </tr>
        
        
        <!-- ###########
         member (enrolment) section
        ################ -->
        <tr><th colspan="3" align="center"><br /><?php print_string('LBL_ENROLL', self::PLUGIN_NAME); helpbutton('enrollment', HELP_TOOLTIP, self::PLUGIN_NAME); ?></th></tr>
        
        <?php
            $roles = get_all_roles();
            $userOptions = array();
            foreach ($roles as $role) {
                
                $userOptions[$role->id] = $role->name;
            }
        ?>

        <!-- enroll_rolemap_01 -->
        <tr valign="top">
            <td align="right"><?php print_string('LBL_ENROLL_ROLEMAP_01', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2"><?php choose_from_menu($userOptions, 'enroll_rolemap_01', isset($this->pluginConfigs->enroll_rolemap_01) ? $this->pluginConfigs->enroll_rolemap_01 : '', get_string('LBL_ENROLL_ROLE_NOMAP', self::PLUGIN_NAME)); ?></td>
        </tr>
        <!-- enroll_rolemap_02 -->
        <tr valign="top">            
            <td align="right"><?php print_string('LBL_ENROLL_ROLEMAP_02', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2"><?php choose_from_menu($userOptions, 'enroll_rolemap_02', isset($this->pluginConfigs->enroll_rolemap_02) ? $this->pluginConfigs->enroll_rolemap_02 : '', get_string('LBL_ENROLL_ROLE_NOMAP', self::PLUGIN_NAME)); ?></td>
        </tr>
        <!-- enroll_rolemap_03 -->
        <tr valign="top">            
            <td align="right"><?php print_string('LBL_ENROLL_ROLEMAP_03', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2"><?php choose_from_menu($userOptions, 'enroll_rolemap_03', isset($this->pluginConfigs->enroll_rolemap_03) ? $this->pluginConfigs->enroll_rolemap_03 : '', get_string('LBL_ENROLL_ROLE_NOMAP', self::PLUGIN_NAME)); ?></td>
        </tr>
        <!-- enroll_rolemap_04 -->
        <tr valign="top">            
            <td align="right"><?php print_string('LBL_ENROLL_ROLEMAP_04', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2"><?php choose_from_menu($userOptions, 'enroll_rolemap_04', isset($this->pluginConfigs->enroll_rolemap_04) ? $this->pluginConfigs->enroll_rolemap_04 : '', get_string('LBL_ENROLL_ROLE_NOMAP', self::PLUGIN_NAME)); ?></td>
        </tr>
        <!-- enroll_rolemap_05 -->
        <tr valign="top">            
            <td align="right"><?php print_string('LBL_ENROLL_ROLEMAP_05', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2"><?php choose_from_menu($userOptions, 'enroll_rolemap_05', isset($this->pluginConfigs->enroll_rolemap_05) ? $this->pluginConfigs->enroll_rolemap_05 : '', get_string('LBL_ENROLL_ROLE_NOMAP', self::PLUGIN_NAME)); ?></td>
        </tr>
        <!-- enroll_rolemap_06 -->
        <tr valign="top">            
            <td align="right"><?php print_string('LBL_ENROLL_ROLEMAP_06', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2"><?php choose_from_menu($userOptions, 'enroll_rolemap_06', isset($this->pluginConfigs->enroll_rolemap_06) ? $this->pluginConfigs->enroll_rolemap_06 : '', get_string('LBL_ENROLL_ROLE_NOMAP', self::PLUGIN_NAME)); ?></td>
        </tr>
        <!-- enroll_rolemap_07 -->
        <tr valign="top">            
            <td align="right"><?php print_string('LBL_ENROLL_ROLEMAP_07', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2"><?php choose_from_menu($userOptions, 'enroll_rolemap_07', isset($this->pluginConfigs->enroll_rolemap_07) ? $this->pluginConfigs->enroll_rolemap_07 : '', get_string('LBL_ENROLL_ROLE_NOMAP', self::PLUGIN_NAME)); ?></td>
        </tr>
        <!-- enroll_rolemap_08 -->
        <tr valign="top">            
            <td align="right"><?php print_string('LBL_ENROLL_ROLEMAP_08', self::PLUGIN_NAME); ?>:</td>
            <td colspan="2"><?php choose_from_menu($userOptions, 'enroll_rolemap_08', isset($this->pluginConfigs->enroll_rolemap_08) ? $this->pluginConfigs->enroll_rolemap_08 : '', get_string('LBL_ENROLL_ROLE_NOMAP', self::PLUGIN_NAME)); ?></td>
        </tr>

    </table>
    <br />
