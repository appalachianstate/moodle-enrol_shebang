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

/* CONFIG STRINGS */
$string['pluginname']                       =
$string['enrolname']                        = 'SHEBanG for SunGard Banner/LMB';
$string['description']                      = 'This enrolment module provides a way to consume SunGard HE Banner&reg; messages generated from Luminis Message Broker. This module is not a SunGard product, and is neither endorsed nor supported by SunGard.';

$string['LBL_DISCLAIMER']                   = 'LICENSE &amp; DISCLAIMER';
$string['LBL_VERSION']                      = 'SHEBanG Enrolment Module Version $a.';

$string['LBL_LOGGING']                      = 'Logging';
$string['LBL_LOGGING_LOGERRS']              = 'Process Log: Write only errors';
$string['LBL_LOGGING_LOGXML']               = 'Message Log: Write message XML';
$string['LBL_LOGGING_NOLOGLOCK']            = 'Message Log: Suspend Locking';


$string['LBL_SECURE']                       = 'Security';
$string['LBL_SECURE_USERNAME']              = 'Username';
$string['LBL_SECURE_PASSWD']                = 'Password';
$string['LBL_SECURE_METHOD']                = 'HTTP Auth. Method';
$string['LBL_SECURE_METHOD_BASIC']          = 'Basic';
$string['LBL_SECURE_METHOD_DIGEST']         = 'Digest';


$string['LBL_MONITOR']                      = 'Monitoring';
$string['LBL_MONITOR_ENABLED']              = 'Enable monitoring';
$string['LBL_MONITOR_WEEKDAYS']             = 'Weekdays to monitor';
$string['LBL_MONITOR_START']                = 'Begin monitoring at';
$string['LBL_MONITOR_STOP']                 = 'Cease monitoring at';
$string['LBL_MONITOR_THRESHOLD']            = 'Begin notifications after';
$string['LBL_MONITOR_EMAILS']               = 'Notify email address(es)';
$string['LBL_MONITOR_USECOMMAS']            = 'Use commas (,) to separate addresses';


$string['LBL_PERSON']                       = 'Person Messages';
$string['LBL_PERSON_CREATE']                = 'Create Moodle user accounts';
$string['LBL_PERSON_DELETE']                = 'Action when recstatus = 3 (Delete)';
$string['LBL_PERSON_DELETE_NOACTION']       = 'No Action';
$string['LBL_PERSON_DELETE_UNENROL']        = 'Unenrol';
$string['LBL_PERSON_DELETE_DELETE']         = 'Delete';
$string['LBL_PERSON_USERNAME']              = 'Source field for username';
$string['LBL_PERSON_USERNAME_EMAIL']        = 'E-mail address (username@domain)';
$string['LBL_PERSON_USERNAME_USERID_EMAIL'] = 'E-mail user name (no domain)';
$string['LBL_PERSON_USERNAME_USERID_LOGON'] = 'Banner user name';
$string['LBL_PERSON_USERNAME_USERID_SCTID'] = 'Banner Id';
$string['LBL_PERSON_USERNAME_FAILSAFE']     = 'Luminis\' sourcedid as failsafe';
$string['LBL_PERSON_USERNAME_FAILSAFE_INFO']= 'Used if username undetected and otherwise blank';
$string['LBL_PERSON_AUTH_METHOD']           = 'New users\' auth. method';
$string['LBL_PERSON_SHIB_DOMAIN']           = 'Shibboleth domain for new users';
$string['LBL_PERSON_PASSWORD']              = 'Source field for user password';
$string['LBL_PERSON_PASSWORD_CHANGES']      = 'Keep passwords updated';
$string['LBL_PERSON_PASSWORD_INFO']         = 'Only effective for Manual auth.';
$string['LBL_PERSON_PASSWORD_RANDOM']       = 'Random String';
$string['LBL_PERSON_PASSWORD_USERID_LOGON'] = 'Banner Password';
$string['LBL_PERSON_PASSWORD_USERID_SCTID'] = 'SCTID Password';

$string['LBL_PERSON_FIRSTNAME_CHANGES']     = 'Apply changes to first-name on updates';
$string['LBL_PERSON_LASTNAME_CHANGES']      = 'Apply changes to last-name on updates';
$string['LBL_PERSON_EMAIL_CHANGES']         = 'Apply changes to e-mail on updates';
$string['LBL_PERSON_TELEPHONE']             = 'Import telephone information';
$string['LBL_PERSON_TELEPHONE_CHANGES']     = 'Apply changes to telephone on updates';
$string['LBL_PERSON_ADDRESS']               = 'Import address information';
$string['LBL_PERSON_ADDRESS_CHANGES']       = 'Apply changes to address on updates';

$string['LBL_PERSON_LOCALITY']              = 'Source field for user\'s city';
$string['LBL_PERSON_LOCALITY_MSG']          = 'Locality field from LMB Message';
$string['LBL_PERSON_LOCALITY_DEF']          = 'Default configured value';
$string['LBL_PERSON_LOCALITY_IFF']          = 'Locality if present, else Default';
$string['LBL_PERSON_LOCALITY_DEFAULT']      = 'Default locality/city';
$string['LBL_PERSON_COUNTRY']               = 'Country for new users';

$string['LBL_COURSE']                       = 'Course (Section) Messages';
$string['LBL_COURSE_CATEGORY']              = 'Category for new courses';
$string['LBL_COURSE_CATEGORY_TERM']         = 'By Term';
$string['LBL_COURSE_CATEGORY_DEPT']         = 'By Dept';
$string['LBL_COURSE_CATEGORY_NEST']         = 'Nest: Term/Dept';
$string['LBL_COURSE_CATEGORY_PICK']         = 'Use Existing';
$string['LBL_COURSE_CATEGORY_ID']           = 'Category for \'Use Existing\'';
$string['LBL_COURSE_SECTIONS_EQUAL_WEEKS']  = 'Sections based on start/stop dates';
$string['LBL_COURSE_FULLNAME_CHANGES']      = 'Apply changes to fullname on updates';
$string['LBL_COURSE_SHORTNAME_CHANGES']     = 'Apply changes to shortname on updates';
$string['LBL_COURSE_FULLNAME_PATTERN']      = 'Pattern for course full name';
$string['LBL_COURSE_SHORTNAME_PATTERN']     = 'Pattern for course short name';
$string['LBL_COURSE_FULLNAME_UPPERCASE']    = 'Force course full name to upper case';
$string['LBL_COURSE_SHORTNAME_UPPERCASE']   = 'Force course short name to upper case';
$string['LBL_COURSE_HIDDEN']                = 'Hide courses when created';
$string['LBL_COURSE_PARENT_STRIPLEAD']      = 'Strip lead chars from parent course code';

$string['LBL_CROSSLIST']                    = 'Cross-Listing';
$string['LBL_CROSSLIST_ENABLED']            = 'Process course cross-listing';
$string['LBL_CROSSLIST_METHOD']             = 'Implement cross-listing using';
$string['LBL_CROSSLIST_METHOD_META']        = 'Meta Course';
$string['LBL_CROSSLIST_METHOD_MERGE']       = 'Merge Course';
$string['LBL_CROSSLIST_GROUPS']             = 'Group enrollees based on child-courses';
$string['LBL_CROSSLIST_FULLNAME_PREFIX']    = 'Cross-list Fullname Prefix';
$string['LBL_CROSSLIST_SHORTNAME_PREFIX']   = 'Cross-list Shortname Prefix';
$string['LBL_CROSSLIST_HIDE_ON_PARENT']     = 'Hide child courses when cross-listed ';

$string['LBL_ENROLL']                       = 'Enrollment Mappings';
$string['LBL_ENROLL_ROLEMAP_01']            = 'Map Learner roletype (01) to';
$string['LBL_ENROLL_ROLEMAP_02']            = 'Map Instructor roletype (02) to';
$string['LBL_ENROLL_ROLEMAP_03']            = 'Map Content Dev.roletype (03) to';
$string['LBL_ENROLL_ROLEMAP_04']            = 'Map Member roletype (04) to';
$string['LBL_ENROLL_ROLEMAP_05']            = 'Map Manager roletype (05) to';
$string['LBL_ENROLL_ROLEMAP_06']            = 'Map Mentor roletype (06) to';
$string['LBL_ENROLL_ROLEMAP_07']            = 'Map Admin. roletype (07) to';
$string['LBL_ENROLL_ROLEMAP_08']            = 'Map Teach Asst. roletype (08) to';
$string['LBL_ENROLL_ROLE_NOMAP']            = 'Not Mapped';


$string['LBL_TOOLS_INDEX']                  = 'SHEBanG Admin. Utilities';
$string['LBL_TOOLS_IMPORT']                 = 'SHEBanG Import File';
$string['LBL_TOOLS_IMPORT_FILE']            = 'Select file to import';
$string['LBL_TOOLS_IMPORT_SUBMIT']          = 'Upload';


/* ERROR STRINGS */
$string['ERR_MSG_NOHEADERS']                = 'No HTTP message headers found';
$string['ERR_XMLLIBS_NOTFOUND']             = 'Required XML libraries not present';
$string['ERR_CONFIGS_NOTSET']               = 'Configuration settings not found';
$string['ERR_DATADIR_IMPORT']               = 'Unable to create the import data directory';
$string['ERR_DATADIR_MESGLOG']              = 'Unable to create the message logging directory';
$string['ERR_DATADIR_PROCLOG']              = 'Unable to create the process logging directory';
$string['ERR_MESGLOG_NOOPEN']               = 'Unable to open the message logging file';
$string['ERR_PROCLOG_NOOPEN']               = 'Unable to open the process logging file';
$string['ERR_MESGLOG_CLOSE']                = 'Failed to close the message logging file';
$string['ERR_PROCLOG_CLOSE']                = 'Failed to close the process logging file';
$string['ERR_DATAFILE_NOFILE']              = 'The import file was not found or is not readable';
$string['ERR_DATAFILE_NOOPEN']              = 'Could not lock the import file';
$string['ERR_XMLPARSER_CREATE']             = 'Failed to create xml_parser';
$string['ERR_RECORDNOTFOUND']               = 'Record not found';
$string['ERR_MISSINGVAL_USERNAME']          = 'The username value is empty';
$string['ERR_MEMBERSHIP_IDTYPE']            = 'Could not determine membership (id)type';
$string['ERR_COURSE_IDNUMBER']              = 'Invalid course idnumber';
$string['ERR_PERSON_SOURCE_ID']             = 'Invalid person source id';
$string['ERR_ENROLL_ROLETYPE_NOMAP']        = 'No mapping for roletype configured';
$string['ERR_ENROLL_ROLETYPE_BADMAP']       = 'Mapping for roletype invalid';
$string['ERR_ENROLL_FAIL']                  = 'Failed to enroll person in course section';
$string['ERR_UNENROLL_FAIL']                = 'Failed to unenroll person from course section';
$string['ERR_CREATE_PARENT_COURSE']         = 'Failed to create cross-list parent course';
$string['ERR_CREATE_CHLID_COURSE']          = 'Failed to add child course to metacourse';
$string['ERR_CREATE_CROSSLIST_GROUP']       = 'Failed to create course group';
$string['ERR_UPDATE_CROSSLIST_GROUP']       = 'Failed to update cross-list with course group id';


/* INFO STRINGS */
$string['INF_USERDELETE_NOACTION']          = 'No action taken for delete user recstatus';
$string['INF_USERCREATE_NOACTION']          = 'No action taken for create user';

$string['INF_TOOLS_IMPORT_BEGIN']           = 'Starting import of uploaded file...';
$string['INF_TOOLS_IMPORT_STATUS']          = 'Status: $a percent completed';

 
$string['INF_CRON_START']                   = 'SHEBanG cron: Beginning cron task.';
$string['INF_CRON_FINISH']                  = 'SHEBanG cron: Finished cron task.';
$string['INF_CRON_MONITOR_START']           = 'SHEBanG cron: Monitor task begins.';
$string['INF_CRON_MONITOR_FINISH']          = 'SHEBanG cron: Monitor task finished.';
$string['INF_CRON_MONITOR_DISABLED']        = 'SHEBanG cron: Not enabled. Exiting.';
$string['INF_CRON_MONITOR_WRONGDAY']        = 'SHEBanG cron: Not today. Exiting.';
$string['INF_CRON_MONITOR_WRONGTIME']       = 'SHEBanG cron: Not in window. Exiting';
$string['INF_CRON_MONITOR_MSGTHRESHOLD']    = 'SHEBanG cron: Last message time threshold not exceeded. Exiting.';
$string['INF_CRON_MONITOR_NOTICETHRESHOLD'] = 'SHEBanG cron: Last notice time threshold not exceeded. Exiting.';
$string['INF_CRON_MONITOR_NOTICESENT']      = 'SHEBanG cron: Email notice sent to $a.';
