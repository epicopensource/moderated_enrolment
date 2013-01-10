<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moderated enrolment plugin settings and presets.
 *
 * @package    enrol
 * @subpackage moderated
 * @copyright  2012 Epic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
if ($ADMIN->fulltree) {

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_moderated_settings', '', get_string('pluginname_desc', 'enrol_moderated')));
    $settings->add(new admin_setting_heading('enrol_moderated_defaults',
        get_string('enrolinstancedefaults_desc', 'enrol_moderated'),'<a href="'.$CFG->wwwroot.'">'.get_string('continue','').'</a>'));
}

