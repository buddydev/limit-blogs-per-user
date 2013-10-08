=== Limit Blogs Per User ===
Contributors:sbrajesh,unknowndomain
Tags: buddypress,wpmu,multisite,wpms, options,limit blogs,user,limit
Requires at least: 2.5+
Tested up to: 3.2.1
Stable tag: 1.5

This plugin is for WordPress Multisite and/or WordPress Multisite+BuddyPress based social network. It limits the number of blogs a user can create.

== Description ==
This plugin is for WordPress Multisite and/or WordPress Multisite+BuddyPress based social network. It limits the number of blogs a user can create.

It is pretty simple and adds an option to Network Admin > Dashboard > Network Settings page, where you can limit the number of blogs. No additional frills required.

What It does

It adds an option to the Network Settings page when you are logged in as Network Administrator of the WordPress Multisite. Look at the bottom of options page, and you will see a text box asking for number of blogs allowed per user. If you set it to zero which, is the default, it will not restrict the number of signups.

== Installation ==

1. Download the plugin Limit Blogs Per Users 
2. Unzip the plugin and upload the file "limit-bogs-per-user.php"  to your wp-content/plugins folder
3. Please login to the Wordpress backend as Network Administrator(site owner)
4. Go to Network Admin > Dashboard > Plugins > Installed page and Network Activate "Limit Blogs Pre User".
5. Go to Network Admin > Dashboard > Network Settings and navigate to the bottom, you will see an option to enter the number of blogs allowed, per user. Enter the limit and click update.

== Frequently Asked Questions ==

Please leave a comment on my plugin page for support

[here](http://buddydev.com/buddypress/limit-blogs-per-user-plugin-for-wpmu/) for any questions.

== Screenshots ==
Please visit [http://buddydev.com/buddypress/limit-blogs-per-user-plugin-for-wpmu/](http://buddydev.com/buddypress/limit-blogs-per-user-plugin-for-wpmu/) 

== Changelog ==
= Version 1.5 =
 * Fixes security issue
 * Tidies up code
 * Adds support for a pre-defined `LBPU_BLOG_LIMIT` in wp-config.php `define( 'LBPU_BLOG_LIMIT', 1 );`
= Version 1.4 =
 *updated to work with wordpress 3.2.1 multisite and BuddyPress 1.5
 * rewritten code to use a singleton class
= Version 1.3 =
 *updated to work with wordpress 3.0 multisite
= Version 1.3 =
 *Fixed a bug with Buddypress 1.2.3 compatibility
= Version 1.2 =
 *Siteadmin restriction fixed,now site admin sees only the site policy
 *Subscribers can creates new blog,the blog of which they are subscriber/contributor/author/editor is not counted.
 * Only the blogs for which a user is admin is counted in user blogs  
 *If value is set Zero,It does not change site policies 
= Version 1.1 =
Some Breaking and then reverting back
= Version 1.1.1 =
 *A silly mistake in version 1.1($wpdb was not declared global) resolved.Many thanks to Steph for pointing the issue.

== Other Notes ==

For any support or any questions,please leave a comment at my blog 

[http://buddydev.com/buddypress/limit-blogs-per-user-plugin-for-wpmu/](http://buddydev.com/buddypress/limit-blogs-per-user-plugin-for-wpmu/)
And yeh did I mention [Steph](http://blog.strategy11.com/) has been very kind in reporting bugs and suggesting fixes.Many Thanks to Steph.