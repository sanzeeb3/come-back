=== Come Back! ===
Contributors: sanzeeb3
Tags: inactive, idle-users, notification
Requires at least: 5.0
Tested up to: 5.7
Requires PHP: 5.6
Stable tag: 1.3.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Send rearrangement emails to inactive customers. Bring them back.

== Description ==

Come Back! sends an automatic email notification to inactive customers to bring your **LOST** customers back. Inactivity is based on logging in.

Create special re-engagement emails targeted directly at your “inactive” customers. Customer inactivity is a fact of life. How you handle it makes all the difference.

**Sample Email:**

*We haven't seen you in a while. Things are a lot different since the last time you logged into {site_name}. I'm {name}, CEO of {site_name}. I wanted to send you a note since you have been inactive for a while. You can come back and continue your awesome works at {site_name}.*

*Login*

*Or, if you’d rather not hear from us again, please click here to unsubscribe - no hard feelings!*

== Screenshots ==

1. Settings Page

== Frequently Asked Questions ==

= How can I test the plugin?

To verify the emails are actually sent, go to the Settings > Come Back! and enter 0 in the 'Send emails to user after inactive days' and then go to wp-admin/tools.php?page=action-scheduler, search for pending action 'cb_schedule_notification' and run. This will sent the email to all the users.

= How can I add image in the email message?

You can switch to 'Text' format in Tinymce editor in the settings and choose 'img' button. You'll need to enter the URL of the image and description of the image. That's it.

= Can I customize the email template?

Yes. You can follow the [documentation](http://sanjeebaryal.com.np/come-back-template-structure/).

== Changelog ==

= 1.0.0 - 12/09/2020 =
* Initial Release

= 1.1.0 - 12/10/2020 =
* Fix - The functionality.

= 1.1.1 - 12/13/2020 =
* Fix - HTML emails.
* Fix - Respect tinymce spaces and linebreaks.

= 1.2.0 - 12/18/2020 =
* Add - Shortcuts support in email message.
* Add - Image support in email message.

= 1.3.0 - 12/24/2020 =
* Add - More shortcuts support.
* Add - Email template support.

= 1.3.1 - 12/25/2020 =
* Feature - Send sample email.
* Fix - Preview background color.

= 1.3.2 - 03/09/2021 =
* Fix - include_once does work/run on loop.