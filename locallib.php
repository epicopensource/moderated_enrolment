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
 * Self enrol plugin implementation.
 *
 * @package    enrol
 * @subpackage moderated
 * @copyright  Epic
 * @author     Ivan Tashev
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//this file is requered from lib.php and confirmuser.php
if (file_exists("../config.php")) {
    require_once("../config.php");
} else {
   require_once("../../config.php");
}
require_once($CFG->dirroot.'/enrol/locallib.php');
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/admin/roles/lib.php');

class enrol_moderated_form extends moodleform {
    protected $instance;

    /**
     * Overriding this function to get unique form id for multiple self enrolments
     *
     * @return string form identifier
     */
//    protected function get_form_identifier() {
//        $formid = $this->_customdata->id.'_'.get_class($this);
//        return $formid;
//    }

    public function definition() {
        global $DB;
        

        $mform = $this->_form;
        $instance = $this->_customdata;
        $this->instance = $instance;
        
        $mform->addElement('html', '<link rel="stylesheet" href="style.css" type="text/css" media="screen">');
        $plugin = enrol_get_plugin('moderated');

        $heading = $plugin->get_instance_name($instance);
	$description = get_string('enteremail_desc', 'enrol_moderated');
        $mform->addElement('html', '<h2 style="padding: 15px;">'.$heading.'</h2>');
        $mform->addElement('html', '<p style="padding: 0 0 0 15px;">'.get_string('moderatedinstructions', 'enrol_moderated') . '</p><p style="padding: 0 0 0 15px;">' .get_string('moderatedinstructions2', 'enrol_moderated'). '</p>');
        $mform->addElement('html', '<p style="padding: 0 0 0 15px;">'.get_string('enteremail_desc', 'enrol_moderated').'</p>');
        $mform->addElement('text', 'email', get_string('enteremail', 'enrol_moderated'));
        $this->add_action_buttons(false, get_string('submit', 'enrol_moderated'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $instance->courseid);

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
        $mform->setDefault('instance', $instance->id);
        
    }

    public function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);


        if (!checkEmail($data['email'])) {
            $errors['email'] = get_string('validemail', 'enrol_moderated');
        } else if(!getMentor($data['id'], $data['email'])) {
            $errors['email'] = get_string('notmentor', 'enrol_moderated');
        }

        return $errors;
    }
    
    
    
}



class enrol_confirm_student_form extends moodleform {
    public function definition() {       
        global $COURSE;

        $mform = $this->_form;
        $student = $this->_customdata;

        
        $studentname = new stdClass();
        $studentname->sname = $student->firstname.' '.$student->lastname;
        $studentname->name = $COURSE->fullname;

        $mform->addElement('html', '<div class="moderated_text">'.get_string('userconfirmenrol', 'enrol_moderated', $studentname).'</div>');

        $mform->addElement('hidden', 'student_id');
        $mform->setType('student_id', PARAM_INT);
        $mform->setDefault('student_id', $student->id);
        
        //if you change the value of the action button then change condtion in confirmuser.php around line 76
        //Ivan Tashev - 11062012
        $this->add_action_buttons(false, 'Confirm');
        $this->add_action_buttons(false, 'Cancel');
    }   
}


    function checkEmail($email) {
        $result = true;
	
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	    $result = false;
	}
	return $result;	
    }


/*
 * Checks if request already sent
 */
function isrequestsent($userid, $mentorid) {
    global $DB;
    
    if($DB->record_exists('enrol_moderated', array('userid'=>$userid, 'mentor'=>$mentorid, 'status'=>'false'))) {
        return true;
    } else {
        return false;
    }
}

/*
 * Getting mentor for this course
 */
function getMentor($courseid, $mentoremil) {
    global $DB;
   
    $mentor_email = $DB->get_record('user', array('email'=>$mentoremil));
    $context = get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST);
    

    if($mentor_email) {
        $mentor = $DB->get_record('role_assignments', array('userid'=>$mentor_email->id, 'roleid'=>get_mentor_roleid(), 'contextid'=>$context->id));
        if($mentor){
            return $mentor->userid;
        } else {
            return false;
        }
        
    } else {
        return false;
    }
}




/*
 * It sends email to the mentor
 */
function send_email_mentor($userid, $mentorid, $courseid) {
    global $CFG, $DB, $CFG;
    
    $course = $DB->get_record('course', array('id'=>$courseid));
    $site = get_site();
    $supportuser = generate_email_supportuser();
    $user = $DB->get_record('user', array('id'=>$mentorid));
    $student_info = $DB->get_record('user', array('id'=>$userid));
    $confirmdata = base64_encode("The_user_id_is:$$$".$userid."$$$".$mentorid."");

    
    $data = new stdClass();
    $data->firstname = fullname($user);
    $data->sitename  = format_string($site->fullname);
    $data->admin     = generate_email_signoff();
    $data->coursename = $course->fullname;
    $data->studentname = fullname($student_info);;
    $data->studentemail = $student_info->email;

    $subject = get_string('emailmentorsubject', 'enrol_moderated', $data);
    
    $data->link  = $CFG->wwwroot .'/enrol/moderated/confirmuser.php?id='.$courseid.'&data='.urlencode($confirmdata);    
    
    $message     = get_string('emailmentor', 'enrol_moderated', $data);
    $messagehtml = '';

    $user->mailformat = 1;  // Always send HTML version as well

    //directly email rather than using the messaging system to ensure its not routed to a popup or jabber
    return email_to_user($user, $supportuser, $subject, $message, $messagehtml);
}


/*
 * When mentor confirm user, it sends email to notify user
 */
function send_email_student($userid, $courseid, $mentorid, $accept) {
    global $DB, $CFG;
    
    $user =  $DB->get_record('user', array('id'=>$userid));
    $mentor = $DB->get_record('user', array('id'=>$mentorid));
    $course = $DB->get_record('course', array('id'=>$courseid));
    $supportuser = generate_email_supportuser();
    $messagehtml = '';
    $data = new stdClass();
    $data->firstname = fullname($user);
    $data->mentor  = fullname($mentor);
    $data->coursename = $course->fullname;
    $data->studentemail = $user->email;
    $data->mentoremail = $mentor->email;
    $data->coursename = $course->fullname;
    $data->admin = generate_email_signoff();
    $data->link  = $CFG->wwwroot .'/course/view.php?id='.$courseid; 
    if($accept && $user && $mentor) {
        $subject = get_string('emailstudentenrolsubjectaccept', 'enrol_moderated', $data);
        $message     = get_string('emailstudentenrol', 'enrol_moderated', $data);
    } else {
        $subject = get_string('emailstudentenrolsubjectdecline', 'enrol_moderated', $data);   
        $message     = get_string('emailstudentenroldecline', 'enrol_moderated', $data); 
    }
    
    $user->mailformat = 1;  // Always send HTML version as well
    return email_to_user($user, $supportuser, $subject, $message, $messagehtml);
}

function confirmuser($userid, $courseid) {
    global $DB;
     
    $confirm = $DB->get_record('enrol_moderated', array('userid'=>$userid, 'courseid'=>$courseid));
    
    if($confirm) {
        $updaterecord = new stdClass();
        $updaterecord->id = $confirm->id;
        $updaterecord->userid = $userid;
        $updaterecord->courseid = $courseid;
        $updaterecord->modified = time();
        $updaterecord->status = 'true';
        $DB->update_record('enrol_moderated', $updaterecord);

        //enrol student
        enrol_student($userid, $courseid);
        
        //add to mentor
        assign_to_mentor($userid, $confirm->mentor);
        
        //send email to user that enrol is confirmed
        send_email_student($userid, $courseid, $confirm->mentor, true);
        
        return true;
            
    } else {
        return false;
    }
    
            
}


function enrol_student($userid, $courseid) {
    global $DB, $PAGE;
    
    $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
    $manager = new course_enrolment_manager($PAGE, $course);
    $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
    $context = get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST);
    
    $enrol = $DB->get_record('enrol', array('enrol'=>'moderated', 'status'=>'0', 'courseid'=>$courseid));
    
    
    $instances = $manager->get_enrolment_instances();
    $plugins = $manager->get_enrolment_plugins();
        
    if (!array_key_exists($enrol->id, $instances)) {
        print_error('key does not exist');
    }
        
    $instance = $instances[$enrol->id];

    $plugin = $plugins[$instance->enrol];

    if (has_capability('enrol/'.$plugin->get_name().':enrol', $context)) {
        $plugin->enrol_user($instance, $user->id, 5);
    } else {
        print_error('error');
    }
}

/*
 * Assigning user to mentor
 */
function assign_to_mentor($userid, $mentorid) {
        $context = get_context_instance(CONTEXT_USER, $userid, MUST_EXIST);
        role_assign(get_mentor_roleid(), $mentorid, $context->id);
}

function get_mentor_roleid() {
    global $DB;
    
    $mentor_role = $DB->get_record('role', array('shortname'=>'mentor'));
    
    return $mentor_role->id;
}


function remove_unenrolled_users($courseid) {
    
//    $enrolled_users = course_enrolments($courseid);
//    echo 'sadf';
//    po($enrolled_users);
//    echo ($enrolled_users);
//    die();
}

