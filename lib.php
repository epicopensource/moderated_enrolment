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
 * Moderate enrolment plugin.
 *
 * This plugin allows you to set courses to enrol users to when they sign up to Moodle
 *
 * @package    enrol
 * @subpackage moderated
 * @copyright  Epic
 * @author     Ivan Tashev
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/enrol/locallib.php");

class enrol_moderated_plugin extends enrol_plugin {
    
    public function enrol_page_hook(stdClass $instance) {
        global $CFG, $OUTPUT, $COURSE, $USER, $DB;
        
        if (isguestuser()) {
            // can not enrol guest!!
            return null;
        }
        if ($DB->record_exists('user_enrolments', array('userid'=>$USER->id, 'enrolid'=>$instance->id))) {
            //TODO: maybe we should tell them they are already enrolled, but can not access the course
            return null;
        }

        if ($instance->enrolstartdate != 0 and $instance->enrolstartdate > time()) {
            //TODO: inform that we can not enrol yet
            return null;
        }

        if ($instance->enrolenddate != 0 and $instance->enrolenddate < time()) {
            //TODO: inform that enrolment is not possible any more
            return null;
        }  
        
        require_once("$CFG->dirroot/enrol/moderated/locallib.php");      
        $output = '<link rel="stylesheet" href="moderated/style.css" type="text/css">';
        $form = new enrol_moderated_form($CFG->wwwroot.'/enrol/index.php?id='.$instance->courseid,$instance);
        ob_start();
        if ($data = $form->get_data()) {
            $email = '';
            $mentorid = 0;
            
            if(isset($_POST['email'])) {
                    $email = $_POST['email'];
            }
            
            //check again if mentor
            if($mentor = getMentor($instance->courseid, $email)) {
                
                //insert request in moderated enrol
                if(!$DB->record_exists('enrol_moderated', array('userid'=>$USER->id, 'courseid'=>$COURSE->id))) {
                    $record = new stdClass();
                    $record->userid = $USER->id;
                    $record->mentor = $mentor;
                    $record->status = 'false';
                    $record->courseid = $COURSE->id;
                    $record->timemodified = time();
                    $DB->insert_record('enrol_moderated', $record);
                }
                
                //send email
                send_email_mentor($USER->id, $mentor, $COURSE->id);
                $output .= '<p class="moderated">'.get_string('emailsent', 'enrol_moderated').'</p>';
                $output .= $OUTPUT->continue_button(new moodle_url($CFG->wwwroot));

                $output .= '';
                //insert record in db
            } else {
                $output = '<p class="moderate">'.get_string('emailsent', 'enrol_moderated').'</p>';
                $form->display($CFG->wwwroot.'/enrol/index.php?id='.$instance->courseid, $instance);
                $output = ob_get_clean();
            }
        } else {
            $form->display($CFG->wwwroot.'/enrol/index.php?id='.$instance->courseid, $instance);
            $output = ob_get_clean();
        }

        

        return $OUTPUT->box($output);
    }
    
    public function roles_protected() {
        
        // users with role assign cap may tweak the roles later
        return false;
    }

    public function allow_unenrol(stdClass $instance) {
        return true;
    }

    public function allow_manage(stdClass $instance) {
        return true;
    }

    public function show_enrolme_link(stdClass $instance) {
        return ($instance->status == ENROL_INSTANCE_ENABLED);
    }

    /**
     * Sets up navigation entries.
     *
     * @param object $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'moderated') {
             throw new coding_exception('Invalid enrollment instance type!');
        }

        $context = get_context_instance(CONTEXT_COURSE, $instance->courseid);
        if (has_capability('enrol/moderated:config', $context)) {
            $managelink = new moodle_url('/enrol/moderated/edit.php', array('courseid'=>$instance->courseid, 'id'=>$instance->id));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'moderated') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = get_context_instance(CONTEXT_COURSE, $instance->courseid);

        $icons = array();

        if (has_capability('enrol/moderated:config', $context)) {
            $editlink = new moodle_url("/enrol/moderated/edit.php", array('courseid'=>$instance->courseid, 'id'=>$instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('i/edit', get_string('edit'), 'core', array('class'=>'icon')));
        }

        return $icons;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        $context = get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/moderated:config', $context)) {
            return NULL;
        }

        return new moodle_url('/enrol/moderated/edit.php', array('courseid'=>$courseid));
    }
	    /**
     * Gets an array of the user enrolment actions
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol_user($instance, $ue) && has_capability("enrol/moderated:unenrol", $context)) {
            $url = new moodle_url('/enrol/moderated/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url, array('class'=>'unenrollink', 'rel'=>$ue->id));
        }
        return $actions;
    }
}

