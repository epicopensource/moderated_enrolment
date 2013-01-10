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
 * This page shows all course enrolment options for current user.
 *
 * @package    moderated enrol
 * @copyright  Epic
 * @author     Ivan Tashev
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once("$CFG->dirroot/enrol/moderated/locallib.php");

$id = required_param('id', PARAM_INT);
$data = required_param('data', PARAM_TEXT);

require_login();


$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

$data_encode = base64_decode($data);

$data_explode = explode('$$$', $data_encode);
$student_id = $data_explode[1];

$student = $DB->get_record('user', array('id'=>$student_id));

// Everybody is enrolled on the frontpage
if ($course->id == SITEID) {
    redirect("$CFG->wwwroot/");
}

//only assigned mentor can see this page
if($data_explode[2] !== $USER->id) {
    print_error('notallowed', 'enrol_moderated');
}


//$PAGE->set_course($course);
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
$PAGE->set_pagelayout('course');

$PAGE->requires->css('/enrol/moderated/style.css');
$PAGE->set_url('/enrol/moderated/index.php', array('id'=>$course->id));

// do not allow enrols when in login-as session
if (session_is_loggedinas() and $USER->loginascontext->contextlevel == CONTEXT_COURSE) {
       
}

$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('pluginname','enrol_moderated'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname','enrol_moderated'));
$form = new enrol_confirm_student_form($CFG->wwwroot.'/enrol/moderated/confirmuser.php?id='.$course->id.'&data='.$data,$student);
if($data = $form->get_data()) {
    if($data->submitbutton == 'Confirm') {
        if(getMentor($course->id, $USER->email) || is_siteadmin($USER)) {
            if(confirmuser($student->id, $course->id)) {
                echo $OUTPUT->box(get_string('userenroled', 'enrol_moderated'));
            } else {
                echo $OUTPUT->box(get_string('userenrolerror', 'enrol_moderated'));
            }
        }
        
    } else if($data->submitbutton == 'Cancel') {
        send_email_student($student->id, $course->id, $USER->id, false);
        echo $OUTPUT->box(get_string('usercancelemailsent', 'enrol_moderated'));
    }
} else {
    $form->display();
}

echo $OUTPUT->footer();

?>