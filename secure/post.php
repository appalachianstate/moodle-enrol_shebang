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


    // Only interested in POSTs
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header("HTTP/1.0 405 Method Not Allowed");
        header("Allow: POST");
        die;
    }

    // Point of entry for the LMB posted message, so gotta get Moodle going.
    // Expect that this file is in enrol/shebang/secure (three dirs down from
    // the Moodle rootdir).
    require_once (dirname(__FILE__) . '/../../../config.php');
    // We may end up creating a course group
    require_once($CFG->dirroot . '/group/lib.php');
    // And we want our enrollment module too
    require_once (dirname(__FILE__) . '/../lib.php');


    if (!defined('SHEBANG_SECURE_REALM')) define ('SHEBANG_SECURE_REALM', 'SHEBanG Authentication');
    if (!defined('SHEBANG_HEADER_MSGID')) define ('SHEBANG_HEADER_MSGID', 'JMSMessageId');
    if (!defined('SHEBANG_HEADER_LDISP')) define ('SHEBANG_HEADER_LDISP', 'ldisp_id');


    $enrolment_plugin = new enrol_shebang_plugin();


    // First check if this plugin is enabled
    if (!enrol_is_enabled('shebang')) {
        header("HTTP/1.0 501 Not Implemented");
        die;
    }

    // If enabled, then check for application-level security
    if (!check_security($enrolment_plugin->getSecureUsername(),
                        $enrolment_plugin->getSecurePassword(),
                        $enrolment_plugin->getSecureMethod() == enrol_shebang_plugin::OPT_SECURE_METHOD_BASIC)) {
        die;
    }

    // If we get to this point, then security checks, if any, are satisfied
    if (!($msg_headers = getallheaders())) {
        header("HTTP/1.0 400 Bad Request");
        die(get_string('ERR_MSG_NOHEADERS', enrol_shebang_plugin::PLUGIN_NAME));
    }

    $msg_id  = array_key_exists(SHEBANG_HEADER_LDISP, $msg_headers)
             ? $msg_headers[SHEBANG_HEADER_LDISP]
             : SHEBANG_HEADER_LDISP;
    $msg_id .= ":";
    $msg_id .= array_key_exists(SHEBANG_HEADER_MSGID, $msg_headers)
             ? $msg_headers[SHEBANG_HEADER_MSGID]
             : SHEBANG_HEADER_MSGID;

    $enrolment_plugin->import_lmb_message(file_get_contents('php://input'), $msg_id);

    exit; /* End of page processing
    -------------------------------------------------------------------------------- */




    /* --------------------------------------------------------------------------------
     * Page helper functions
     */


    /**
     * Check credentials if needed
     *
     * @param   string      $username       User name to check
     * @param   string      $password       Password to check
     * @param   boolean     $basic_method   User HTTP Basic authentication (default is Digest)
     * @return  boolean
     */
    function check_security($username, $userpwd, $basic_method = false)
    {

        // No username and no password, then it is unprotected
        if (empty($username) && empty($userpwd)) {
            return true;
        }

        // If they configured BASIC auth, do that, otherwise do DIGEST
        if ($basic_method) {

            if (   isset($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_USER'])
                && $_SERVER['PHP_AUTH_USER'] === $username
                && $_SERVER['PHP_AUTH_PW']   === $userpwd) {

                return true;
            }

            // Either credentials not there or they didn't match so
            // arrange to send a 401 header with Basic auth header
            header("WWW-Authenticate: Basic realm=\"" . SHEBANG_SECURE_REALM . "\"");

        } else {

            if (   isset($_SERVER['PHP_AUTH_DIGEST']) && !empty($_SERVER['PHP_AUTH_DIGEST'])
                && digest_response_matches($_SERVER['PHP_AUTH_DIGEST'], $username, $userpwd, $_SERVER['REQUEST_METHOD'])) {

                return true;

            }

            // Either credentials not there or they didn't match so
            // arrange to send a 401 header with Digest auth header
            header("WWW-Authenticate: Digest realm=\"" . SHEBANG_SECURE_REALM . "\",qop=\"auth\",nonce=\"" . uniqid() ."\",opaque=\"" . md5(SHEBANG_SECURE_REALM) ."\"");

        }

        // Dropped through to here when credentials not present or
        // they did not match, so WWW-Authenticate header of the
        // correct flavor should already be set, set the 401 header
        // the cancel message and return false so the page will die
        header("HTTP/1.0 401 Unauthorized");
        echo "Authentication Failed";

        return false;

    } // check_security



    /**
     * Examine an HTTP Digest authentication server variable and check
     * the credentials. This routine was lifted from the online PHP
     * Manual (http://php.net/manual/en/features.http-auth.php) Example
     * No. 7.
     *
     * @param   string  $digest_server_var  The PHP_AUTH_DIGEST server var
     * @param   string  $username           The username against which to check
     * @param   string  $userpwd            The password against which to check
     * @param   string  $request_method     The HTTP request method used
     * @return  boolean
     */
    function digest_response_matches($digest_server_var, $username, $userpwd, $request_method)
    {

        // Need to see each of these elements in the PHP_AUTH_DIGEST
        // server var. Set up an assoc. array with the keys that are
        // needed--the value part isn't important, just use a dummy
        // val of 1
        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1,
                              'username'=>1, 'uri'=>1, 'response'=>1);
        preg_match_all('@(' . implode('|', array_keys($needed_parts)) . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@',
          $digest_server_var, $matches, PREG_SET_ORDER);

        // Where we got a match, the element is present in the digest
        // server var, so copy it to the $data array and remove from
        // the $needed_parts array
        $data = array();
        foreach ($matches as $m) {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
        }

        // If there were any elements missing in the digest server var
        // the corresponding key will still be present in the array
        if ($needed_parts) return false;

        // Generate the valid response from our perspective, and that
        // should match the response element passed in the digest server
        // var
        $a1 = md5($username . ':' . SHEBANG_SECURE_REALM . ':' . $userpwd);
        $a2 = md5($request_method . ':' . $data['uri']);
        $valid_response = md5($a1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' .$a2);

        // If their response matches our response, let 'em in
        return ($data['response'] === $valid_response);

    } // digest_response_matches

