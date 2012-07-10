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
     * @copyright   2010 Appalachian State Universtiy, Boone, NC
     * @license     GNU General Public License version 3
     * @package     enrol
     * @subpackage  shebang
     */

    defined('MOODLE_INTERNAL') || die();

    require_once("$CFG->dirroot/lib/ddllib.php");


    /**
     * Handle database updates
     *
     * @param   int         $oldversion     The currently recorded version for this mod/plugin
     * @return  boolean
     * @uses $DB
     */
    function xmldb_enrol_shebang_upgrade($oldversion=0) {

        global $DB;


        $dbman = $DB->get_manager();
        $result = true;


        if ($oldversion < 2012062500) {

            try
            {

                // Start keeping the person/user association in the person
                // table (with a user id) rather than in the user table in
                // the form the SCTID value in the idnumber column
                $table = new xmldb_table('enrol_shebang_person');
                $field = new xmldb_field('userid_moodle', XMLDB_TYPE_INTEGER, 10, false, false, false, null, 'update_date');
                $dbman->add_field($table, $field);

                $key   = new xmldb_key('xak4', XMLDB_KEY_UNIQUE, array('userid_moodle'));
                $dbman->add_key($table, $key);

                unset($table); unset($field); unset($key);

                // Only one enrollment per person/roletype in a given course section
                $table = new xmldb_table('enrol_shebang_member');
                $key   = new xmldb_key('xak1', XMLDB_KEY_UNIQUE, array('section_source_id','person_source_id','roletype'));

                $dbman->add_key($table, $key);

                unset($table); unset($key);

                // Populate new column with values if possible
                $sql = "UPDATE {enrol_shebang_person} p "
                     . "   SET p.userid_moodle = ( "
                     . "SELECT u.id FROM {user} u WHERE u.idnumber = p.userid_sctid) ";

                $result = $DB->execute($sql);

            }
            catch (Exception $exc)
            {
                $result = false;
            }

        }

        return $result;

    } // xmldb_enrol_shebang_upgrade
