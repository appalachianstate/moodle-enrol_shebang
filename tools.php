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


    // Get Moodle going
    require_once(dirname(__FILE__) . '/../../config.php');
    
    // Need our particular enrollment plugin's consts 
    require_once(dirname(__FILE__) . '/enrol.php');
    
    // No anonymous access for this page
    require_login();

    // Must be a site administrator
    require_capability('moodle/site:config', get_system_context());

    
    $action             = optional_param('action', 'index', PARAM_ALPHA);
    $tool_class_name    = enrolment_plugin_shebang::PLUGIN_NAME . "_tools_{$action}";

    if (file_exists(dirname(__FILE__) . "/tools/tools_{$action}_class.php")) {

        include_once(dirname(__FILE__) . "/tools/tools_{$action}_class.php");
        $tool = new $tool_class_name();
        $tool->handle_request();

    }
