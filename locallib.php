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

    require_once("$CFG->dirroot/course/lib.php");
    require_once("$CFG->dirroot/group/lib.php");
    require_once("$CFG->dirroot/lib/weblib.php");



    /**
     * Utility class to process LMB messages
     */
    class enrol_shebang_processor
    {


        /* -------------------------------------------------------------------
         * Class constants
         */

        const PLUGIN_NAME                       = 'enrol_shebang';
        const PLUGIN_PATH                       = '/enrol/shebang';

        const ROLETYPE_LEARNER                  = '01';
        const ROLETYPE_INSTRUCTOR               = '02';
        const ROLETYPE_CONTENTDEV               = '03';
        const ROLETYPE_MEMBER                   = '04';
        const ROLETYPE_MANAGER                  = '05';
        const ROLETYPE_MENTOR                   = '06';
        const ROLETYPE_ADMINISTRATOR            = '07';
        const ROLETYPE_TEACHINGASST             = '08';

        const IDTYPE_PERSON                     = '1';
        const IDTYPE_GROUP                      = '2';

        const RECSTATUS_ADD                     = '1';
        const RECSTATUS_UPDATE                  = '2';
        const RECSTATUS_DELETE                  = '3';

        const STATUS_INACTIVE                   = '0';
        const STATUS_ACTIVE                     = '1';

        const MKDIR_MODE                        = 02770;
        const INFILE_BLOCK_SIZE                 = 32768;

        const LOGFILE_BASENAME_PROCESS          = 'process_log.';
        const LOGFILE_BASENAME_MESSAGE          = 'message_log.';

        const DATEFMT_LOG_FILEX                 = 'Ymd';
        const DATEFMT_LOG_ENTRY                 = 'Y-m-d\TH:i:s O';
        const DATEFMT_SQL_VALUE                 = 'Y-m-d H:i:s';

        const LMBTAG_PROPERTIES                 = 'PROPERTIES';
        const LMBTAG_GROUP                      = 'GROUP';
        const LMBTAG_PERSON                     = 'PERSON';
        const LMBTAG_MEMBERSHIP                 = 'MEMBERSHIP';

        const LMBTAG_TYPEVALUE_COLLEGE          = 'COLLEGE';
        const LMBTAG_TYPEVALUE_DEPT             = 'DEPARTMENT';
        const LMBTAG_TYPEVALUE_TERM             = 'TERM';
        const LMBTAG_TYPEVALUE_COURSE           = 'COURSE';
        const LMBTAG_TYPEVALUE_SECTION          = 'COURSESECTION';
        const LMBTAG_TYPEVALUE_CROSSLIST        = 'CROSSLISTEDSECTION';
        const LMBTAG_TYPEVALUE_SEGMENT          = 'SEGMENT';

        /*
         * Moodle database entity (table) names
         */
        const MOODLENT_USER                     = 'user';
        const MOODLENT_COURSE                   = 'course';
        const MOODLENT_COURSE_CATEGORY          = 'course_categories';
        const MOODLENT_COURSE_SECTION           = 'course_sections';
        const MOODLENT_ROLE_ASSIGNMENT          = 'role_assignments';
        const MOODLENT_GROUP                    = 'groups';
        const MOODLENT_ENROL                    = 'enrol';
        const MOODLENT_USER_ENROL               = 'user_enrolments';

        /*
         * Plugin database entity (table) names
         */
        const SHEBANGENT_TERM                   = 'enrol_shebang_term';
        const SHEBANGENT_PERSON                 = 'enrol_shebang_person';
        const SHEBANGENT_SECTION                = 'enrol_shebang_section';
        const SHEBANGENT_MEMBER                 = 'enrol_shebang_member';
        const SHEBANGENT_CROSSLIST              = 'enrol_shebang_crosslist';

        /*
         * Config value options
         */
        const OPT_PERSON_DELETE_DELETE          = 'delete';
        const OPT_PERSON_DELETE_UNENROL         = 'unenrol';

        const OPT_PERSON_USERNAME_EMAIL         = 'email';
        const OPT_PERSON_USERNAME_USERID_EMAIL  = 'userid_email';
        const OPT_PERSON_USERNAME_USERID_LOGON  = 'userid_logon';
        const OPT_PERSON_USERNAME_USERID_SCTID  = 'userid_sctid';

        const OPT_PERSON_PASSWORD_USERID_LOGON  = 'userid_logon';
        const OPT_PERSON_PASSWORD_USERID_SCTID  = 'userid_sctid';

        const OPT_PERSON_LOCALITY_DEF           = 'def';
        const OPT_PERSON_LOCALITY_MSG           = 'msg';
        const OPT_PERSON_LOCALITY_IFF           = 'iff';

        const OPT_AUTH_SHIBBOLETH               = 'shibboleth';
        const OPT_AUTH_SHIBBUNCIF               = 'shibbuncif';
        const OPT_AUTH_MANUAL                   = 'manual';
        const OPT_AUTH_NOLOGIN                  = 'nologin';

        const OPT_COURSE_CATEGORY_TERM          = 'term';
        const OPT_COURSE_CATEGORY_DEPT          = 'dept';
        const OPT_COURSE_CATEGORY_NEST          = 'nest';
        const OPT_COURSE_CATEGORY_PICK          = 'pick';

        const OPT_CROSSLIST_METHOD_MERGE        = 'merge';
        const OPT_CROSSLIST_METHOD_META         = 'meta';

        const OPT_SECURE_METHOD_BASIC           = 'basic';
        const OPT_SECURE_METHOD_DIGEST          = 'digest';

        /*
         * Config value defaults
         */
        const DEF_PERSON_COUNTRY                = 'US';
        const DEF_PERSON_AUTH_METHOD            = 'nologin';

        const DEF_MONITOR_START_HOUR            = '9';
        const DEF_MONITOR_START_MIN             = '00';
        const DEF_MONITOR_STOP_HOUR             = '17';
        const DEF_MONITOR_STOP_MIN              = '00';
        const DEF_MONITOR_THRESHOLD             = '30';

        const MAX_MONITOR_THRESHOLD             = 1440;
        const MIN_MONITOR_THRESHOLD             = 5;

        const MONITOR_NOTICES_INTERVAL          = 30;

        const MAX_COURSE_PARENT_STRIPLEAD       = 10;

        const MAX_LEN_PERSON_STREET             = 70;



        /* -------------------------------------------------------------------
         * Class member vars
         */

        /**
         * Plugin config values
         *
         * @var stdClass
         * @access private
         */
        private $config                = null;

        /**
         * Reference var to Moodle's global $CFG
         *
         * @var stdClass
         * @access private
         */
        private $moodleConfigs          = null;

        /**
         * Reference var to Moodle's global $DB
         *
         * @var moodle_database
         * @access private
         */
        private $moodleDB               = null;

        /**
         * Where to log the LMB messages received
         *
         * @var string
         * @access private
         */
        private $messageLogPath         = '';

        /**
         * Resource for the LMB message log file
         *
         * @var resource
         * @access private
         */
        private $messageLogRes          = null;

        /**
         * Where to log the processing info messages
         *
         * @var string
         * @access private
         */
        private $processLogPath         = '';

        /**
         * Boolean to tell the parser_character_data() callback routine
         * whether to append to the $parseDataBuffer or not
         *
         * @var boolean
         * @access private
         */
        private $buffering              = false;

        /**
         * Buffers up the parsed XML
         *
         * @var string
         * @access private
         */
        private $parseDataBuffer        = '';

        /**
         * Return code from last message processing
         *
         * @var boolean
         * @access private
         */
        private $lastRetCode            = false;

        /**
         * When importing a Banner extract file, holds the <datetime> value
         *
         * @var int
         * @access private
         */
        private $importFileDatetime     = null;

        /**
         * Cache of roles, keyed by id
         *
         * @var array
         * @access private
         */
        private $roleCache              = array();

        /**
         * Did a course insert take place
         *
         * @var boolean
         * @access private
         */
        private $courseInserted         = false;

        /**
         * Array of recognized tokens used for fashioning course names
         *
         * @var array
         * @access public
         * @static
         */
        public static $courseNameTokens = array('/%termcode%/i', '/%termdesc%/i',
                                                '/%fullname%/i', '/%longname%/i', '/%shortname%/i', '/%sourceid%/i',
                                                '/%deptcode%/i', '/%deptname%/i',
                                                '/%parentcode%/i',
                                                '/%coursenum%/i', '/%sectionnum%/i');

        /**
         * Where do the process, message logs go
         *
         * @var string
         * @access private
         */
        private $logging_dirpath        = '';

        /**
         * Instance of the enrol plugin
         *
         * @var enrol_shebang_plugin
         * @access private
         */
        private $enrol_plugin           = null;


        /* -------------------------------------------------------------------
         * Class methods
         */


        /**
         * Constructor
         *
         * @uses $CFG, $DB
         */
        function __construct()
        {
            global $CFG, $DB;



            // Make a reference to the global configs
            $this->moodleConfigs    = $CFG;
            // Make a reference to the global database connection
            $this->moodleDB         = $DB;

            $this->config           = get_config(self::PLUGIN_NAME);
            $this->enrol_plugin     = enrol_get_plugin('shebang');

        } // __construct



        /**
         * Get plugin config value
         *
         * @access public
         * @param string    $name
         * @param mixed     $default
         * @return mixed
         */
        public function get_config($name, $default = null)
        {

            return isset($this->config->$name) ? $this->config->$name : $default;

        }



        /**
         * Accessor for security config - username
         *
         * @access public
         * @return string
         */
        public function getSecureUsername()
        {
            return $this->config->secure_username;
        }



        /**
         * Accessor for security config - password
         *
         * @access public
         * @return string
         */
        public function getSecurePassword()
        {
            return $this->config->secure_passwd;
        }



        /**
         * Accessor for security config - method
         *
         * @access public
         * @return string
         */
        public function getSecureMethod()
        {
            return $this->config->secure_method;
        }



        /**
         * Make sure process and message log files are present and writable
         *
         * @access private
         * @return void
         */
        private function prepare_logfiles()
        {

            // If alternate directory configured then
            // verify it exists and is writable
            if (!empty($this->config->logging_dirpath)) {
                $this->logging_dirpath = make_writable_directory(preg_replace('/\/+$/', '', trim($this->config->logging_dirpath)), false);
            }
            // If alternate directory not set or did not
            // verify as writable dir, then use default
            if (empty($this->logging_dirpath)) {
                $this->logging_dirpath = make_writable_directory($this->moodleConfigs->dataroot . "/" . self::PLUGIN_NAME, false);
            }
            // If directory still not set at this point
            // then neither alternate nor default verified
            if (empty($this->logging_dirpath)) {
                error_log(get_string('ERR_DATADIR_CREATE', self::PLUGIN_NAME));
                throw new moodle_exception('ERR_DATADIR_CREATE', self::PLUGIN_NAME);
            }

            $log_date_suffix        = date(self::DATEFMT_LOG_FILEX);
            $this->messageLogPath   = $this->logging_dirpath . "/" . self::LOGFILE_BASENAME_MESSAGE . $log_date_suffix;
            $this->processLogPath   = $this->logging_dirpath . "/" . self::LOGFILE_BASENAME_PROCESS . $log_date_suffix;

            // Touch the message log before we need it, so we can die if no joy
            if (!file_exists($this->messageLogPath) && !touch($this->messageLogPath)) {
                error_log(get_string('ERR_MESGLOG_NOOPEN', self::PLUGIN_NAME));
                throw new moodle_exception('ERR_MESGLOG_NOOPEN', self::PLUGIN_NAME);
            }
            // Touch the process log before we need it, so we can die if no joy
            if (!file_exists($this->processLogPath) && !touch($this->processLogPath)) {
                error_log(get_string('ERR_PROCLOG_NOOPEN', self::PLUGIN_NAME));
                throw new moodle_exception('ERR_PROCLOG_NOOPEN', self::PLUGIN_NAME);
            }

        } // prepare_logfiles



        /**
         * Write the LMB posted message contents to a text file in the Moodle
         * data directory.
         *
         * @access  private
         * @param   string      $msg_id     LMB message id from HTTP headers
         * @param   string      $data       XML that was *POST*ed by LMB
         * @param   boolean     $keep_lock  Whether or not to keep file lock
         * @param   boolean     $result     Success or failure
         * @return  void
         */
        private function log_lmb_message($msg_id, $data, $keep_lock = false, $result = null)
        {

            /* We run the risk of bogging down the message processing, but
             * it will help to serialize the processing of the messages
             * coming in from the LMB. No logic is being done here, just
             * lock-write-release. The log file name is a canonical form
             * using the date, e.g. 'message_log.YYYYMMDD' and is in our
             * log directory in the Moodle data directory.
             */

            if (isset($this->config->logging_nologlock) && !empty($this->config->logging_nologlock)) {
                // Defeat the locking if told to do so
                $keep_lock = false;
            }

            if ((null == $this->messageLogRes) && (!($this->messageLogRes = fopen($this->messageLogPath, 'a')) || !flock($this->messageLogRes, LOCK_EX))) {
                throw new Exception(get_string('ERR_MESGLOG_NOOPEN', self::PLUGIN_NAME));
            }

            $log_time = date(self::DATEFMT_LOG_ENTRY);
            $log_mesg = "#{$log_time}#{$msg_id}";
            if ($result !== null) {
                $log_mesg .= "#" . ($result ? 'success' : 'failure');
            }
            $log_mesg .= "\n";
            fwrite($this->messageLogRes, $log_mesg);

            if ($this->config->logging_logxml && $data !== null) {
                fwrite($this->messageLogRes, $data);
                fwrite($this->messageLogRes, "\n");
            }

            // If unlock fails (can it?) we got big problems,
            // so handle that here and die quickly
            if (!$keep_lock) {
                if (flock($this->messageLogRes, LOCK_UN) && fclose($this->messageLogRes)) {
                    $this->messageLogRes = null;
                } else {
                    error_log(get_string('ERR_MESGLOG_CLOSE', self::PLUGIN_NAME));
                    die();
                }
            }

        }  // log_lmb_message



        /**
         * Write the exception information to a log file in the Moodle data directory
         *
         * @access  private
         * @param   Exception     $exc        Exception to log
         * @param   unknown_type  $rc
         * @return  void
         */
        private function log_process_exception(Exception $exc)
        {

            if (!($fp = fopen($this->processLogPath, 'a'))) {
                die(get_string('ERR_PROCLOG_NOOPEN', self::PLUGIN_NAME));
            }

            fwrite($fp, date(self::DATEFMT_LOG_ENTRY)
                      . "|Exception Code: " . $exc->getCode() . "\n"
                      . "In File: " . $exc->getFile() . ":" . $exc->getLine() . "\n"
                      . "Message: " . $exc->getMessage() . (property_exists($exc, 'error') ? ", " . $exc->error : "") . "\n"
                      . $exc->getTraceAsString() . "\n");

            if (!fclose($fp))  {
                die(get_string('ERR_PROCLOG_CLOSE', self::PLUGIN_NAME));
            }

        } // log_process_exception



        /**
         * Write the processing information to a log file in the Moodle data directory
         *
         * @access  private
         * @param   string      $entity     Entity name
         * @param   string      $id         Id value of entity
         * @param   string      $op         Operation performed
         * @param   mixed       $rc         Success indicator (boolean) or information string
         * @return  void
         */
        private function log_process_message($entity, $id, $op, $rc)
        {

            if ($this->config->logging_onlyerrors && $rc === true) {
                return;
            }

            if (!($fp = fopen($this->processLogPath, 'a'))) {
                die(get_string('ERR_PROCLOG_NOOPEN', self::PLUGIN_NAME));
            }

            if ($rc === true)
                $info = 'success';
            elseif ($rc === false)
                $info = 'failure';
            else
                $info = $rc;

            fwrite($fp, date(self::DATEFMT_LOG_ENTRY) . "|{$entity}|{$op}|{$id}|$info\n");

            if (!fclose($fp)) {
                die(get_string('ERR_PROCLOG_CLOSE', self::PLUGIN_NAME));
            }

        }  // log_process_message



        /**
         * Import the LMB (IMS) messages in the file specified.
         *
         * @access public
         * @param  string         $infile    Path to the input file
         * @param  progress_trace $progress  Name of the callback routine to which to report progress
         * @return boolean
         */
        public function import_lmb_file(stored_file $stored_file, progress_bar $progress = null)
        {

            $fh                =
            $xml_parser        = null;
            $progress_counter  = new stdClass();



            // Regardless of PHP version certain modules and classes have
            // to be present
            if (!function_exists('xml_parser_create') || !class_exists('DOMDocument') || !class_exists('DOMXPath')) {
                error_log(get_string('ERR_XMLLIBS_NOTFOUND', self::PLUGIN_NAME));
                throw new moodle_exception('ERR_XMLLIBS_NOTFOUND', self::PLUGIN_NAME);
            }

            if (!$this->config) {
                // No configs found
                error_log(get_string('ERR_CONFIGS_NOTSET', self::PLUGIN_NAME));
                throw new moodle_exception('ERR_CONFIGS_NOTSET', self::PLUGIN_NAME);
            }

            // Set up process and message log files
            try { $this->prepare_logfiles(); }
            catch (moodle_exception $exc) {
                $exc->link = $this->moodleConfigs->wwwroot . self::PLUGIN_PATH . '/tools.php?task=import';
                throw $exc;
            }

            // Fetch the roles and keep them for the duration
            $roles_array = get_all_roles();
            foreach ($roles_array as $role) {
                $this->roleCache[$role->id] = $role;
            }
            unset($roles_array);

            // Need to know some global configs in case of new Moodle recs
            // being inserted
            if (!isset($this->moodleConfigs->mnet_localhost_id)) {
                include_once("{$this->moodleConfigs->dirroot}/mnet/lib.php");
                $env = new mnet_environment();
                $env->init(); unset($env);
            }

            set_time_limit(0);
            $progress_counter->block_size   = self::INFILE_BLOCK_SIZE;
            $progress_counter->blocks_total = ceil($stored_file->get_filesize() / self::INFILE_BLOCK_SIZE);
            $progress_counter->blocks_read  = 0;

            try
            {
                // No file, no joy
                if (false == ($fh = $stored_file->get_content_file_handle())) {
                    throw new Exception(get_string('ERR_DATAFILE_NOOPEN', self::PLUGIN_NAME));
                }

                if (!($xml_parser = xml_parser_create())) {
                    throw new Exception(get_string('ERR_XMLPARSER_CREATE', self::PLUGIN_NAME));
                }

                // Do the case folding so all string matches and xqueries can
                // be consistently uppercase. Skip white space (if possible)
                // and don't skip start tags
                xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
                xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, true);
                xml_parser_set_option($xml_parser, XML_OPTION_SKIP_TAGSTART, 0);

                xml_set_element_handler($xml_parser, array($this, 'parser_start_element'), array($this, 'parser_end_element'));
                xml_set_character_data_handler($xml_parser, array($this, 'parser_character_data'));

                while (true == ($data = fread($fh, self::INFILE_BLOCK_SIZE))) {

                    $progress_counter->blocks_read++;
                    if (!xml_parse($xml_parser, $data, feof($fh))) {
                        throw new Exception(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
                    }

                    unset($data);

                    if ($progress != null) {
                        $progress->update($progress_counter->blocks_read, $progress_counter->blocks_total, get_string('INF_TOOLS_IMPORT_PROGRESS', self::PLUGIN_NAME, $progress_counter));
                    }

                } // while (true == ($data = fread...

                // If any courses inserted in this
                // import, fix up the sort order
                if ($this->courseInserted) {
                    $this->courseInserted = false;
                    fix_course_sortorder();
                    cache_helper::purge_by_event('changesincourse');
                }

                // Clean up before leaving
                xml_parser_free($xml_parser);
                fclose($fh);

                return true;
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                if ($fh != null) {
                    fclose($fh);
                }
                if ($xml_parser != null) {
                    xml_parser_free($xml_parser);
                }
                if ($progress != null) {
                  $progress->update($progress_counter->blocks_read, $progress_counter->blocks_total, get_string('error'));
                }
                return false;
            }

        } // import_lmb_file



        /**
         * Import an LMB (IMS) message into Moodle
         *
         * @access public
         * @param  string       $data       XML that was *POST*ed by LMB
         * @param  string       $msg_id     LMB message id from HTTP headers
         * @return boolean
         */
        public function import_lmb_message($data = '', $msg_id = '')
        {

            // Regardless of PHP version certain modules and classes have
            // to be present
            if (!function_exists('xml_parser_create') || !class_exists('DOMDocument') || !class_exists('DOMXPath')) {
                error_log(get_string('ERR_XMLLIBS_NOTFOUND', self::PLUGIN_NAME));
                return false;
            }

            if (!$this->config) {
                // No configs found
                error_log(get_string('ERR_CONFIGS_NOTSET', self::PLUGIN_NAME));
                return false;
            }

            // Set up process and message log files
            try { $this->prepare_logfiles(); }
            catch (moodle_exception $exc) {
                return false;
            }

            // Fetch the roles and keep them for the duration
            $roles_array = get_all_roles();
            foreach ($roles_array as $role) {
                $this->roleCache[$role->id] = $role;
            }
            unset($roles_array);

            // Need to know some global configs in case of new Moodle recs
            // being inserted
            if (!isset($this->moodleConfigs->mnet_localhost_id)) {
                include_once("{$this->moodleConfigs->dirroot}/mnet/lib.php");
                $env = new mnet_environment();
                $env->init(); unset($env);
            }

            // No payload, no joy
            if (empty($data)) {
                return false;
            }

            // Reset this value from any previously received msg
            $this->importFileDatetime = '';

            $xml_parser = null;
            try
            {
                $this->log_lmb_message($msg_id, $data, true, null);

                if (!($xml_parser = xml_parser_create())) {
                    throw new Exception(get_string('ERR_XMLPARSER_CREATE', self::PLUGIN_NAME));
                }

                xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
                xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, false);
                xml_parser_set_option($xml_parser, XML_OPTION_SKIP_TAGSTART, 0);

                xml_set_element_handler($xml_parser, array($this, 'parser_start_element'), array($this, 'parser_end_element'));
                xml_set_character_data_handler($xml_parser, array($this, 'parser_character_data'));

                if (!xml_parse($xml_parser, $data, true)) {
                    throw new Exception(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
                }

                // If any courses inserted in this
                // message, fix up the sort order
                if ($this->courseInserted) {
                    $this->courseInserted = false;
                    fix_course_sortorder();
                    // The call to cache_helper::purge_by_event() is called after a call
                    // to fix_course_sortorder(), but this would risk the cache getting
                    // purged too frequently as messages are sent, so will leave commented
                    //cache_helper::purge_by_event('changesincourse');
                }

                $this->log_lmb_message($msg_id, null, false, $this->lastRetCode);
                xml_parser_free($xml_parser);

                if (!$this->lastRetCode) {
                    $this->notify_message_error($msg_id);
                }

                return $this->lastRetCode;
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                if ($this->messageLogRes != null) {
                    $this->log_lmb_message($msg_id, 'failure', false);
                }
                if ($xml_parser != null) {
                    xml_parser_free($xml_parser);
                }
                $this->notify_message_error($msg_id);
                return false;
            }

        }  // import_lmb_message



        /**
         * Callback routine to handle an opening element tag
         *
         * @access  private
         * @param   resource    $parser     The xml resource type returned from xml_create_parser()
         * @param   string      $name       Element name
         * @param   array       $attrs      Array of attributes (strings) for this element
         * @return  void
         */
        private function parser_start_element($parser, $name, $attrs)
        {

            /* We expect to see, in order, the ENTERPRISE (root) element, then
             * PROPERTIES (first child), and so on. We want to start buffering
             * when we encounter a GROUP, PERSON, or MEMBERSHIP opening tag.
             * All we do is continue to buffer up the xml that makes up one of
             * the desired elements, and when the close tag is encountered, a
             * call to the corresponding import_[name]_element routine handles
             * the import.
             */

            switch ($name) {
                case self::LMBTAG_PROPERTIES:
                case self::LMBTAG_GROUP:
                case self::LMBTAG_PERSON:
                case self::LMBTAG_MEMBERSHIP:
                    $this->parseDataBuffer = '';
                    $this->buffering = true;
                    break;
              //default: Don't care about this one
            }

            // Not buffering, no more to do
            if (!$this->buffering) {
                return;
            }

            $attr_list = '';
            if ($attrs) {
                reset($attrs);
                while (false !== ($keyval = each($attrs))) {
                    list($key, $val) = $keyval;
                    // Revert any '&' back to '&amp;' so the
                    // text can be put into an XMLDocument
                    $attr_list .= " {$key}=\"" . preg_replace(array('/</', '/>/', '/&/'), array('&lt;', '&gt;', '&amp;'), $val) . "\"";
                }
            }
            $this->parseDataBuffer .= "<{$name}{$attr_list}>";

        } // parser_start_element



        /**
         * Callback routine to handle a closing element tag
         *
         * @access  private
         * @param   resource    $parser     The xml resource type returned from xml_create_parser()
         * @param   string      $name       Element name
         * @return  void
         */
        private function parser_end_element($parser, $name)
        {

            /* We expect to see, in order, the ENTERPRISE (root) element, then
             * PROPERTIES (first child), and so on. We want to start buffering
             * when we encounter a GROUP, PERSON, or MEMBERSHIP opening tag.
             * All we do is continue to buffer up the xml that makes up one of
             * the desired elements, and when the close tag is encountered, a
             * call to the corresponding import_[name]_element routine handles
             * the import.
             */

            // Not buffering, don't care
            if (!$this->buffering) {
                return;
            }

            // Stick it on the end of the buffer
            $this->parseDataBuffer .= "</{$name}>";

            // See if it's one for which an action takes place
            switch($name)
            {

                case self::LMBTAG_PROPERTIES:
                case self::LMBTAG_GROUP:
                case self::LMBTAG_PERSON:
                case self::LMBTAG_MEMBERSHIP:

                    $doc = new DOMDocument();
                    try
                    {
                        $doc->loadXML($this->parseDataBuffer);
                        $method = "import_" . strtolower($name) . "_element";
                        $this->lastRetCode = $this->$method($doc);
                    }
                    catch (Exception $exc)
                    {
                        $this->log_process_exception($exc);
                        $this->log_process_message($name, "", "", $this->parseDataBuffer);
                        $this->lastRetCode = false;
                    }

                    unset($doc);
                    $this->buffering = false;
                    $this->parseDataBuffer = '';
                    break;

              //default: Nothing more to do otherwise

            }

        } // parser_end_element



        /**
         * Callback routine to handle the character data collection
         * for text that falls between an opening tag, and either its
         * corresponding closing tag, or the opening tag of the first
         * child element (usually whitespace chars in the latter case)
         *
         * @access  private
         * @param   resource        $parser     The xml resource type returned from xml_create_parser()
         * @param   string          $data       Character data
         * @return  void
         */
        private function parser_character_data($parser, $data)
        {

            if ($this->buffering) {
                // Want to strip out the extra \n and space chars that
                // appear between a closing element tag and the next
                // element's opening tag. Also, because the xml_parser
                // is translating '&amp;' into '&', we need to revert
                // all of them before putting into an XMLDocument
                $this->parseDataBuffer .= preg_replace(array('/\A\s+\z/', '/</', '/>/', '/&/'), array('', '&lt;', '&gt;', '&amp;'), $data);
            }

        } // parser_character_data



        /**
         * Fetches the datetime sub-element value and assigns that to the $importFileDatetime member var
         *
         * @access  private
         * @param   DOMDocument     $doc        The XML document containing the <properties> element
         * @return  boolean                     Success or failure
         */
        private function import_properties_element(DOMDocument $doc)
        {

            $xpath  = new DOMXPath($doc);
            $this->importFileDatetime = strtotime($xpath->evaluate("string(/PROPERTIES/DATETIME)"));

            return true;

        } // import_properties_element



        /**
         * Import a <group> element
         *
         * @access  private
         * @param   DOMDocument     $doc        The XML document containing the <group> element
         * @return  boolean                     Success or failure
         */
        private function import_group_element(DOMDocument $doc)
        {

            /* Still need to determine the type of <group> encountered,
             * whether a term, course, course section, etc., so look
             * at the first <grouptype>/<typevalue> that has a level
             * value of 1.
             */

            $rc          = false;
            $xpath       = new DOMXPath($doc);
            $type_value  = strtoupper($xpath->evaluate("string(/GROUP/GROUPTYPE[TYPEVALUE[@LEVEL = \"1\"]][1]/TYPEVALUE)"));

            switch($type_value)
            {
                case self::LMBTAG_TYPEVALUE_TERM:
                case self::LMBTAG_TYPEVALUE_SECTION:
                    $method = "import_group_" . strtolower($type_value);
                    $rc = $this->$method($doc);
                    break;
                /*
                We do not care too much about the cross-listed section group
                element because it does not provide any useful information,
                other than that somewhere later on we will receive membership
                elements that specify which course sections belong--and that
                is where we can get useful information.
                case self::LMBTAG_TYPEVALUE_CROSSLIST:

                Not receiving these message elements in our implementation of
                Banner...
                case self::LMBTAG_TYPEVALUE_COLLEGE:
                case self::LMBTAG_TYPEVALUE_DEPT:
                case self::LMBTAG_TYPEVALUE_COURSE:
                case self::LMBTAG_TYPEVALUE_SEGMENT:
                    break;
                */
            }

            return $rc;

        } // import_group_element



        /**
         * Import an academic term into Moodle
         *
         * @access  private
         * @param   DOMDocument     $doc    XML document containing the term <group> element
         * @return  boolean                 Success or failure
         */
        private function import_group_term(DOMDocument $doc)
        {

            /* Moodle does not use the concept of an academic term, but
             * still can use it so admins can map course sections for a
             * given term to a Moodle course category
             *
             * Collect the values for source name, source id, short desc,
             * long desc, begin (date), and end (date).
             */

            $rc          = false;
            $lmb_data    = new stdClass();
            $xpath       = new DOMXPath($doc);

            $lmb_data->source_id     = $xpath->evaluate("string(/GROUP/SOURCEDID/ID)");
            $lmb_data->desc_short    = $xpath->evaluate("string(/GROUP/DESCRIPTION/SHORT)");
            $lmb_data->desc_long     = $xpath->evaluate("string(/GROUP/DESCRIPTION/LONG)");
            $lmb_data->begin_date    = $xpath->evaluate("string(/GROUP/TIMEFRAME/BEGIN)");
            $lmb_data->end_date      = $xpath->evaluate("string(/GROUP/TIMEFRAME/END)");
            $lmb_data->category_id   = 0;
            $lmb_data->insert_date   =
            $lmb_data->update_date   = date(self::DATEFMT_SQL_VALUE);

            // Insert it or update it
            try
            {
                if (false === ($old_rec = $this->moodleDB->get_record(self::SHEBANGENT_TERM, array('source_id' => $lmb_data->source_id)))) {
                    $op = 'insert';
                    $rc = $this->moodleDB->insert_record(self::SHEBANGENT_TERM, $lmb_data, false);
                } else {
                    $lmb_data->id = $old_rec->id; unset($lmb_data->insert_date); unset($lmb_data->category_id);
                    $op = 'update';
                    $rc = $this->moodleDB->update_record(self::SHEBANGENT_TERM, $lmb_data);
                }
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                $rc = false;
            }

            $this->log_process_message(self::SHEBANGENT_TERM, $lmb_data->source_id, $op, $rc);
            return $rc;

        } // import_group_term



        /**
         * Import a course-section into Moodle
         *
         * @access  private
         * @param   DOMDocument     $doc    XML document containing the course-section <group> element
         * @return  boolean                 Success or failure
         */
        private function import_group_coursesection(DOMDocument $doc)
        {

            /* What Banner calls a course section is just a course in
             * Moodle.
             */

            $lmb_data    = new stdClass();
            $xpath       = new DOMXPath($doc);

            $lmb_data->source_id         = $xpath->evaluate("string(/GROUP/SOURCEDID/ID)");
            $lmb_data->term              = $xpath->evaluate("string(/GROUP/RELATIONSHIP[@RELATION = \"1\"][LABEL = \"Term\"]/SOURCEDID/ID)");
            $lmb_data->course_source_id  = $xpath->evaluate("string(/GROUP/RELATIONSHIP[@RELATION = \"1\"][LABEL = \"Course\"]/SOURCEDID/ID)");
            $lmb_data->desc_short        = $xpath->evaluate("string(/GROUP/DESCRIPTION/SHORT)");
            $lmb_data->desc_long         = $xpath->evaluate("string(/GROUP/DESCRIPTION/LONG)");
            $lmb_data->desc_full         = $xpath->evaluate("string(/GROUP/DESCRIPTION/FULL)");
            $lmb_data->dept_name         = $xpath->evaluate("string(/GROUP/ORG/ORGUNIT)");
            $lmb_data->begin_date        = $xpath->evaluate("string(/GROUP/TIMEFRAME/BEGIN)");
            $lmb_data->end_date          = $xpath->evaluate("string(/GROUP/TIMEFRAME/END)");
            $lmb_data->recstatus         = $xpath->evaluate("string(/GROUP/@RECSTATUS)");
            $lmb_data->insert_date       =
            $lmb_data->update_date       = date(self::DATEFMT_SQL_VALUE);



            // Validate the source_id
            if (empty($lmb_data->source_id)) {
                $this->log_process_message(self::SHEBANGENT_SECTION, "", "", get_string('ERR_COURSE_IDNUMBER', self::PLUGIN_NAME));
                return false;
            }

            // If recstatus not present, default is add (1)
            if (!isset($lmb_data->recstatus) || empty($lmb_data->recstatus)) $lmb_data->recstatus = self::RECSTATUS_ADD;


            if ($this->config->course_parent_striplead) {
                $lmb_data->course_source_id = substr($lmb_data->course_source_id, $this->config->course_parent_striplead);
            }

            // Insert or update the staging rec
            try
            {
                if (false === ($old_rec =  $this->moodleDB->get_record(self::SHEBANGENT_SECTION, array('source_id' => $lmb_data->source_id)))) {
                    $op = 'insert';
                    $rc = $this->moodleDB->insert_record(self::SHEBANGENT_SECTION, $lmb_data, false);
                } else {
                    $lmb_data->id = $old_rec->id; unset($lmb_data->insert_date);
                    $op = 'update';
                    $rc = $this->moodleDB->update_record(self::SHEBANGENT_SECTION, $lmb_data);
                }
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                $rc = false;
            }
            $this->log_process_message(self::SHEBANGENT_SECTION, $lmb_data->source_id, $op, $rc);

            // Bailout here if no joy
            if (false === $rc) {
                return false;
            }

            // See if there is a Moodle course rec
            $course_rec = $this->moodleDB->get_record(self::MOODLENT_COURSE, array('idnumber' => $lmb_data->source_id));
            if ($lmb_data->recstatus == self::RECSTATUS_DELETE) {

                if (false === $course_rec) {
                    // Delete a course that is not there, no-op
                    $this->log_process_message(self::MOODLENT_COURSE, $lmb_data->source_id, 'delete', get_string('INF_COURSEDELETE_NOACTION', self::PLUGIN_NAME));

                } else {
                    // Will not delete a course, but will make
                    // it unavailable and remove association
                    $course_rec->visible    =
                    $course_rec->visibleold = 0;
                    $course_rec->idnumber   = '';
                    try { $this->moodleDB->update_record(self::MOODLENT_COURSE, $course_rec); }
                    catch (Exception $exc) { $this->log_process_exception($exc); $rc = false; }
                }

            } else {

                // Add or update the course
                if (false === $course_rec) {
                    $rc = $this->insert_course($lmb_data);
                    $this->log_process_message(self::MOODLENT_COURSE, $lmb_data->source_id, 'insert', $rc);
                } else {
                    $rc = $this->update_course($lmb_data, $course_rec);
                    $this->log_process_message(self::MOODLENT_COURSE, $course_rec->id, 'update', $rc);
                }

            } // else recstatus == RECSTATUS_ADD | RECSTATUS_UPDATE

            return $rc;

        } // import_group_coursesection



        /**
         * Insert a new Moodle course rec with data from the Banner/LMB rec
         *
         * @access  private
         * @param   stdClass    $lmb_data   The Banner/LMB msg rec
         * @return  boolean                 Success or failure
         */
        private function insert_course(stdClass $lmb_data)
        {

            // Course isn't there, so fix up a minimal one and add it
            $new_course_data = $this->create_empty_course_object();

            // Determine from the configs the category to use for the new course
            switch($this->config->course_category)
            {

                case self::OPT_COURSE_CATEGORY_DEPT:
                    $new_course_data->category = $this->get_category_by_name($lmb_data->dept_name);
                    break;

                case self::OPT_COURSE_CATEGORY_TERM:
                    $new_course_data->category = $this->get_category_by_term($lmb_data->term);
                    break;

                case self::OPT_COURSE_CATEGORY_NEST:
                    $new_course_data->category = $this->get_category_by_name($lmb_data->dept_name, true, $this->get_category_by_term($lmb_data->term));
                    break;

                default: // OPT_COURSE_CATEGORY_PICK
                    if (   isset($this->config->course_category_id) && !empty($this->config->course_category_id)
                        && $this->moodleDB->record_exists(self::MOODLENT_COURSE_CATEGORY, array('id' => $this->config->course_category_id))) {
                        $new_course_data->category = $this->config->course_category_id;
                    }

            }

            // If the course category could not be resolved then leave
            if (0 === ($new_course_data->category)) {
                $this->log_process_message(self::MOODLENT_COURSE, $lmb_data->source_id, 'insert', get_string('ERR_COURSECAT_ZERO', self::PLUGIN_NAME));
                return false;
            };


            // Figure the names from the config'd patterns.
            if ($this->config->course_fullname_pattern) {
                $new_course_data->fullname = $this->replace_name_tokens($this->config->course_fullname_pattern, $lmb_data);
            } else {
                $new_course_data->fullname = $lmb_data->desc_full;
            }
            if ($this->config->course_fullname_uppercase) {
                $new_course_data->fullname = strtoupper($new_course_data->fullname);
            }

            if ($this->config->course_shortname_pattern) {
                $new_course_data->shortname = $this->replace_name_tokens($this->config->course_shortname_pattern, $lmb_data);
            } else {
                $new_course_data->shortname = $lmb_data->desc_long;
            }
            if ($this->config->course_shortname_uppercase) {
                $new_course_data->shortname = strtoupper($new_course_data->shortname);
            }

            $new_course_data->idnumber    = $lmb_data->source_id;
            $new_course_data->summary     = $lmb_data->desc_full;

            $new_course_data->startdate   = empty($lmb_data->begin_date) ? 0 : strtotime($lmb_data->begin_date);
            $new_course_data->visible     =
            $new_course_data->visibleold  = $this->config->course_hidden ? 0 : 1;

            // Adjust numsections to fit the number of weeks if
            // that's the preference. Get the start/end dates from
            // the LMB data
            if ($this->config->course_sections_equal_weeks && !empty($lmb_data->end_date) && !empty($new_course_data->startdate)) {
                $new_course_data->numsections = ceil(abs(strtotime($lmb_data->end_date) - $new_course_data->startdate) / WEEKSECS);
            }

            // Insert the course, leave if that fails
            try
            {
                // Call this plugin's create_course method which mimics
                // the Moodle course/lib.php routine, but does not do
                // any more in the database than is needed. Set a bool
                // to indicate a call fix_course_sortorder is needed.
                $course = $this->create_course($new_course_data);
                $this->courseInserted = true;

                // Set up an enrol plugin for this course. In the call to create_course()
                // an attempt was made to create an enrol instance for the course, but
                // because the config 'defaultenrol' is not set, it does nothing. It must
                // be done here, passing the enrol start/end date values in an array
                $enrol_properties = array(
                        'enrolstartdate' => empty($lmb_data->begin_date) ? 0 : strtotime($lmb_data->begin_date),
                        'enrolenddate'   => empty($lmb_data->end_date)   ? 0 : strtotime($lmb_data->end_date)
                );
                if (null == $this->enrol_plugin->add_instance($course, $enrol_properties)) {
                    $this->log_process_message(self::MOODLENT_ENROL, $course->id, 'insert', get_string('ERR_ENROL_INSERT', PLUGIN_NAME));
                    return false;
                }
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                return false;
            }

            return true;

        } // insert_course



        /**
         * Update the existing Moodle course rec with data from the Banner/LMB rec
         *
         * @access  private
         * @param   stdClass    $lmb_data       The Banner/LMB msg rec
         * @param   stdClass    $course_rec     The existing Moodle course rec
         * @return  boolean                     Success or failure
         */
        private function update_course(stdClass $lmb_data, stdClass $course_rec)
        {

            // There is an existing Moodle course record ($course_rec)
            // for the presented idnumber (sourcedid)

            if ($this->config->course_fullname_changes) {
                if ($this->config->course_fullname_pattern) {
                    $course_rec->fullname = $this->replace_name_tokens($this->config->course_fullname_pattern, $lmb_data);
                } else {
                    $course_rec->fullname = $lmb_data->desc_full;
                }
                if ($this->config->course_fullname_uppercase) {
                    $course_rec->fullname = strtoupper($course_rec->fullname);
                }
            }

            if ($this->config->course_shortname_changes) {
                if ($this->config->course_shortname_pattern) {
                    $course_rec->shortname = $this->replace_name_tokens($this->config->course_shortname_pattern, $lmb_data);
                } else {
                    $course_rec->shortname = $lmb_data->desc_long;
                }
                if ($this->config->course_shortname_uppercase) {
                    $course_rec->shortname = strtoupper($course_rec->shortname);
                }
            }

            $course_rec->startdate      = strtotime($lmb_data->begin_date);
            $course_rec->timemodified   = strtotime($lmb_data->update_date);

            // Update the existing Moodle course
            try
            {
                // Going to do simple row update rather than call the update_course()
                // method, since not changing category, sort order, etc.
                $this->moodleDB->update_record(self::MOODLENT_COURSE, $course_rec);
                // Might need to adjust the enrol start/end date values in the
                // course enrol instance.
                $enrol_properties = array(
                        'enrolstartdate' => strtotime($lmb_data->begin_date),
                        'enrolenddate'   => strtotime($lmb_data->end_date)
                );
                if (null == ($instance_rec = $this->moodleDB->get_record(self::MOODLENT_ENROL, array('courseid' => $course_rec->id, 'enrol' => $this->enrol_plugin->get_name())))) {
                    $this->enrol_plugin->add_instance($course_rec, $enrol_properties);
                } else {
                    $instance_rec->enrolstartdate = $enrol_properties['enrolstartdate'];
                    $instance_rec->enrolenddate   = $enrol_properties['enrolenddate'];
                    $this->moodleDB->update_record(self::MOODLENT_ENROL, $instance_rec);
                }

                events_trigger('course_updated', $course_rec);
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                return false;
            }

            return true;

        } // update_course



        /**
         * Import a person into Moodle
         *
         * @access  private
         * @param   DOMDocument     $doc    XML document containing the <person> element
         * @return  boolean                 Success or failure
         */
        private function import_person_element(DOMDocument $doc)
        {

            $rc          = false;

            $xpath       = new DOMXPath($doc);
            $lmb_data    = new stdClass();

            $lmb_data->source_id     = $xpath->evaluate("string(/PERSON/SOURCEDID/ID)");
            $lmb_data->userid_logon  = $xpath->evaluate("string(/PERSON/USERID[@USERIDTYPE = \"Logon ID\"])");
            $lmb_data->userid_sctid  = $xpath->evaluate("string(/PERSON/USERID[@USERIDTYPE = \"SCTID\"])");
            $lmb_data->userid_email  = $xpath->evaluate("string(/PERSON/USERID[@USERIDTYPE = \"Email ID\"])");
            $lmb_data->full_name     = $xpath->evaluate("string(/PERSON/NAME/FN)");
            $lmb_data->family_name   = $xpath->evaluate("string(/PERSON/NAME/N/FAMILY)");
            $lmb_data->given_name    = $xpath->evaluate("string(/PERSON/NAME/N/GIVEN)");
            $lmb_data->email         = $xpath->evaluate("string(/PERSON/EMAIL)");
            $lmb_data->telephone     = $xpath->evaluate("string(/PERSON/TEL[@TELTYPE = \"1\" or @TELTYPE = \"3\"][1])");
            $lmb_data->street        = $xpath->evaluate("string(/PERSON/ADR/STREET[1])");
            //                       . $xpath->evaluate("string(/PERSON/ADR/STREET[2])")
            //                       . $xpath->evaluate("string(/PERSON/ADR/STREET[3])");
            $lmb_data->locality      = $xpath->evaluate("string(/PERSON/ADR/LOCALITY)");
            $lmb_data->country       = $xpath->evaluate("string(/PERSON/ADR/COUNTRY)");
            $lmb_data->major         = $xpath->evaluate("string(/PERSON/EXTENSION/LUMINISPERSON/ACADEMICMAJOR[1])");
            $lmb_data->recstatus     = $xpath->evaluate("string(/PERSON/@RECSTATUS)");
            $lmb_data->insert_date   =
            $lmb_data->update_date   = date(self::DATEFMT_SQL_VALUE);



            // Validate the source_id
            if (empty($lmb_data->source_id)) {
                $this->log_process_message(self::SHEBANGENT_PERSON, "", "", get_string('ERR_PERSON_SOURCE_ID', self::PLUGIN_NAME));
                return false;
            }

            // Certain columns need to be null proper to avoid
            // the Oracle dirty hack when binding parm values
            if (empty($lmb_data->userid_logon)) $lmb_data->userid_logon = null;
            if (empty($lmb_data->userid_sctid)) $lmb_data->userid_sctid = null;
            if (empty($lmb_data->userid_email)) $lmb_data->userid_email = null;
            if (empty($lmb_data->email))        $lmb_data->email        = null;

            // If recstatus not present, default is add (1)
            if (!isset($lmb_data->recstatus) || empty($lmb_data->recstatus)) $lmb_data->recstatus = self::RECSTATUS_ADD;

            // Fetch the existing staging record, or insert a new one
            try
            {
                if (false === ($old_rec = $this->moodleDB->get_record(self::SHEBANGENT_PERSON, array('source_id' => $lmb_data->source_id)))) {
                    $op = 'insert';
                    $lmb_data->id = $this->moodleDB->insert_record(self::SHEBANGENT_PERSON, $lmb_data, true);
                } else {
                    // Need to preserve these values: id, obviously, and the userid_moodle value
                    // which holds the user record association (mdl_user id)
                    $lmb_data->id = $old_rec->id; $lmb_data->userid_moodle = $old_rec->userid_moodle;
                    // Don't step on this value either
                    unset($lmb_data->insert_date);
                    $op = 'update';
                    $this->moodleDB->update_record(self::SHEBANGENT_PERSON, $lmb_data);
                }
                $rc = true;
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                $rc = false;
            }

            $this->log_process_message(self::SHEBANGENT_PERSON, $lmb_data->source_id, $op, $rc);

            // Bailout here if no joy
            if (false === $rc) {
                return false;
            }


            // If the recstatus on the message indicates to delete this user.
            if ($lmb_data->recstatus == self::RECSTATUS_DELETE) {
                return $this->delete_user($lmb_data);
            }

            // The lmb message was for other than delete so we
            // need to to either update or insert this person

            // Determine what the username should be according
            // to the configs
            $username = '';
            switch($this->config->person_username)
            {
                case self::OPT_PERSON_USERNAME_EMAIL:
                case self::OPT_PERSON_USERNAME_USERID_EMAIL:
                case self::OPT_PERSON_USERNAME_USERID_LOGON:
                case self::OPT_PERSON_USERNAME_USERID_SCTID:
                    $field = $this->config->person_username;
                    $username = trim($lmb_data->$field);
                    break;
                default:
                    $username = trim($lmb_data->userid_logon);
            }
            if (empty($username)) {
                if ($this->config->person_username_failsafe) {
                    $username = $lmb_data->source_id;
                } else {
                    $this->log_process_message(self::MOODLENT_USER, $lmb_data->source_id, '', get_string('ERR_MISSINGVAL_USERNAME', self::PLUGIN_NAME));
                    return false;
                }
            }
            // If Shibboleth username domain is specified, append
            // it to the username so we keep universal uniqueness
            if ($this->config->person_auth_method == self::OPT_AUTH_SHIBBOLETH || $this->config->person_auth_method == self::OPT_AUTH_SHIBBUNCIF) {
                $userdomain = trim($this->config->person_shib_domain);
                if ($userdomain && substr($userdomain, 0, 1) != '@') {
                    $userdomain = '@' . $userdomain;
                }
                $username .= $userdomain;
            }

            $username = strtolower($username);

            // If there is already a user association to our (existing) person
            // staging rec, then use it if it is good. However, if there is not
            // an association (new person staging rec, missing or bad user id
            // value in existing rec), then try to make an association using the
            // mnethostid (site config) and username derived from the message.

            $user_rec = false;
            // Try to fetch the user rec
            if (isset($lmb_data->userid_moodle) && !empty($lmb_data->userid_moodle)) {
                if (false === ($user_rec = $this->moodleDB->get_record(self::MOODLENT_USER, array('id' => $lmb_data->userid_moodle, 'deleted' => 0)))) {
                    // The currently associated user id is no longer valid and
                    // should be discarded
                    $this->log_process_message(self::MOODLENT_USER, $lmb_data->userid_moodle, 'select', get_string('ERR_RECORDNOTFOUND', self::PLUGIN_NAME));
                    $lmb_data->userid_moodle = null;
                }
            }

            // If no row found (or no attempt to find one) try to make
            // an association with an existing user based on mnethostid
            // and username
            if (false === $user_rec) {
                $user_rec = $this->moodleDB->get_record(self::MOODLENT_USER, array('mnethostid' => $this->moodleConfigs->mnet_localhost_id, 'username' => $username));
            }

            $insert_result =
            $update_result = true;
            try
            {
                // If still no user rec then need to add one if configs allow
                if (false === $user_rec) {
                    if ($this->config->person_create) {
                        $insert_result = (boolean)($user_rec = $this->insert_user($lmb_data, $username, $xpath));
                    } else {
                        $this->log_process_message(self::MOODLENT_USER, $lmb_data->source_id, '', get_string('INF_USERCREATE_NOACTION', self::PLUGIN_NAME));
                        return true;
                    }
                } else {
                    // Found a user rec so update it with message info. Username may
                    // have changed so pass that with the $lmb_data
                    $update_result = $this->update_user($user_rec, $lmb_data, $username, $xpath);
                }

                // If the user association hasn't been recorded yet then do it
                if ($user_rec && (!isset($lmb_data->userid_moodle) || empty($lmb_data->userid_moodle))) {
                    $lmb_data->userid_moodle = $user_rec->id;
                    $this->moodleDB->update_record(self::SHEBANGENT_PERSON, $lmb_data);
                }
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                return false;
            }

            return $insert_result && $update_result;

        } // import_person_element



        /**
         * Insert a new Moodle user record with the LMB information
         *
         * @access      private
         * @param       stdClass        $lmb_data   The LMB staging record (message received)
         * @param       string          $username   The username derived from the LMB message
         * @param       DOMXPath        $xpath      The DOM Xpath with the XML message loaded
         * @return      mixed                       New user record, or false
         */
        private function insert_user(stdClass $lmb_data, $username, DOMXPath $xpath)
        {

            // Create a new Moodle user rec
            $user_rec               = new stdClass();
            $user_rec->username     = $username;
            $user_rec->confirmed    = 1;
            $user_rec->lang         = $this->moodleConfigs->lang;
            $user_rec->lastip       = '0.0.0.0';
            $user_rec->mnethostid   = $this->moodleConfigs->mnet_localhost_id;
            $user_rec->timezone     = $this->moodleConfigs->timezone;
            $user_rec->country      = $this->config->person_country;

            // Get the auth plugin to be used for new users
            if (is_enabled_auth($this->config->person_auth_method)) {
                $auth_plugin = get_auth_plugin($this->config->person_auth_method);
            } else {
                $auth_plugin = get_auth_plugin(self::OPT_AUTH_NOLOGIN);
            }
            $user_rec->auth = $auth_plugin->authtype;

            // If the auth plugin prevents local passwords, then indicate with
            // a plain text value in the password field, otherwise fix up a hash
            // of the specified default password in the message. One exception:
            // the 'nologin' auth plugin indicates local passwords can be used
            // because it does not want to step on any existing password hases,
            // but this is a new user so we'll use the plain text for 'nologin'
            if ($auth_plugin->prevent_local_passwords() || $auth_plugin->authtype == self::OPT_AUTH_NOLOGIN) {

                $user_rec->password = 'not cached';

            } else {

                switch($this->config->person_password)
                {
                    case self::OPT_PERSON_PASSWORD_USERID_LOGON:
                        $user_rec->password = $xpath->evaluate("string(/PERSON/USERID[@USERIDTYPE = \"Logon ID\"]/@PASSWORD)");
                        break;
                    case self::OPT_PERSON_PASSWORD_USERID_SCTID:
                        $user_rec->password = $xpath->evaluate("string(/PERSON/USERID[@USERIDTYPE = \"SCTID\"]/@PASSWORD)");
                        break;
                    default:
                        $user_rec->password = random_string();
                }
                $user_rec->password = hash_internal_user_password($user_rec->password);

            }

            if ($this->config->person_idnumber_sctid) {
                $user_rec->idnumber = $lmb_data->userid_sctid;
            } else {
                $user_rec->idnumber = $lmb_data->source_id;
            }

            $user_rec->firstname = $lmb_data->given_name;
            $user_rec->lastname  = $lmb_data->family_name;
            $user_rec->email     = $lmb_data->email;

            if ($this->config->person_telephone) {
                $user_rec->phone1 = $lmb_data->telephone;
            }

            if ($this->config->person_address) {
                $user_rec->address = substr($lmb_data->street, 0, self::MAX_LEN_PERSON_STREET);
                switch($this->config->person_locality)
                {
                    case self::OPT_PERSON_LOCALITY_DEF:
                        $user_rec->city = $this->config->person_locality_default;
                        break;
                    case self::OPT_PERSON_LOCALITY_MSG:
                        $user_rec->city = $lmb_data->locality;
                        break;
                    default:
                        if ($lmb_data->locality) {
                            $user_rec->city = $lmb_data->locality;
                        } else {
                            $user_rec->city = $this->config->person_locality_default;
                        }
                }
            }

            $user_rec->description  = $lmb_data->full_name;
            $user_rec->timecreated  =
            $user_rec->timemodified = strtotime($lmb_data->update_date);

            // Insert a new Moodle user record
            try
            {
                $user_rec->id = $this->moodleDB->insert_record(self::MOODLENT_USER, $user_rec, true);
                context_user::instance($user_rec->id);
                events_trigger('user_created', $user_rec);
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                $user_rec = false;
            }

            $this->log_process_message(self::MOODLENT_USER, $lmb_data->source_id, 'insert', (boolean)$user_rec);
            return $user_rec;

        } // insert_user



        /**
         * Upate a user record using information in the LMB message
         *
         * @acess       private
         * @param       stdClass        $user_rec               The moodle user record to update
         * @param       stdClass        $lmb_data               The LMB staging record (message received)
         * @param       string          $username               The username derived from LMB message data
         * @param       DOMXPath        $xpath                  The DOM Xpath with the XML message loaded
         * @return      boolean                                 Success or failure
         */
        private function update_user(stdClass $user_rec, stdClass $lmb_data, $username, DOMXPath $xpath)
        {

            // There is an existing Moodle user record ($user_rec)
            // either already associated with the person record or
            // one that matches up on the mnethostid/username values

            $user_rec->username = $username;

            if ($this->config->person_firstname_changes) {
                $user_rec->firstname = $lmb_data->given_name;
            }
            if ($this->config->person_lastname_changes) {
                $user_rec->lastname = $lmb_data->family_name;
            }

            // Only update the Moodle user email column value if a
            // value was provided in the LMB message, otherwise leave
            // it unchanged. It should never be null (or an empty str)
            if (!empty($lmb_data->email)) {
                $user_rec->email = $lmb_data->email;
            }

            if ($this->config->person_telephone && $this->config->person_telephone_changes) {
                $user_rec->phone1 = $lmb_data->telephone;
            }

            if ($this->config->person_address) {
                $user_rec->address = substr($lmb_data->street, 0, self::MAX_LEN_PERSON_STREET);
                switch($this->config->person_locality)
                {
                    case self::OPT_PERSON_LOCALITY_DEF:
                        $user_rec->city = $this->config->person_locality_default;
                        break;
                    case self::OPT_PERSON_LOCALITY_MSG:
                        $user_rec->city = $lmb_data->locality;
                        break;
                    default:
                        if ($lmb_data->locality) {
                            $user_rec->city = $lmb_data->locality;
                        } else {
                            $user_rec->city = $this->config->person_locality_default;
                        }
                }
            }

            // Update the existing Moodle user first, then check if any password change
            // is needed, and do that secondly through the associated auth plugin
            $rc = true;
            try
            {
                $this->moodleDB->update_record(self::MOODLENT_USER, $user_rec);
                // Do we need to keep the password updated
                if ($this->config->person_password_changes) {
                    // Fetch the auth plugin used, and if it allows
                    // local passwords, update the field
                    $auth_plugin = get_auth_plugin($user_rec->auth);
                    if (!$auth_plugin->prevent_local_passwords() && $auth_plugin->authtype != self::OPT_AUTH_NOLOGIN) {
                        $password = '';
                        switch($this->config->person_password)
                        {
                            case self::OPT_PERSON_PASSWORD_USERID_LOGON:
                                $password = $xpath->evaluate("string(/PERSON/USERID[@USERIDTYPE = \"Logon ID\"]/@PASSWORD)");
                                break;
                            case self::OPT_PERSON_PASSWORD_USERID_SCTID:
                                $password = $xpath->evaluate("string(/PERSON/USERID[@USERIDTYPE = \"SCTID\"]/@PASSWORD)");
                                break;
                        }
                        // Overwrite the current password only if there was a
                        // new value present in the message.
                        if ($password) {
                            $auth_plugin->user_update_password($user_rec, $password);
                        }
                    }
                }
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                $rc = false;
            }

            $this->log_process_message(self::MOODLENT_USER, $lmb_data->source_id, 'update', $rc);
            return $rc;

        } // update_user



        /**
         * Process the person message with a recstatus=3
         *
         * @access  private
         * @param   stdClass        $lmb_data       LMB staging record (message received)
         * @return  boolean
         */
        private function delete_user(stdClass $lmb_data)
        {

            // If there is not a user association then nothing to do
            if (!isset($lmb_data->userid_moodle) || empty($lmb_data->userid_moodle)) {
                $this->log_process_message(self::MOODLENT_USER, $lmb_data->source_id, 'delete', get_string('INF_USERDELETE_NOACTION', self::PLUGIN_NAME));
                return true;
            }

            // Fetch the associated user
            if (false === ($user_rec = $this->moodleDB->get_record(self::MOODLENT_USER, array('id' => $lmb_data->userid_moodle)))) {
                // Asked to delete a user who isn't there
                $this->log_process_message(self::MOODLENT_USER, $lmb_data->source_id, 'delete', get_string('ERR_RECORDNOTFOUND', self::PLUGIN_NAME));
                return true;
            }


            $op = $this->config->person_delete;
            switch($op)
            {
                case self::OPT_PERSON_DELETE_DELETE:
                    // Let Moodle delete the user--this
                    // will remove the user from the DB
                    try
                    {
                        delete_user($user_rec);
                        $lmb_data->userid_moodle = null;
                        $this->moodleDB->update_record(self::SHEBANGENT_PERSON, $lmb_data);
                        $rc = true;
                    }
                    catch (Exception $exc)
                    {
                        $this->log_process_exception($exc);
                        $rc = false;
                    }
                    break;
                case self::OPT_PERSON_DELETE_UNENROL:
                    // Call the parent class' user_delete()
                    // method, which will only find all of
                    // the user's enrolments done with this
                    // plugin, and remove them using a call
                    // to unenrol_user().
                    try
                    {
                        $this->enrol_plugin->user_delete($user_rec);
                        $rc = true;
                    }
                    catch (Exception $exc)
                    {
                        $this->log_process_exception($exc);
                        $rc = false;
                    }
                    break;
                default:
                    // Ignore the message
                    $rc = get_string('INF_USERDELETE_NOACTION', self::PLUGIN_NAME);
            }

            $this->log_process_message(self::MOODLENT_USER, $lmb_data->source_id, $op, $rc);
            return (boolean)$rc;

        } // delete_user



        /**
         * Import a membership element into Moodle
         *
         * @access  private
         * @param   DOMDocument     $doc    XML document containing the <membership> and its constituent <member> elements
         * @return  boolean                 Success or failure
         */
        private function import_membership_element(DOMDocument $doc)
        {

            /* Need to determine the type of membership, whether it is a
             * course enrolment for a student or teacher, or cross-list
             * section association.
             */

            $rc         = false;
            $xpath      = new DOMXPath($doc);

            $parent_id  = $xpath->evaluate("string(/MEMBERSHIP/SOURCEDID/ID)");
            $child_id   = $xpath->evaluate("string(/MEMBERSHIP/MEMBER/SOURCEDID/ID)");
            $id_type    = $xpath->evaluate("string(/MEMBERSHIP/MEMBER/IDTYPE)");

            unset($xpath);

            if ($id_type === self::IDTYPE_PERSON) {
                // Student or Instructor enrollment
                $rc = $this->import_membership_person($doc);
            } elseif ($id_type === self::IDTYPE_GROUP) {
                // Cross-listed course section
                $rc = $this->import_membership_group($doc);
            } else {
                // Bad data
                $this->log_process_message(self::SHEBANGENT_MEMBER, "{$parent_id}:{$child_id}:{$id_type}", '', get_string('ERR_MEMBERSHIP_IDTYPE', self::PLUGIN_NAME));
            }

            return $rc;

        } // import_membership_element



        /**
         * Import a course enrollment (Moodle role assignment)
         *
         * @access  private
         * @param   DOMDocument     $doc    XML Document containing the <membership> and its constituent <member> elements
         * @return  boolean
         */
        private function import_membership_person(DOMDocument $doc)
        {
            /*
             * The membership element provides the person's sourcedid/id for
             * identification, however, we are using the SCTID (Banner) user
             * id type in the mdl_user idnumber column -- the staging record
             * is our cross reference.
             */

            $rc          = false;

            $xpath       = new DOMXPath($doc);
            $lmb_data    = new stdClass();

            $lmb_data->section_source_id = $xpath->evaluate("string(/MEMBERSHIP/SOURCEDID/ID)");
            $lmb_data->person_source_id  = $xpath->evaluate("string(/MEMBERSHIP/MEMBER/SOURCEDID/ID)");
            $lmb_data->roletype          = $xpath->evaluate("string(/MEMBERSHIP/MEMBER/ROLE/@ROLETYPE)");
            $lmb_data->recstatus         = $xpath->evaluate("string(/MEMBERSHIP/MEMBER/ROLE/@RECSTATUS)");
            $lmb_data->status            = $xpath->evaluate("string(/MEMBERSHIP/MEMBER/ROLE/STATUS)");
            $lmb_data->insert_date       =
            $lmb_data->update_date       = date(self::DATEFMT_SQL_VALUE);



            // If recstatus missing, default is to add (1)
            if (!isset($lmb_data->recstatus) || empty($lmb_data->recstatus)) $lmb_data->recstatus = self::RECSTATUS_ADD;

            try
            {
                if (false === ($old_rec = $this->moodleDB->get_record(self::SHEBANGENT_MEMBER, array('section_source_id' => $lmb_data->section_source_id, 'person_source_id' => $lmb_data->person_source_id)))) {
                    $op = 'insert';
                    $rc = $this->moodleDB->insert_record(self::SHEBANGENT_MEMBER, $lmb_data, false);
                } else {
                    $lmb_data->id = $old_rec->id; unset($lmb_data->insert_date);
                    $op = 'update';
                    $rc = $this->moodleDB->update_record(self::SHEBANGENT_MEMBER, $lmb_data);
                }
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                $rc = false;
            }

            $this->log_process_message(self::SHEBANGENT_MEMBER, "{$lmb_data->section_source_id}:{$lmb_data->person_source_id}", $op, $rc);


            // Bailout here if no joy
            if (false === $rc) {
                return false;
            }


            // Will need to know if the specified section is part of a course cross-list
            $crosslist_rec = !empty($this->config->crosslist_enabled)
                           ? $this->moodleDB->get_record_select(self::SHEBANGENT_CROSSLIST,
                                                                "status = :status and recstatus != :recstatus and child_source_id = :child_source_id",
                                                                array('status' => self::STATUS_ACTIVE, 'recstatus' => self::RECSTATUS_DELETE, 'child_source_id' => $lmb_data->section_source_id))
                           : null;

            // If there's no cross-list record (either because it did not exist or our
            // configs indicated we are not handling cross-list messages) then use the
            // course specified in the message. Likewise, if we do handle cross-list
            // messages, but implement it with metacourses, use the course specified
            // in the message and let Moodle sync the enrollments. Only in the third
            // case where we handle cross-listing by merging enrollments into a non-
            // meta course, do we find the parent course and make enrollments in that.
            $target_source_id = (empty($crosslist_rec) || $this->config->crosslist_method == self::OPT_CROSSLIST_METHOD_META)
                              ? $lmb_data->section_source_id        // No cross-listing, or cross-listing using meta courses
                              : $crosslist_rec->parent_source_id;   // Cross-listing with merge method, use parent section

            $suspend_enrollment = $lmb_data->status === self::STATUS_INACTIVE;
            $delete_enrollment  = ($lmb_data->recstatus === self::RECSTATUS_DELETE)
                               || ($this->config->enrollments_delete_inactive && $suspend_enrollment);

            return $this->process_user_enrollment($lmb_data->person_source_id, $target_source_id, $lmb_data->roletype,
                                              !empty($crosslist_rec) ? $crosslist_rec->group_id : 0,
                                              $delete_enrollment, $suspend_enrollment);

        } // import_membership_person



        /**
         * Create a cross-listed section association between parent and child
         *
         * @access  private
         * @param   DOMDocument     $doc    XML Document containing the <membership> and its constituent <member> elements
         * @return  boolean
         */
        private function import_membership_group(DOMDocument $doc)
        {

            $rc          = true;
            $meta_plugin = null;


            // Query the document for data we need
            $xpath                       = new DOMXPath($doc);
            $lmb_data                    = new stdClass();
            $lmb_data->parent_source_id  = $xpath->evaluate("string(/MEMBERSHIP/SOURCEDID/ID)");
            $lmb_data->child_source_id   = $xpath->evaluate("string(/MEMBERSHIP/MEMBER/SOURCEDID/ID)");
            $lmb_data->recstatus         = $xpath->evaluate("string(/MEMBERSHIP/MEMBER/ROLE/@RECSTATUS)");
            $lmb_data->status            = $xpath->evaluate("string(/MEMBERSHIP/MEMBER/ROLE/STATUS)");
            $lmb_data->insert_date       =
            $lmb_data->update_date       = date(self::DATEFMT_SQL_VALUE);


            $op = 'insert';
            // Now find out if we are allowed to do this
            if (empty($this->config->crosslist_enabled)) {
                $this->log_process_message(self::SHEBANGENT_CROSSLIST, "{$lmb_data->parent_source_id}:{$lmb_data->child_source_id}", $op, get_string('ERR_CROSSLISTNOTENABLED', self::PLUGIN_NAME));
                return false;
            }

            // If supposed to do meta course
            if ($this->config->crosslist_method == self::OPT_CROSSLIST_METHOD_META) {
                // Is that enrol plugin enabled?
                if (enrol_is_enabled('meta')) {
                    // We will need the meta plugin
                    // to do some work for us
                    $meta_plugin = enrol_get_plugin('meta');
                } else {
                    // No joy
                    $this->log_process_message(self::SHEBANGENT_CROSSLIST, "{$lmb_data->parent_source_id}:{$lmb_data->child_source_id}", $op, get_string('ERR_METANOTENABLED', self::PLUGIN_NAME));
                    return false;
                }
            }

            // Insert or update the staging rec
            try
            {
                if (false === ($old_rec = $this->moodleDB->get_record(self::SHEBANGENT_CROSSLIST, array('child_source_id' => $lmb_data->child_source_id)))) {
                    $op = 'insert';
                    $lmb_data->id = $this->moodleDB->insert_record(self::SHEBANGENT_CROSSLIST, $lmb_data);
                    $rc = (boolean)$lmb_data->id;
                } else {
                    $lmb_data->id = $old_rec->id; $lmb_data->group_id = $old_rec->group_id; unset($lmb_data->insert_date);
                    $op = 'update';
                    $rc = $this->moodleDB->update_record(self::SHEBANGENT_CROSSLIST, $lmb_data);
                }
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                $rc = false;
            }

            $this->log_process_message(self::SHEBANGENT_CROSSLIST, "{$lmb_data->parent_source_id}:{$lmb_data->child_source_id}", $op, $rc);


            // Bailout here if no joy
            if (false === $rc) {
                return false;
            }


            // Will need the child course id and context for most everything
            if (false === ($child_course = $this->moodleDB->get_record(self::MOODLENT_COURSE, array('idnumber' => $lmb_data->child_source_id)))) {
                $this->log_process_message(self::MOODLENT_COURSE, $lmb_data->child_source_id, 'select', get_string('ERR_COURSE_IDNUMBER', self::PLUGIN_NAME));
                return false;
            }


            // The parent course may or may not exist yet. If this is the first
            // membership msg for the parent course, we will have to create it,
            // but we'll make that test later.
            $parent_course = $this->moodleDB->get_record(self::MOODLENT_COURSE, array('idnumber' => $lmb_data->parent_source_id));


            // If recstatus/status indicate an end to the parent child relationship
            if ($lmb_data->recstatus === self::RECSTATUS_DELETE || $lmb_data->status === self::STATUS_INACTIVE) {
                // Remove the parent-child association.
                if ($this->config->crosslist_method == self::OPT_CROSSLIST_METHOD_META) {
                    if ($parent_course && false !== ($meta_enroll_instance = $this->moodleDB->get_record(self::MOODLENT_ENROL, array('courseid' => $parent_course->id, 'enrol' => $meta_plugin->get_name(), 'customint1' => $child_course->id)))) {
                        if ($lmb_data->recstatus === self::RECSTATUS_DELETE) {
                            // Remove the child from the metacourse and enrollments will get sync'd
                            $meta_plugin->delete_instance($meta_enroll_instance);
                            $rc = true;
                        } else {
                            // Disable the enrol instance for the one child course
                            $meta_enroll_instance->status = ENROL_INSTANCE_DISABLED;
                            $this->moodleDB->update_record(self::MOODLENT_ENROL, $meta_enroll_instance);
                        }
                    }
                } else {
                    // With the staging record already updated (recstatus and/or status), no
                    // new enrollments will be made in the parent merge course, but need to
                    // remove existing enrollments in parent and place them in child course
                    $all_enrolls_succeeded = true;
                    $members_array = $this->moodleDB->get_recordset(self::SHEBANGENT_MEMBER, array('section_source_id' => $lmb_data->child_source_id, 'recstatus' => self::RECSTATUS_ADD, 'status' => self::STATUS_ACTIVE));
                    foreach ($members_array as $member) {

                        if ($lmb_data->recstatus === self::RECSTATUS_DELETE) {

                            // Put member into the course which is no longer a child course and is
                            // the course for which the person membership was originally designated
                            if (false !== ($rc = $this->process_user_enrollment($member->person_source_id, $member->section_source_id, $member->roletype))) {
                                // and succeeding that, take member out of the old parent *merge* course
                                if (false === ($rc = $this->process_user_enrollment($member->person_source_id, $lmb_data->parent_source_id, $member->roletype, 0, true))) {
                                    $all_enrolls_succeeded = false;
                                    $this->log_process_message(self::MOODLENT_USER_ENROL, "{$member->person_source_id}:{$lmb_data->parent_source_id}", 'delete', get_string('ERR_UNENROLL_FAIL', self::PLUGIN_NAME));
                                }
                            } else {
                                $all_enrolls_succeeded = false;
                                $this->log_process_message(self::MOODLENT_USER_ENROL, "{$member->person_source_id}:{$member->section_source_id}", 'insert', get_string('ERR_ENROLL_FAIL', self::PLUGIN_NAME));
                            }

                        } else {

                            // Suspend in the *merge* course
                            if (false === ($rc = $this->process_user_enrollment($member->person_source_id, $lmb_data->parent_source_id, $member->roletype, 0, false, true))) {
                                $all_enrolls_succeeded = false;
                                $this->log_process_message(self::MOODLENT_USER_ENROL, "{$member->person_source_id}:{$lmb_data->parent_source_id}", 'update', get_string('ERR_SUSPEND_FAIL', self::PLUGIN_NAME));
                            }

                        }

                    } // foreach

                    $rc = $all_enrolls_succeeded;
                }

                // Remove group for this child course only when membership deleted
                if ($lmb_data->recstatus === self::RECSTATUS_DELETE) {
                    if (!empty($lmb_data->group_id) && !groups_delete_group($lmb_data->group_id)) {
                        $this->log_process_message(self::MOODLENT_GROUP, $lmb_data->group_id, 'delete', false);
                        $rc = false;
                    } else {
                        $lmb_data->group_id = 0;
                        $this->moodleDB->update_record(self::SHEBANGENT_CROSSLIST, $lmb_data);
                    }
                }

                return $rc;

            }

            // The message recstatus/status indicate an add. This may be the first
            // message for a given parent course, so we need to check first that it
            // has been created. The LMB message only provides a source id for the
            // parent course, so let's set it up based on the given child course
            if (!$parent_course) {

                $parent_course_data                 = $this->create_empty_course_object();

                $parent_course_data->idnumber       = $lmb_data->parent_source_id;
                $parent_course_data->category       = $child_course->category;
                $parent_course_data->fullname       = $this->config->crosslist_fullname_prefix  . $child_course->fullname;
                $parent_course_data->shortname      = $this->config->crosslist_shortname_prefix . $child_course->shortname;
                $parent_course_data->timecreated    =
                $parent_course_data->timemodified   = strtotime($lmb_data->update_date);

                try
                {
                    // Call this plugin's create_course method which mimics
                    // the Moodle course/lib.php routine, but does not do
                    // any more in the database than is needed. Set a bool
                    // to indicate a call fix_course_sortorder is needed.
                    $parent_course = $this->create_course($parent_course_data);
                    $this->courseInserted = true;
                }
                catch (Exception $exc)
                {
                    $this->log_process_exception($exc);
                    $this->log_process_message(self::MOODLENT_COURSE, $lmb_data->parent_source_id, 'insert', get_string('ERR_CREATE_PARENT_COURSE', self::PLUGIN_NAME));
                    return false;
                }

            } // if (!$parent_course)


            // At this point we have a parent course and a child course. The parent course
            // now needs an enroll plugin instance if there is not one already. Which plugin
            // is used depends on the cross-list method selected.
            if ($this->config->crosslist_method == self::OPT_CROSSLIST_METHOD_META) {
                $enroll_instance = $this->moodleDB->get_record(self::MOODLENT_ENROL, array('courseid' => $parent_course->id, 'enrol' => $meta_plugin->get_name(), 'customint1' => $child_course->id));
                if (false == $enroll_instance) {
                    $enroll_instance_id = $meta_plugin->add_instance($parent_course, array('customint1' => $child_course->id));
                } elseif ($enroll_instance->status == ENROL_INSTANCE_DISABLED) {
                    $enroll_instance->status = ENROL_INSTANCE_ENABLED;
                    $enroll_instance->timemodified = time();
                    $this->moodleDB->update_record(self::MOODLENT_ENROL, $enroll_instance);
                }
                $meta_plugin->course_updated(false, $parent_course, null);
            } elseif ($this->config->crosslist_method == self::OPT_CROSSLIST_METHOD_MERGE && false === ($enroll_instance = $this->moodleDB->get_record(self::MOODLENT_ENROL, array('courseid' => $parent_course->id, 'enrol' => $this->enrol_plugin->get_name())))) {
                $enroll_instance_id = $this->enrol_plugin->add_instance($parent_course);
            }

            // Hide the child course if the config says to do so
            if ($rc && $this->config->crosslist_hide_on_parent) {
                $this->moodleDB->set_field(self::MOODLENT_COURSE, 'visible', 0, array('id' => $child_course->id));
            }

            // If a group is needed, create one (if it does not exist) in the parent,
            // but named for the child section.
            if (isset($this->config->crosslist_groups) && !empty($this->config->crosslist_groups) && (!isset($lmb_data->group_id) || empty($lmb_data->group_id))) {
                $group_rec = new stdClass();
                $group_rec->courseid    = $parent_course->id;
                $group_rec->name        =
                $group_rec->description = $child_course->shortname;
                if (false === ($group_rec->id = groups_create_group($group_rec))) {
                    $this->log_process_message(self::MOODLENT_GROUP, $parent_course->id, 'insert', get_string('ERR_CREATE_CROSSLIST_GROUP', self::PLUGIN_NAME));
                    return false;
                }
                // Update the cross-list staging rec with the new group id
                $lmb_data->group_id = $group_rec->id;
                try
                {
                    $this->moodleDB->update_record(self::SHEBANGENT_CROSSLIST, $lmb_data);
                }
                catch (Exception $exc)
                {
                    $this->log_process_exception($exc);
                    $this->log_process_message(self::SHEBANGENT_CROSSLIST, $lmb_data->id, 'update', get_string('ERR_UPDATE_CROSSLIST_GROUP', self::PLUGIN_NAME));
                    return false;
                }
            } // isset($this->config->crosslist_groups) ...

            return true;

        } // import_membership_group



        /**
         * Do the work of making the enrollment and role assignment
         *
         * @access  private
         * @param   string      $person_source_id       Luminis Id (sourcedid/id) for the person
         * @param   string      $section_source_id      Luminis Id (sourcedid/id) for the course section (CRN)
         * @param   string      $ims_role_type          IMS roletype value from the message
         * @param   int         $group_id               Optional group id to which to assign enrollee
         * @param   boolean     $unenroll               Optional unenroll indicator - default is false, which will enroll a user
         * @param   boolean     $suspend                Optional suspend indicator - default is false, which will make enrollment active
         * @return  boolean                             Success or failure
         */
        private function process_user_enrollment($person_source_id, $section_source_id, $ims_role_type, $group_id = 0, $unenroll = false, $suspend = false)
        {

            $rc = false;



            // Get target course, and its context
            if (false === ($course_rec = $this->moodleDB->get_record(self::MOODLENT_COURSE, array('idnumber' => $section_source_id)))) {
                $this->log_process_message(self::MOODLENT_COURSE, $section_source_id, 'select', get_string('ERR_COURSE_IDNUMBER', self::PLUGIN_NAME));
                return false;
            }

            // Get this plugin's enrol instance for the course
            if (false === ($enrol_instance = $this->moodleDB->get_record(self::MOODLENT_ENROL, array('courseid' => $course_rec->id, 'enrol' => $this->enrol_plugin->get_name(), 'status' => ENROL_INSTANCE_ENABLED)))) {
                $this->log_process_message(self::MOODLENT_ENROL, $course_rec->id, 'select', get_string('ERR_RECORDNOTFOUND', self::PLUGIN_NAME));
                return false;
            }

            // Find the appropriate role mapping
            if (false === ($role_id = $this->get_role_mapping($ims_role_type))) {
                $this->log_process_message(self::MOODLENT_ROLE_ASSIGNMENT, $ims_role_type, 'select', get_string('ERR_ENROLL_ROLETYPE_NOMAP', self::PLUGIN_NAME));
                return false;
            }

            // Fetch the user by joining with the staging record
            // which has both the Luminis source id and Moodle
            // user id values
            $query = "SELECT u.* "
                   . "  FROM {" . self::MOODLENT_USER . "} u "
                   . " INNER JOIN {" . self::SHEBANGENT_PERSON . "} p "
                   . "    ON p.userid_moodle = u.id "
                   . " WHERE p.source_id = :source_id AND u.deleted = 0";
            $parms = array('source_id' => $person_source_id);

            if (false === ($user_rec = $this->moodleDB->get_record_sql($query, $parms))) {
                $this->log_process_message(self::MOODLENT_USER, $person_source_id, 'select', get_string('ERR_PERSON_SOURCE_ID', self::PLUGIN_NAME));
                return false;
            }

            try
            {
                if ($unenroll) {
                    // If the role recstatus attribute or member status
                    // sub-element indicated to unenroll this user
                    $op = 'unenrol';
                    $this->enrol_plugin->unenrol_user($enrol_instance, $user_rec->id);
                } else {
                    // Otherwise, enroll or update current enrollment
                    $op = 'enrol';
                    $this->enrol_plugin->enrol_user($enrol_instance, $user_rec->id, $role_id, 0, 0, $suspend ? ENROL_USER_SUSPENDED : ENROL_USER_ACTIVE);
                    if ($group_id && !empty($this->config->crosslist_groups)) {
                        groups_add_member($group_id, $user_rec->id);
                    }
                }
                $rc = true;
            }
            catch (Exception $exc)
            {
                $this->log_process_exception($exc);
                $rc = false;
            }

            $this->log_process_message(self::MOODLENT_USER_ENROL, "{$enrol_instance->courseid}:{$role_id}:{$user_rec->id}", $op, $rc);
            return $rc;

        } // process_user_enrollment



        /**
         * Create and return a stdClass object with properties needed
         * to make an empty Moodle course record
         *
         * @access  private
         * @return  stdClass                An object with properties needed for Moodle course
         */
        private function create_empty_course_object()
        {

            /* Don't want to fetch these configs everytime this
             * routine is called because if we are processing a
             * file then expect to create a few hundred courses
             */
            static $course_configs          = null;
            if ($course_configs == null) {
                $course_configs = get_config('moodlecourse');
            }

            $course_rec                     = new stdClass();
            $course_rec->category           = 0;
            $course_rec->sortorder          = 1;
            $course_rec->summaryformat      = FORMAT_HTML;
            $course_rec->format             = $course_configs->format;
            $course_rec->showgrades         = $course_configs->showgrades;
            $course_rec->newsitems          = $course_configs->newsitems;
            $course_rec->numsections        = $course_configs->numsections;
            $course_rec->maxbytes           = $course_configs->maxbytes;
            $course_rec->showreports        = $course_configs->showreports;
            $course_rec->visible            =
            $course_rec->visibleold         = $course_configs->visible;
            $course_rec->hiddensections     = $course_configs->hiddensections;
            $course_rec->groupmode          = $course_configs->groupmode;
            $course_rec->groupmodeforce     = $course_configs->groupmodeforce;
            $course_rec->lang               = $course_configs->lang;
            $course_rec->enablecompletion   = $course_configs->enablecompletion;

            $course_rec->fullname           =
            $course_rec->shortname          =
            $course_rec->idnumber           =
            $course_rec->summary            = '';

            $course_rec->startdate          =
            $course_rec->defaultgroupingid  =
            $course_rec->timecreated        =
            $course_rec->timemodified       = 0;

            return $course_rec;

        } // create_empty_course_object



        /**
         * Routine to find/create a category associated with the
         * specified academic term (identified by its source id)
         *
         * @access  private
         * @param   string    $source_id        Source id (sourcedid) for the term
         * @param   boolean   $create_new       Indicates whether to create a category if needed
         * @return  int                         Category id value or 0 on error
         */
        private function get_category_by_term($source_id, $create_new = true)
        {

            // Check the source_id (key) value
            if (empty($source_id)) return 0;

            // See if it's already there
            if (false === ($term_rec = $this->moodleDB->get_record(self::SHEBANGENT_TERM, array('source_id' => $source_id)))) {

                $term_rec               = new stdClass();
                $term_rec->source_id    =
                $term_rec->desc_short   =
                $term_rec->desc_long    = $source_id;;
                $term_rec->category_id  = 0;
                $term_rec->begin_date   =
                $term_rec->end_date     =
                $term_rec->insert_date  =
                $term_rec->update_date  = date(self::DATEFMT_SQL_VALUE);

                try
                {
                    $term_rec->id = $this->moodleDB->insert_record(self::SHEBANGENT_TERM, $term_rec);
                }
                catch (Exception $exc)
                {
                    $this->log_process_message(self::SHEBANGENT_TERM, 0, 'insert', get_string('ERR_CREATE_TERM', self::PLUGIN_NAME));
                    return 0;
                }

            }

            // If the term has not yet been associated with a category, or if
            // the category id is invalid, create a new one if permitted
            if (empty($term_rec->category_id) || false === ($category = $this->moodleDB->get_record(self::MOODLENT_COURSE_CATEGORY, array('id' => $term_rec->category_id)))) {

                if (!$create_new) return 0;

                $category               = new stdClass();
                $category->name         =
                $category->description  = $term_rec->desc_long;
                $category->parent       = 0;

                if (false === ($category->id = $this->moodleDB->insert_record(self::MOODLENT_COURSE_CATEGORY, $category))) {
                    $this->log_process_message(self::MOODLENT_COURSE_CATEGORY, 0, 'insert', get_string('ERR_CREATE_CATEGORY', self::PLUGIN_NAME));
                    return 0;
                }

                $category->context = context_coursecat::instance($category->id);
                $category->context->mark_dirty();
                fix_course_sortorder();

                $term_rec->category_id = $category->id;
                try
                {
                    $this->moodleDB->set_field(self::SHEBANGENT_TERM, 'category_id', $category->id, array('id' => $term_rec->id));
                }
                catch (Exception $exc)
                {
                    $this->log_process_message(self::SHEBANGENT_TERM, $term_rec->id, 'update', get_string('ERR_UPDATE_TERM', self::PLUGIN_NAME));
                    return 0;
                }

            }

            return $term_rec->category_id;

        } // get_category_by_term



        /**
         * Routine to find categories by name, when having to
         * assign courses to categories based on dept. name
         *
         * @access  private
         * @param   string  $name
         * @param   boolean $create_new
         * @param   int     $parent_id
         * @return  int
         */
        private function get_category_by_name($name, $create_new = true, $parent_id = 0)
        {

            if (false === ($category = $this->moodleDB->get_record(self::MOODLENT_COURSE_CATEGORY, array('name' => $name, 'parent' => $parent_id)))) {

                if (!$create_new) return 0;

                $category               = new stdClass();
                $category->name         =
                $category->description  = $name;
                $category->parent       = $parent_id;

                if (false === ($category->id = $this->moodleDB->insert_record(self::MOODLENT_COURSE_CATEGORY, $category))) {
                    $this->log_process_message(self::MOODLENT_COURSE_CATEGORY, 0, 'insert', get_string('ERR_CREATE_CATEGORY', self::PLUGIN_NAME));
                    return 0;
                }

                $category->context = context_coursecat::instance($category->id);
                $category->context->mark_dirty();
                fix_course_sortorder();

            }

            return $category->id;

        } // get_category_by_name



        /**
         * Create a new course
         *
         * Lifted from Moodle's course/lib.php and modified go back to the database
         * as seldom as possible since it may be called from a batch import.
         *
         * @param  stdClass    $data    Data needed for an entry in the 'course' table
         * @return stdClass             New course instance
         */
        private function create_course(stdClass $data)
        {

            // Normally an application-level RI check for category
            // is done, but that is handled by the calling routine
            //$category = $DB->get_record('course_categories', array('id'=>$data->category), '*', MUST_EXIST);

            // Then an application-level check for shortname
            if (!empty($data->shortname)) {
                if ($this->moodleDB->record_exists('course', array('shortname' => $data->shortname))) {
                    throw new moodle_exception('shortnametaken');
                }
            }

            // Then an application-level check for idnumber uniqueness,
            // but the calling routine has done this already
            //if (!empty($data->idnumber)) {
            //    if ($DB->record_exists('course', array('idnumber' => $data->idnumber))) {
            //        throw new moodle_exception('idnumbertaken');
            //    }
            //}

            $data->timecreated  = time();
            $data->timemodified = $data->timecreated;

            // place at beginning of any category
            $data->sortorder = 0;

            // No editoroptions to consider here
            //if ($editoroptions) {
            //    // summary text is updated later, we need context to store the files first
            //    $data->summary = '';
            //    $data->summary_format = FORMAT_HTML;
            //}

            // Visibility determined by plugin config
            //if (!isset($data->visible)) {
            //    // data not from form, add missing visibility info
            //    $data->visible = $category->visible;
            //}
            //$data->visibleold = $data->visible;

            $newcourseid = $this->moodleDB->insert_record('course', $data);
            $context = context_course::instance($newcourseid, MUST_EXIST);

            // Still, no editoroptions to consider here
            //if ($editoroptions) {
            //    // Save the files used in the summary editor and store
            //    $data = file_postupdate_standard_editor($data, 'summary', $editoroptions, $context, 'course', 'summary', 0);
            //    $DB->set_field('course', 'summary', $data->summary, array('id'=>$newcourseid));
            //    $DB->set_field('course', 'summaryformat', $data->summary_format, array('id'=>$newcourseid));
            //}
            //if ($overviewfilesoptions = course_overviewfiles_options($newcourseid)) {
            //    // Save the course overviewfiles
            //    $data = file_postupdate_standard_filemanager($data, 'overviewfiles', $overviewfilesoptions, $context, 'course', 'overviewfiles', 0);
            //}

            // update course format options
            course_get_format($newcourseid)->update_course_format_options($data);

            $course = course_get_format($newcourseid)->get_course();

            // Setup the blocks
            blocks_add_default_course_blocks($course);

            // Create a default section.
            course_create_sections_if_missing($course, 0);

            // To expensive to do in a batch import,
            // defer until last course is processed
            //fix_course_sortorder();
            // purge appropriate caches in case fix_course_sortorder() did not change anything
            //cache_helper::purge_by_event('changesincourse');

            // new context created - better mark it as dirty
            $context->mark_dirty();

            // Save any custom role names.
            //save_local_role_names($course->id, (array)$data);

            // set up enrolments
            enrol_course_updated(true, $course, $data);

            // No need to log this
            //add_to_log(SITEID, 'course', 'new', 'view.php?id='.$course->id, $data->fullname.' (ID '.$course->id.')');

            // Trigger events
            events_trigger('course_created', $course);

            return $course;
        }



        /**
         * Substitute name tokens for their respective values
         *
         * @access  private
         * @param   string      $pattern    Configuration value containing name tokens
         * @param   stdClass    $lmb_data   The Banner/LMB msg rec
         * @return  string                  The string with tokens substituted for their respective values
         */
        private function replace_name_tokens($pattern, stdClass $lmb_data)
        {

            static $cache_term_rec = array();



            // See if the term rec is cached, if so, use it, otherwise
            // fetch it and place it in the cache
            if (array_key_exists($lmb_data->term, $cache_term_rec)) {
                $term_rec = $cache_term_rec[$lmb_data->term];
                $term_desc = $term_rec->desc_long;
            } elseif (false !== ($term_rec = $this->moodleDB->get_record(self::SHEBANGENT_TERM, array('source_id' => $lmb_data->term)))) {
                // Fetch the term description, given the code and
                // cache it for later
                $cache_term_rec[$lmb_data->term] = $term_rec;
                $term_desc = $term_rec->desc_long;
            } else {
                // Can not find a term rec so just use the code
                $term_desc = $lmb_data->term;
            }

            // We will assume that the dept code, not explicitly identified in the message
            // is the the prefix part of the long description delimited by a hyphen (-).
            list($dept_code, $course_num, $section_num) = explode('-', $lmb_data->desc_long);

            if (!isset($dept_code))   $dept_code   = '';
            if (!isset($course_num))  $course_num  = '';
            if (!isset($section_num)) $section_num = '';

            return preg_replace(self::$courseNameTokens,
                                array($lmb_data->term, $term_desc,
                                      $lmb_data->desc_full, $lmb_data->desc_long, $lmb_data->desc_short, $lmb_data->source_id,
                                      $dept_code, $lmb_data->dept_name,
                                      $lmb_data->course_source_id,
                                      $course_num, $section_num),
                                $pattern);

        } // replace_name_tokens



        /**
         * Looks up the configured Moodle role id given an IMS/LMB
         * roletype value
         *
         * @access  private
         * @param   string      $ims_role_type      The IMS/LMB roletype value
         * @return  mixed                           The role id value or false is not configured or invalid
         */
        private function get_role_mapping($ims_role_type)
        {

            // Find the appropriate role mapping
            $rolemap_config_name = "enroll_rolemap_{$ims_role_type}";
            if (!isset($this->config->$rolemap_config_name) || empty($this->config->$rolemap_config_name)) {
                return false;
            }

            // Validate the role mapping
            $role_id = (int)$this->config->$rolemap_config_name;
            if (!array_key_exists($role_id, $this->roleCache)) {
                return false;
            }

            return $role_id;

        } // get_role_mapping



        /**
         * Checks the last time a message arrived and if needed sends a notification
         *
         * @access  private
         * @return  void
         * @uses    $SITE
         */
        public function cron_monitor_activity()
        {
            global $SITE;


            // If no configs found, don't croak the entire cron run,
            // just leave
            if (!$this->config) {
                mtrace(get_string('ERR_CONFIGS_NOTSET', self::PLUGIN_NAME));
                return;
            }

            $this->prepare_logfiles();

            mtrace(get_string('INF_CRON_MONITOR_START', self::PLUGIN_NAME));

            // Check that monitor is enabled
            if (   !isset($this->config->monitor_weekdays) || empty($this->config->monitor_weekdays)
                || !isset($this->config->monitor_emails)   || empty($this->config->monitor_emails)) {
                mtrace(get_string('INF_CRON_MONITOR_DISABLED', self::PLUGIN_NAME));
                return;
            }

            // Verify the threshold
            if (!isset($this->config->monitor_threshold) || empty($this->config->monitor_threshold)) {
                $this->config->monitor_threshold = self::DEF_MONITOR_THRESHOLD;
            }

            // Check day of week and time of day
            $timestamp_array = getdate();

            // Admin setting for multicheckbox stored as comma delimited
            // string of key values.
            if (!array_key_exists($timestamp_array['wday'], array_fill_keys(explode(',', $this->config->monitor_weekdays), 1))) {
                mtrace(get_string('INF_CRON_MONITOR_WRONGDAY', self::PLUGIN_NAME));
                return;
            }

            $start_timestamp = mktime((int)$this->config->monitor_start_hour,
                                      (int)$this->config->monitor_start_min,
                                      0,
                                      $timestamp_array['mon'],
                                      $timestamp_array['mday'],
                                      $timestamp_array['year']);
            $stop_timestamp  = mktime((int)$this->config->monitor_stop_hour,
                                      (int)$this->config->monitor_stop_min,
                                      0,
                                      $timestamp_array['mon'],
                                      $timestamp_array['mday'],
                                      $timestamp_array['year']);

            // If the stop time appears to be earlier than the start time, assume
            // the stop time is with respect to the following day, i.e. start
            // sometime tonight, finish tomorrow morning.. t1 is our start time,
            // and t2 is the stop time
            if ($stop_timestamp <= $start_timestamp) {
                // Looking for the no-run period t2 < t < t1
                if ($stop_timestamp < $timestamp_array[0] && $timestamp_array[0] < $start_timestamp) {
                    mtrace(get_string('INF_CRON_MONITOR_WRONGTIME', self::PLUGIN_NAME));
                    return;
                }
            } else {
                // Looking for the opposite of t1 <= t <= t2 -- are we NOT in the window
                if ($start_timestamp > $timestamp_array[0] || $timestamp_array[0] > $stop_timestamp) {
                    mtrace(get_string('INF_CRON_MONITOR_WRONGTIME', self::PLUGIN_NAME));
                    return;
                }
            }

            // When was the last message received. Use the log file info
            $minutes_lapsed = floor(($timestamp_array[0] - filemtime($this->messageLogPath)) / 60);
            if ($minutes_lapsed < ((int)$this->config->monitor_threshold)) {
                mtrace(get_string('INF_CRON_MONITOR_MSGTHRESHOLD', self::PLUGIN_NAME));
                return;
            }

            // Was the last notification less than the fixed interval
            $last_notice_file = "{$this->logging_dirpath}/.shebang-monitor-last-notice";
            if (file_exists($last_notice_file) && (floor(($timestamp_array[0] - filemtime($last_notice_file)) / 60)) < self::MONITOR_NOTICES_INTERVAL) {
                mtrace(get_string('INF_CRON_MONITOR_NOTICETHRESHOLD', self::PLUGIN_NAME));
                return;
            }

            // Send a notice
            $user = new stdClass();
            $user->mailformat = 0;

            $email_address_array = preg_split('/[,;]/', $this->config->monitor_emails);
            foreach ($email_address_array as $email_address) {
                $email_address = trim($email_address);
                if (empty($email_address) || !validate_email($email_address))
                    continue;
                $user->firstname = "SHEBanG Monitor";
                $user->lastname  = "Recipient:{$email_address}";
                $user->email     = trim($email_address);
                email_to_user($user, get_admin(), $SITE->shortname . ", SHEBanG Monitor Notice", "SHEBanG Monitor Notice: {$minutes_lapsed} minutes have passed since the last LMB message arrived.");
                mtrace(get_string('INF_CRON_MONITOR_NOTICESENT', self::PLUGIN_NAME, $email_address));
            }

            // Tickle the last notice file
            touch($last_notice_file);

            mtrace(get_string('INF_CRON_MONITOR_FINISH', self::PLUGIN_NAME));

        } // cron_monitor_activity



        /**
         * Sends e-mail notification that message processing error has occurred
         *
         * @access  private
         * @param   string    $msg_id    The message identifier
         * @return  void
         * @uses    $SITE
         */
        private function notify_message_error($msg_id)
        {
            global $SITE;



            if (empty($this->config->responses_notify_on_error))
                return;

            // Was the last notification less than the fixed interval. Check
            // by fetch config record directly from DB, avoid config caching
            // and if problem with DB connection, then it will puke before
            // sending emails.
            $config_rec = null;
            if (false === ($config_rec = $this->moodleDB->get_record('config_plugins', array('plugin' => self::PLUGIN_NAME, 'name' => 'responses_notify_last')))) {
                $config_rec         = new stdClass();
                $config_rec->plugin = self::PLUGIN_NAME;
                $config_rec->name   = 'responses_notify_last';
                $config_rec->value  = '0';
            }

            $timestamp_array = getdate();
            if (floor(($timestamp_array[0] - intval($config_rec->value)) / 60) < self::MONITOR_NOTICES_INTERVAL) {
                return;
            }

            // Update time last notice was sent
            $config_rec->value = $timestamp_array[0];
            try {

                if (empty($config_rec->id)) {
                    $this->moodleDB->insert_record('config_plugins', $config_rec, false);
                } else {
                    $this->moodleDB->update_record('config_plugins', $config_rec);
                }

                $user = new stdClass();
                $user->mailformat = 0;

                $email_address_array = preg_split('/[,;]/', $this->config->responses_emails);
                foreach ($email_address_array as $email_address) {
                    $email_address = trim($email_address);
                    if (empty($email_address) || !validate_email($email_address))
                        continue;
                    $user->firstname = "SHEBanG Processing Error";
                    $user->lastname  = "Recipient:{$email_address}";
                    $user->email     = trim($email_address);
                    email_to_user($user, get_admin(), $SITE->shortname . ", SHEBanG Processing Error", "SHEBanG Processing Error: Failed to process message with Id {$msg_id}.");
                }

            }
            catch (Exception $exc) {

                $this->log_process_exception($exc);

            }

        } // notify_message_error


    } // class
