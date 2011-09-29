=== Limit Blogs Per User ===
Contributors:sbrajesh
Tags: buddypress,wpmu,multisite,wpms, options,limit blogs,user,limit
Requires at least: 2.5+
Tested up to: 3.2.1
Stable tag: 1.4

This plugin is for WordPress Multisite and/or WordPress Multisite+buddypress based social network.It limits the number of blogs a user can create.
== Description ==

This is a plugin for wpmu/wpmu+byddypress powered websites, where network administrators can limit the number of blogs a user can signup.
It is pretty simple and adds an option to NetworkAdmin->Dashboard->network settings page, where you can limit the number of blogs. No additional frills required.

What It does

It adds an option to the Network settings  page when you are logged in as site admin of the wpmu site(or wpmu+buddyppress site),Look at the bottom of options page,and You will see a text box like this asking for number of blogs allowed per user.If you set it to zero(which is the default),It will not restrict the blog registration then.

 == Installation ==

1. Download the plugin Limit Blogs Per Users 
2. Unzip the plugin and upload the file "limit-bogs-per-user.php"  to your wp-content/plugins  folder

3 Please login to the Wordpress backend as Network Administrator(site owner)

4.Go to NetworkAdmin-> Dashboard->Plugins->Installed page  and network activate "Limit Blogs Pre User".

5.Go to Network Admin->Dashboard->network settings and  navigate to the bottom,you will see an option to enter the number of blogs allowed,per 
user.Enter some limit and click update.

5.You are done!. Now it's time to for action.Enjoy the life.

== Frequently Asked Questions ==

Please leave a comment on my plugin page for support

[here](http://buddydev.com/buddypress/limit-blogs-per-user-plugin-for-wpmu/) for any questions.



== Screenshots ==
Please visit 

[http://buddydev.com/buddypress/limit-blogs-per-user-plugin-for-wpmu/](http://buddydev.com/buddypress/limit-blogs-per-user-plugin-for-wpmu/) 

== Changelog ==
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