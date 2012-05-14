=== Path Access ===
Contributors: Kevin Dees
Tags: members, access, path
Requires at least: 3.3
Tested up to: 3.3.2
Stable Tag: 1.0

This plugin gives you the ability to call the 404 template file on specific paths where a user is not logged into WordPress.

== Description ==

This plugin gives you the ability to call the 404 template file on specific paths when a user is not logged into WordPress.

To use Path Access look under the Settings Menu.

== Usage ==

Your theme must have a 404.php file in order to work. However, if you want a more custom page you can create a file called access.php and users will be directed to that template instead.

To restrict access to a page or child pages you must enter path separated by a return. Separate paths with a return and include beginning and ending slashes "/". Add * to the end of a path to restrict all child pages. Again, you must have a 404.php template file or you can create your own template file and name it access.php. Some paths are blocked such as /wp-admin and /wp-login.php for your safety.

You can also specify which roles may access the restricted pages by checking the boxes of the role you want to enable access for.

== Installation ==

Upload the path-access plugin to your blog, Activate it!

1, 2: You're done!

== Screenshots ==

1. screenshot-1.png

== Changelog ==



= 1.0 =

* Restrict Path Access by page and role