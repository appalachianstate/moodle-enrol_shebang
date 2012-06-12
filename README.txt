 README.txt for SHEBanG enrolment plugin/module
 
 DISCLAIMER AND LICENSING
 ------------------------
 SHEBanG enrollment plugin for SunGard HE Banner(r) data import
 
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or (at
 your option) any later version.
 
 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with this program. If not, see <http://www.gnu.org/licenses/>.
 
 GENERAL INFORMATION
 -------------------
 The SHEBanG enrollment plugin is designed to import Luminis Message
 Broker (LMB) messages containing data sent from a SunGard HE Banner
 installation. The messages are POSTed by LMB to a specified URL;
 that URL should end up resolving to enrol/shebang/secure/post.php
 either by means of direct address, symbolic link, or Apache server
 configuration (e.g. alias). An alternate method of import is the
 file upload feature.

 There already is an enrolment plugin, LMB, developed by Eric Merrill
 <merrill@oakland.edu> that processes Banner data, and has many more
 configuration options and features. We have been using this plugin
 for several years at Appalachian State with only a few basic alter-
 ations.

 So why write a new one? Why reinvent? There were a few reasons.
 
 In our particular installtion of Banner/LMB, we kept getting several
 essentially identical messages which resulted in duplicate rows in
 one of the staging tables. Normally not a big problem, but eventually
 the staging table becomes bloated with duplicates. We also wanted to
 log each of the XML messages received, but not in the database.
 
 Putting them in a text file is more practical for us. Messages sent
 by the LMB are logged to the file-system in the Moodle data directory
 rather than the database; this message log file is locked while the
 message is being processed in order to serialize message processing.

 In the case where additional data from the XML message was needed, a
 regular expression had to be added--most often this is simple enough
 given an existing pattern from which to start, but in some instances
 where the message format changes ever so slightly (e.g. recstatus),
 it makes more sense to use DOMXPath queries to extract message data.

 XMLParser is used to break up the input, whether from file or posted
 XML, into the principle elements we want (<group>, <person>, and
 <membership>), and from that point use the DOMDocument and DOMXPath
 to query for the needed values.

 Some other differences:
   
 Messages imported from a file are not logged since there's already a
 source file;
 
 A cross-list course parent is created only when the first <membership>
 message for that cross-list arrives, and then it will be created by
 making a copy of the child course.
 
 The Moodle user's idnumber column is populated with the SCTID/Banner
 userid, rather than the Luminis sourcedid/id.
 
 The only cron activity is the monitor for last message arrival time.
 Some Moodle sites may have a very frequent cron activity (5-10 min)
 so it didn't seem practical to put dir/file check tasks in a common
 script--rather use a dedicated cron task, if needed at all, for the
 Banner extract/batch-file imports.
 
 
 INSTALLATION
 ------------
 Place the shebang directory in the site's enrol directory. Access the
 notifications page (Admin block->Notifications) to initiate database
 setup.
 
 Enable and configure the plugin, using the admin menu:
  Site Administration->Plugins->Enrolments->Manage enrol plugins
 
 Configure your Banner/LMB to POST messages to the URL corresponding
 to the enrol/shebang/secure/post.php file, for example:
 
   https://moodle.someuniversity.edu/enrol/shebang/secure/post.php
 
      * * * * * * * * *   I M P O R T A N T   * * * * * * * * *      

 THIS URL NEEDS TO BE SECURED IN SOME FASHION TO PREVENT UNAUTHORIZED
 USERS FROM INJECTING ERRANT DATA INTO YOUR DATABASE. THE METHODS FOR
 SECURING THIS URL INCLUDE, BUT ARE NOT LIMITED TO, APACHE HTTP AUTH
 CONFIGURED--IDEALLY WITH SSL--AT THE SERVER/VHOST/DIRECTORY LEVEL OR
 WITH AN .htaccess FILE. ANOTHER OPTION, IS TO USE THE AUTHENTICATION
 FEATURE IN THE MODULE, BUT AGAIN IT IS STRONGLY RECOMMENDED TO USE A
 SECURE SOCKET CONNECTION IN ADDITION TO HTTP BASIC OR DIGEST AUTHEN-
 TICATION SINCE EITHER THE USERID/PWD INFORMATION WILL BE EXPOSED
 WHEN SENT IN CLEAR-TEXT (BASIC) OR THE (DIGEST) HASH WILL BE EXPOSED.
 
 There is no Moodle authentication protection in the post.php since it
 is intended that only Banner LMB will be accessing this URL and a
 separate userid/password will be configured by the Moodle server admin
 and shared with the Luminis admins.


 CONSIDERATIONS FOR BLOCKING ISSUES
 ----------------------------------
 Because SHEBanG serializes message processing using a file lock, it
 is possible, in some cases likely, that a blocking bottleneck will
 take place when a large number of messages are sent by Banner/LMB at
 or near the same time. To mitigate the risk of the all the Apache
 worker processes being occupied and blocked, and thus freezing out
 the application end-users, consider setting up another Apache daemon
 to handle LMB messages exclusively on an alternate port such as 8080.
 In this way you can tailor the configured number of initial, max-min
 spare worker processes, etc., and if these processes should become 
 consumed, the end-users will not be impacted.
 
 
 HOW ENTITIES ARE ASSOICATED AND REFERENCED
 ------------------------------------------
 Courses:
 A staging record in the [mdl_enrol_shebang_section] table is inserted
 or updated, using the <sourcedid><id> value (the CRN) as the alternate
 key. This same value is placed in the [mdl_course](idnumber) column so
 that table can be queried directory. The course's (idnumber) value can
 not be changed after that or the association will be lost.
 
 Users:
 A staging record in the [mdl_enrol_shebang_person] table is inserted
 or updated, using the <sourcedid><id> value (the Luminis Id for that
 Banner identity, distinct from the Banner identity value). Susbsequent
 <membership> messages for this person will use this <sourcedid><id> so
 we need to use this as an alternate identifying key value. A <person>
 message also contains the <userid useridtype="SCTID"> value, the
 Banner identity, and it's this value that is placed in the [mdl_user]
 (idnumber) column.
 
 So, upon arrival of a <person> message, and after the staging record
 is handled, the [mdl_user] table is queried on the (idnumber) column
 for the SCTID value--if no record is found, then an attempt is made to
 insert one, otherwise the found record is updated. Because other
 columns in the [mdl_user] table are unique (enforced either by the
 application or the database), a collision on email or username by 
 prevent the insert. In such cases, manual inspection of the existing
 user account should be done to determine if it can be deleted (no 
 access, no enrolments, etc.) or if it should be associated with the
 corresponding [mdl_enrol_shebang_person] row by placing the appro-
 priate Banner identity value in the user's (idnumber) column.

 Until any such collision is resolved, course enrollments for that
 person/user will fail since no association exists between the person
 entity and the user entity.
