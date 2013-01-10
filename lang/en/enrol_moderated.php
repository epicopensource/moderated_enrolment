<?php
$string['enrolname'] = 'Moderated enrolment';
$string['pluginname'] = 'Moderated enrolment';
$string['pluginname_desc'] = 'Enrols the user in the course when he requests access in Moodle';

$string['assignrole'] = 'Assign role';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users';
$string['enrolenddate'] = 'End date';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolperiod'] = 'Enrolment period';
$string['enrolperiod_desc'] = 'Default length of the enrolment period (in seconds).'; //TODO: fixme
$string['enrolstartdate'] = 'Start date';
$string['mailadmins'] = 'Notify admin';
$string['mailstudents'] = 'Notify students';
$string['mailteachers'] = 'Notify teachers';
$string['nocost'] = 'There is no cost associated with enrolling in this course!';
$string['moderated:config'] = 'Configure moderated enrol instances';
$string['moderated:manage'] = 'Manage enrolled users';
$string['moderated:unenrol'] = 'Unenrol users from course';
$string['moderated:unenrolself'] = 'Unenrol self from the course';
$string['status'] = 'Allow moderated enrolments';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['submit'] = 'Submit';
$string['validemail'] = 'Please enter a valid email address';
$string['enteremail_desc'] = 'Please enter the email address of your mentor in the field below.';
$string['enteremail'] = 'Mentor email:';
$string['notmentor'] = 'The email you supplied is not assigned to a mentor';
$string['emailsent'] = 'An email has been sent to your menor. Once your request has been confirmed, you will be able to access the course.';
$string['emailalreadysent'] = 'Email already sent';
$string['confirmuser'] = 'Confirm request';
$string['userconfirmenrol'] = 'Click the "Confirm" link below to assign {$a->sname} as your mentee and enrol on the {$a->name} course. Click "Cancel" to deny this request.';
$string['emailmentor'] = 'Hi {$a->firstname},

{$a->studentname} ({$a->studentemail}) would like to enrol on the {$a->coursename} course and has asked you to be their mentor.

As a mentor, you will be able to review their progress within the course.

Click here to approve or cancel this request and notify {$a->studentemail} 

{$a->link}

In most mail programs, this should appear as a blue link
which you can just click on.  If that doesn\'t work,
then cut and paste the address into the address
line at the top of your web browser window.

If you need help, please contact the site administrator,
{$a->admin}';

$string['emailmentorsubject'] = '{$a->studentname} has asked you to be their mentor on the {$a->coursename} course';

$string['emailstudentenrol'] = 'Hi {$a->firstname},

{$a->mentor} ({$a->mentoremail}) has accepted your request to be your mentor. You now have access to the {$a->coursename} course. 
Your mentor will be able to log in and review your progress.

Click here to view the course: 

{$a->link} 

In most mail programs, this should appear as a blue link
which you can just click on.  If that doesn\'t work,
then cut and paste the address into the address
line at the top of your web browser window.

If you need help, please contact the site administrator,
{$a->admin}';

$string['emailstudentenrolsubjectaccept'] = '{$a->mentor} has agreed to be your mentor on the {$a->coursename} course';
$string['emailstudentenrolsubjectdecline'] = '{$a->mentor} has declined your request for enrolment on the {$a->coursename} course';

$string['emailstudentenroldecline'] = 'Hi {$a->firstname},

{$a->mentor} ({$a->mentoremail}) has declined your request for {$a->coursename} course. 

If you need help, please contact the site administrator,
{$a->admin}';
$string['notallowed'] = 'You are not allowed to view this page';
$string['moderated:enrol'] = 'Enrol students';
$string['userenroled'] = 'User successfully enrolled. They will now be notified by email, and you will also be able to review thier progress within the system. <br /><a href="../../">Continue</a>';
$string['userenrolerror'] = 'There was an error while trying to assign this user as mentee or enrol them on to course.';
$string['usercancelemailsent'] = 'Request declined. User will be notified via email.';
$string['enrolinstancedefaults_desc'] = 'The moderated enrolment plugin has no settings to modify.';
?>
