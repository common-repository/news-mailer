=== News Mailer ===
Contributors: edvind
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EXQYJ7PM2R9RU
Tags: mail, newsletter, news, email, mass, notification, notify, update, swiftmailer
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 0.5.4

Simple plugin for mailing news or updates to members registered on the blog.

== Description ==

News Mailer is a plugin for mailing news or updates to members registered on the blog. It uses the powerful component based [Swiftmailer](http://swiftmailer.org/ "Swiftmailer") library.


In order to use the plugin properly you'll need to edit the template files:

* templates/template.html
* templates/template.txt


It's extremely important for you to also configure the SMTP settings.


Features:

* Mail to specified role groups
* SMTP, Sendmail and mail()
* SMTP Authentification
* UTF-8 (Eg. proper support for åäö characters)
* HTML and Plain text mail body (use both or just one of them)
* Templates
* User details can be included in template
* AntiFlood feature


Bug reports, feedback and feature requests is thankfully received! Either in the [forums](http://wordpress.org/tags/news-mailer?forum_id=10#postform) or on the [News Mailer plugin homepage](http://edvindev.wordpress.com/news-mailer/).


If you want to translate the plugin feel free to do it, the .pot-file is included in the 'lang' directory. Send the translation to me and I'll add it in the next release. For more information on translating (i18n), go to http://codex.wordpress.org/Translating_WordPress

When upgrading, don't forget to backup your custom template files!

== Installation ==

1. Upload the `news-mailer` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set up the plugin under Settings - Mail
4. Let the news spread

== Frequently Asked Questions ==

= What is News Mailer? =

A plugin for sending newsletters to registered users on your blog.

= Where do I configure the plugin? =

A section named "Mail" under Settings is added.

= How do I configure the layout of the mails? =

You'll need to edit the template files (HTML/Plain):

* templates/template.html
* templates/template.txt

The following markup can be used within the templates: %TITLE%, %BLOGNAME%, %DATE%, %ARTICLES%

User specific details availible:, {ID}, {user_login}, {user_email}, {user_url}, {user_registered}, {user_registered_date}, {display_name}, {first_name}, {last_name}, {nickname}, {description}

= Why does my newsletters fail to be delivered? =

That's a tricky question with a load of potential answers. Sending massive amounts of mail without a paid service can be an advanced task that requires a lot of information and testing from you.

First check your settings, make sure SMTP is configured correctly according to the details provided by your SMTP host. Often you can get details about how many mails that can be sent per hour etc. by asking politely :)

If the first, lets say, 40 recipents receives the newsletter and the rest doesn't, check if your webserver (not the SMTP host) has a maximum expiration period for php scripts and, if possible, change it to something higher. Lowering the number of mails sent per connection and expanding the period between connections can also work if used with the wp-cron AntiFlood feature.

== Screenshots ==

1. Send mail page
2. News Mailer settings page

== Changelog ==

= 0.5.4 =
* Fixed: The "Wrong parameter count for nl2br()" bug for those using pre 5.3.0 php installations

= 0.5.3 =
* Added: Ability to set mail format in settings to both plain/html, plain only and html only
* Added: Settings link to plugin list entry
* Fixed: Encryption over SMTP
* Fixed: Bug when saving settings and not defining smtp password
* Fixed: Total deletion of all settings and tables when uninstalling plugin
* Fixed: Proper auto generated line breaks from text input
* Fixed: Line breaks in preambles
* Fixed: WordPress mail transport error message

= 0.5.2 =
* Fixed: HTML-links within the mail now works properly
* Fixed: Some minor formatting issues

= 0.5.1 =
* Fixed: Small bug with stream_get_transports() function error message
* Fixed: Removed event setting from displaying since it's not supported yet

= 0.5 =
* Added: wp_cron function to send mails
* Added: Welcome message

= 0.4.2 =
* Added: "Potential issues"-section on the options page
* Fixed: Two database entries missing in the uninstall deletion process
* Some new localized text, updated .pot file

= 0.4.1 =
* Fixed: Small change in antiflood settings

= 0.4 =
* Added: Proper options page on admin panel
* Added: Complete deletion of database entries when uninstalling
* Added: AntiFlood feature to prevent SMTP servers to think your newsletters are spam
* Added: Ability to write two separate "articles" in a mail
* Fixed: Templates are now ready for public use and modification
* Extensive change/cleanup in overall code

= 0.3.3 beta =
* Added: 3 transport modes: SMTP, Sendmail and mail() configurable on the settings page
* Added: Check if transport is set on mail page
* Added: Option to choose if upcoming events should be a part of the mail or not
* Fixed: Serious bug where templates could not be found due to hardcoded path

= 0.3.2 beta =
* Fixed: Reciever mail variable

= 0.3.1 beta =
* Language fixes

= 0.3 beta =
* Added: Individualized user information in mail
* Misc fixes and code cleanup

= 0.2.1 alpha =
* Added: Swedish translation
* Fixed: Minor language problem

= 0.2 alpha =
* Added: Send mail to specified role groups
* Added: l18n implementation.
* Added: English translation.
* Added: Users can now change SMTP Settings on the general settings page
* Added: Integration with Events Manager Extended
* Fixed: Issue with Swiftmailer settings
* Misc fixes and code cleanup

= 0.1 alpha =
* Initial alpha release

== Upgrade Notice ==

= 0.5.4 =
Remember to back up your template files!

= 0.5.3 =
Remember to back up your template files!

= 0.5.1 =
Remember to back up your template files!

= 0.4 =
Because of the redesign of the plugin most of the swedish translation disappeared. This is to be fixed in a future release when things are more stable and all language strings are defined.

= 0.3.3 beta =
More transport types supported.
Serious template related bug fixed.
The language files should now work as expected.

= 0.3.1 beta =
Language fixes.

= 0.3 beta =
This version is the first beta version. The plugin works pretty well but you'll have to edit the template to suit your needs. See the FAQ for more information on this. 

= 0.2 alpha =
This version is an alpha version and does not work properly.

= 0.1 alpha =
This version is an alpha version and does not work properly.