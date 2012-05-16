=== Path Access ===
Contributors: kevindees
Tags: members, access, path
Requires at least: 3.3
Tested up to: 3.3.2
Stable Tag: 1.0.1

This plugin gives you the ability to call the 404.php template file or your own custom template file for specific paths where a user is not logged in.

== Description ==

This plugin gives you the ability to call the 404.php template file or your own custom template file for specific paths where a user is not logged into WordPress and is not a specific role.

To use Path Access look under the "Settings" menu.

Your theme must have a 404.php file. However, if you want a more custom page you can create a file called access.php and add it to your theme. Users will be directed to that template instead, the aceess.php template will set your HTTP Response to 403.

In the User Interface, to restrict access to a page or child pages you must:

* Enter the path of that page
* Separate multiple paths with a return
* Include beginning and ending slashes "/"
* Add * to the end of a path to restrict all child pages.

You must have a 404.php template file or you can create your own template file and name it access.php. Some paths are blocked such as /wp-admin and /wp-login.php for your safety. You can also specify which roles may access the restricted pages by checking the boxes of the role you want to enable access for.


== Installation ==

Upload the path-access plugin to your blog, Activate it!

1, 2: You're done!

== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 1.0.1 =

* Set HTTP Headers
* Updated WordPress plugin page info

= 1.0 =

* Restrict Path Access by page and role