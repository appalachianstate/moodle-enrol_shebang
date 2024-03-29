##### 2023053000:0.1.9-41 - 2023-05-30

Update RELEASE docs.

Add config for updating SCTID/idnumber.

##### 2023032800:0.1.8-41 - 2023-03-28

Update request headers for latest Luminis version.

No functional change; review create_course compatibility with 3.10.x.

Fix constant in insert_course.

##### 2019052007:0.1.7-36 - 2019-01-28

Branched for Moodle 3.6, remove deprecated coursecat calls, use core_course_category

##### 2014052006:0.1.6-30 - 2018-09-19

Update RELEASE and README docs.

Remove deprecated PHP function each().

Bug fix in scheduled task error handler; remove old strings.

##### 2014052005:0.1.5-30 - 2018-04-11

Add config for course term filtering

##### 2014052004:0.1.4-30 - 2018-01-11

Remove merge course (in favor of metacourse), fix metacourse grouping feature,
refactor cron task (monitoring) to scheduled task.

##### 2014052003:0.1.3-30 - 2016-07-07

Add config for name preference, i.e. nickname

##### 2014052002:0.1.2-30 - 2016-01-15

Adjust order of lib calls in create_course to match course/lib.php, remove enum
attributes in install.xml, put more info in exceptions.

##### 2014052000:0.1.1 - 2014-05-20

Add config to allow enroll (membership) messages with role status 0 (inactive)
to be treated the same as role with recstatus 3 (delete). With this config on,
reverts to 0.0.9 behavior.

Require either JMSMessageId or ldisp_id instead of both. Apache 2.4 will remove
the ldisp_id header (underscores not allowed in header names), so value will be
lost.

Remove calls to deprecated get_context_instance(), and mark_context_dirty().

Handle case where configured logdir is invalid, fallback to default rather than
puke an error.

Respect the moodle/course:changeidnumber capability when adding/editing enrol
instances.

##### 2013080501:0.1.0 - 2013-10-08

Reorganize code to separate message processing from the enrol plugin class, and
place into locallib.php.

Distinguish between status and recstatus when processing membership messages so
that a status of 0 will suspend user's enrollment in a class, and a recstatus
value of 3 (delete) will remove the enrollment. This behavior extends to group
membership (cross-listed courses).

Fix plugin to support deletion of enrollments by editingteacher, manager roles.
Also provide for addition of enrol instance to course with prompt for course's
idnumber. Add hooks for validation of course's idnumber when editing course
settings.

Fix breadcrumb navbar in admin. tools.

##### 2013080500:0.0.9 - 2013-08-05

CONTRIB-4472 Move all file I/O out of the plugin class' constructor, and remove
any 'die' calls from constructor to eliminate wsod risks. Add ability to return
either HTTP 200 or 500 on message processing failures. Add notification feature
for message processing failures. Fix parsing of admin email addresses-commas or
semicolon separators. Update Moodle username from LMB messages. Add awareness
of auth_shibbuncif (Shibboleth) plugin to handle formatting of username@domain
usernames. Modify post.php to indicate no Moodle cookies expected, i.e. session
not needed. Establish branches to correspond to lib changes in Moodle 2.2, 2,4,
and 2.5

##### 2012062500:0.0.8 - 2013-07-09

CONTRIB-4471 Check configured log directory path exists and writable and fall
back to the default directory if configured path is invalid.

##### 2012062500:0.0.7 - 2013-07-08

This release is a roll-up of unpublished (on moodle.org) previous releases. It
addresses CONTRIB-4466, CONTRIB-4467, and CONTRIB-4468, as well as an
enhancement and general code cleanup.

CONTRIB-4466: Corrected the logging so when DML exceptions occurred inserting/
updating Moodle user records, it was correctly reflected in the process and
message logs.

CONTRIB-4467: Fixed the monitoring feature which was altogether broken since the
2x refactoring.

CONTRIB-4468: Corrected the behavior when associating an LDI person with a
Moodle user to check for users marked as deleted. If current association is with
a user marked as deleted, a new Moodle user record will be created.

Added configuration setting for an alternate logging directory, useful for
locating logs outside of the Moodle data directory.

IMPORTANT: The "requires" Moodle version was revised down to 2011120510 after
testing the plugin in v. 2.2.10 (MOODLE_22_STABLE).
