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


    define('NO_OUTPUT_BUFFERING', true);

    // Get Moodle going
    require_once(dirname(__FILE__) . '/../../config.php');
    // Need our particular enrollment plugin's consts
    require_once(dirname(__FILE__) . '/locallib.php');

    // No anonymous access for this page
    require_login();

    // Must be a site administrator
    require_capability('moodle/site:config', context_system::instance());


    $task = optional_param('task', 'index', PARAM_ALPHA);
    if (!file_exists(dirname(__FILE__) . "/tools/tools_{$task}_class.php")) {
        $task = 'index';
    }

    include(dirname(__FILE__) . "/tools/tools_{$task}_class.php");

    $tool_class_name = enrol_shebang_processor::PLUGIN_NAME . "_tools_{$task}";
    $tool = new $tool_class_name();

    $tool->handle_request();
