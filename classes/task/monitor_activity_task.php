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

    namespace enrol_shebang\task;

    use core\task\scheduled_task;

    defined('MOODLE_INTERNAL') || die();

    require_once(__DIR__ . '/../../locallib.php');



    class monitor_activity_task extends scheduled_task
    {

        /**
         * {@inheritDoc}
         * @see \core\task\scheduled_task::get_name()
         */
        public function get_name()
        {
            return get_string('monitor_activity_task_name', 'enrol_shebang');
        }


        /**
         * {@inheritDoc}
         * @see \core\task\task_base::execute()
         */
        public function execute()
        {

            try {
                $processor = new \enrol_shebang_processor();
                $processor->monitor_activity();
            }
            catch (Exception $exc) {
                $info = get_exception_info($exc);
                mtrace(bootstrap_renderer::early_error($info->message, $info->moreinfourl, $info->link, $info->backtrace, $info->debuginfo, $info->errorcode));
            }

        }

    }