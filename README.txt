=== Alert box for comments ===
Contributors: elfiiik
Tags: alert, box, responds, comments
Requires at least: 4.0
Tested up to: 4.9.6
Requires PHP: 5.0.0
Stable tag: trunk
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Plugin creates button, that opens box and shows logged users 3 latest responds on their comments. 

== Description ==
This plugin is for creating small button, that opens box and shows
3 latest responds on your comments. Only logged users can view those responds.
Admin can change positions of the button or the box through settings page.
Responds are stored into new table created by this plugin.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/Alert box for comments` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the \'Plugins\' screen in WordPress
3. Use the Alert Box settings in the admin menu to position your button

== Frequently Asked Questions ==
Q: Can there be more than 3 responds?
A: No, plugin is created to keep only 3 responds in your database to reduce size of your database.

Q: Can you redesign button, box, etc...?
A: Not yet, redesigning for admins will be implemented in the future.

Q: On which pages will the button appear?
A: On everyone.

== Screenshots ==
1. Description for the assets/screenshot-1.png. Settings page for plugin, where you can change positions of button or box. Values need to be in format \"10px\" to work..
2. Description for the assets/screenshot-2.png. Live demo from test page with 1 respond on comment.

== Changelog ==
= 1.1 =
* Fixing issues to meet requirements for plugin being published
* Renaming functions with bad prefixes
* Upgrading plugin security

= 1.0 =
* First working version of the plugin


== Upgrade Notice ==
= 1.1 =
This version upgrades overall functionality and security of plugin. Plugin upgrade highly suggested.