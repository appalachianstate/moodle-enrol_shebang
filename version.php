<?php

    defined('MOODLE_INTERNAL') || die();

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

    /**
     * Version info for shebang enrol module/plugin. Included
     * from the lib/adminlib.php:upgrade_plugins function
     * during site plugins administration.
     */

    $plugin             = new stdClass();

    $plugin->version    = 2011031701;
    $plugin->requires   = 2010000000;
    $plugin->release    = "0.0.2";
    $plugin->cron       = 1;
