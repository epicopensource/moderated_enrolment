-----------------------------------------------------------------------------

Moderated Enrolment plugin for Moodle
Copyright (C) 2012 Epic (http://www.epic.co.uk/)

This program is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the Free
Software Foundation, either version 3 of the License, or (at your option)
any later version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
more details.

You should have received a copy of the GNU General Public License along with
this program. If not, see http://www.gnu.org/copyleft/gpl.html.

-----------------------------------------------------------------------------

Summary
---------

This is an enrolment plug-in to allow moderated enrolment onto courses. 

Description
-------------

The moderated enrolment plug-in allows a  mentor to manage user enrolment onto courses.  

When the course is set up to use this method of enrolment the student level user will have to request access to the course. They do this by entering the email of their mentor into Moodle.

Once this is done Moodle then sends an email to the mentor with a link to approve or cancel the users request for enrolment onto the course. 

When the mentor accepts the request the student is then notified by email and provided a link to access the course.

Requirements
---------------

Moodle 2.2, 2.3 or 2.4

Installation
--------------

1.	Unpack the module into your Moodle install in order to create a enrol/moderated directory. 
2.	Visit the /admin/index.php page to trigger the database installation.
3.	Navigate to Site administration/Plugins/Enrolments/Manage enrol plugins and Enable Moderated enrolment. 

The plug-in doesn't have any additional settings.

Prerequisite to using the Moderated Enrolment plug-in: for the moderated enrolment plug-in to work the role of 'Mentor' will need to be set up in Moodle. This can be done by the administrator in 'Define roles' this can be fresh new role or a copy of an existing role renamed. You will need to make sure the role has all the permissions for moderated enrolment set to allow.

Bugs/patches
--------------

Feel free to send bug reports (and/or patches!) to the current maintainer:

  Mark Aberdour (mark.aberdour@epic.co.uk)

Changes
-------------

(see the ChangeLog.txt file)





