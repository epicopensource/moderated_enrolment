<?php

/**
 * @package    enrol
 * @subpackage moderated
 * @author     Ivan Tashev 
 * @copyright  Epic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');


class enrol_moderated_edit_form extends moodleform {

    function definition() {
        $mform = $this->_form;

        list($instance, $plugin, $context) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_moderated'));

        // duncan croucher - 10/01/13 - Commented out the below line as we don't want the user selecting their own name for the enrolment instance.
        
        //$mform->addElement('text', 'name', get_string('custominstancename', 'enrol'));

        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'status', get_string('status', 'enrol_moderated'), $options);
        $mform->setDefault('status', $plugin->get_config('status'));

        if ($instance->id) {
            $roles = get_default_enrol_roles($context, $instance->roleid);
        } else {
            $roles = get_default_enrol_roles($context, $plugin->get_config('roleid'));
        }
        
        // duncan croucher - 10/01/13 - commented out the below as we don't won't the user to select a different role here. As plugin won't 
        // function if anything but Mentor is selected.
        
        // $mform->addElement('select', 'roleid', get_string('assignrole', 'enrol_moderated'), $roles);
       // $mform->setDefault('roleid', $plugin->get_config('roleid'));


        $mform->addElement('duration', 'enrolperiod', get_string('enrolperiod', 'enrol_moderated'), array('optional' => true, 'defaultunit' => 86400));
        $mform->setDefault('enrolperiod', $plugin->get_config('enrolperiod'));


        $mform->addElement('date_selector', 'enrolstartdate', get_string('enrolstartdate', 'enrol_moderated'), array('optional' => true));
        $mform->setDefault('enrolstartdate', 0);


        $mform->addElement('date_selector', 'enrolenddate', get_string('enrolenddate', 'enrol_moderated'), array('optional' => true));
        $mform->setDefault('enrolenddate', 0);

        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'courseid');

        $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));

        $this->set_data($instance);
    }

    function validation($data, $files) {
        global $DB, $CFG;
        $errors = parent::validation($data, $files);

        list($instance, $plugin, $context) = $this->_customdata;

        if ($data['status'] == ENROL_INSTANCE_ENABLED) {
            if (!empty($data['enrolenddate']) and $data['enrolenddate'] < $data['enrolstartdate']) {
                $errors['enrolenddate'] = get_string('enrolenddaterror', 'enrol_moderated');
            }

        }

        return $errors;
    }
}
