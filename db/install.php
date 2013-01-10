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
 * Moderate enrolment plugin installation.
 *
 * @package    enrol
 * @subpackage moderated
 * @author     Ivan Tashev
 * @copyright  Epic - 2012
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_moderated_install() {
    global $CFG, $DB;
    
    $event = new stdClass();

	// Add event handler
	$conditions = array ('eventname' => 'user_created', 'component' => 'moderated');
    if (!$DB->record_exists ('events_handlers', $conditions))
    {
        $event = 'user_created';
        $handler->eventname = $event;
        $handler->component = 'moderated';
        $handler->handlerfile = '/enrol/moderated/enrol.php';
        $handler->handlerfunction = serialize ('moderated_'.$event);
        $handler->schedule = 'instant';
        $handler->status = 0;

        $DB->insert_record ('events_handlers', $handler);
    }

}
