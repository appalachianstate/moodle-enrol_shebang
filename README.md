## SHEBanG enrol plugin for Moodle
This plugin is not an Ellucian product, and is neither endorsed nor supported by Ellucian.
 
### Description
The SHEBanG enrol plugin is used to import Luminis Message Broker (LMB) messages from Ellucian (formerly SunGard HE) Banner. Messages are POSTed by LMB to a specified target URL, which for this plugin would be _{hostname}/enrol/shebang/secure/post.php_. You can also import from an uploaded file.

There is already an enrolment plugin, [LMB](https://moodle.org/plugins/enrol_lmb), developed by [Eric Merrill](https://moodle.org/user/profile.php?id=139623) that processes Banner data, and has many more configuration options and features. We used Eric's plugin successfully for several years at Appalachian State with only a few basic alterations.

##### So why write a new one? Why reinvent? There were a few reasons.
 
In our particular installtion of Banner/LMB, we kept getting several identical messages in succession which resulted in duplicate rows in one of the staging tables. Normally not a big problem, but eventually the staging table becomes bloated with duplicates.

We also wanted to log each of the XML messages received, but not in the database; putting them in a log file is more practical for us. The message log file is locked while the message is being processed in order to serialize message processing, which helps with the previously mentioned problem of identical messages sent in succession.

In the case where additional data from the XML message was needed, a regular expression had to be added--most often this is simple enough given an existing pattern from which to start, but in some instances where the message format changes ever so slightly (e.g. recstatus), it makes more sense to use DOMXPath queries to extract message data.

XMLParser is used to break up the input, whether from file or posted XML, into the principal elements we want (`<group>`, `<person>`, and `<membership>`), and from that point use the DOMDocument and DOMXPath to query for the needed values.

##### Some other differences:
   
+ Messages imported from a file are not logged since the message source file is available for reference.

+ A cross-list course parent is created only when the first <membership> message for that cross-list arrives, and then it will be created by copying names and other information from the child course.
 
+ A cross-reference table is used to associate the Moodle user id and the Luminis id; this allows the IDNUMBER field to be populated with the SCT/Banner id, which our faculty prefer.
 
+ The only scheduled task activity is the monitor for last message arrival time.

### Installation & Configuration

Place the shebang directory in the site's enrol directory. Access the notifications page _(Settings block->Site administration->Notifications)_ to initiate database setup.
 
First configure and then enable the plugin _(Site administration->Plugins->Enrolments->Manage enrol plugins)_

Configure your LMB to POST messages to the URL corresponding to the _enrol/shebang/secure/post.php_ file, for example:

    https://moodle.someuniversity.edu/enrol/shebang/secure/post.php

__* * * * * * * * * * * * * * * * I M P O R T A N T * * * * * * * * * * * * * * * *__      

THIS URL NEEDS TO BE SECURED IN SOME FASHION TO PREVENT UNAUTHORIZED USERS FROM INJECTING DATA INTO YOUR DATABASE. THE METHODS FOR SECURING THIS URL INCLUDE, BUT ARE NOT LIMITED TO, APACHE HTTP AUTH CONFIGURED WITH SSL AT THE SERVER/VHOST/DIRECTORY LEVEL OR WITH AN .htaccess FILE. ANOTHER OPTION, IS TO USE THE AUTHENTICATION FEATURE IN THE PLUGIN, BUT AGAIN IT IS STRONGLY RECOMMENDED TO USE AN
SSL CONNECTION IN ADDITION TO HTTP BASIC OR DIGEST AUTHENTICATION SINCE EITHER THE USERID/PWD INFORMATION WILL BE EXPOSED WHEN SENT IN CLEAR-TEXT (BASIC) OR THE (DIGEST) HASH WILL BE EXPOSED.

REMEMBER TO DENY ALL ACCESS TO THAT DIRECTORY FOR ALL THE CONFIGURED HOSTS/VHOSTS, AND ALLOW ACCESS ONLY TO THE HOST(S) ON WHICH YOUR LMB IS RUNNING.

##### CONSIDERATIONS FOR BLOCKING ISSUES

Because SHEBanG serializes message processing using a file lock, it is possible, in some cases likely, that a blocking bottleneck will take place when a large number of messages are sent by Banner/LMB at or near the same time. To mitigate the risk of the all the Apache worker processes being occupied and blocked, and thus freezing out the application end-users, consider setting up another Apache daemon to handle LMB messages exclusively on an alternate port such as 8080. In this way you can tailor the configured number of initial, max-min spare worker processes, etc., and if these processes should become consumed, the end-users will not be impacted.
 
##### HOW ENTITIES ARE ASSOICATED AND REFERENCED
 
 Courses:
 
 A staging record in the [mdl_enrol_shebang_section] table is inserted or updated, using the <sourcedid><id> value (the CRN) as the alternate key. This same value is placed in the [mdl_course][idnumber] column so that table can be queried directory. The course's [idnumber] value can not be changed after that or the association will be lost.
 
 Users:
 
 A staging record in the [mdl_enrol_shebang_person] table is inserted or updated, using the `<sourcedid><id>` value (the Luminis id) as the key. Once a Moodle username is associated with that Luminis id, the Moodle user id is also stored in the staging record, making it a cross-reference. Subsequent `<membership>` messages for this person will use the Luminis id `<sourcedid><id>`. A <person> message also contains a `<userid useridtype="SCTID">` element, the Banner id. Either the Banner SCTID or the Luminis id can be placed in the [mdl_user][idnumber] column.
 
##### DISCLAIMER AND LICENSING

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version. 

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. 

You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.
