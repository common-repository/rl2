=== rl2 ===
Contributors: rw26
Donate link: http://lrw.net/
Tags: resources, community
Requires at least: 3.0.1
Tested up to: 3.4.1
Stable tag: 0.6

The rl2 pluging collects and displays resources - that is names, addresses,
URLs and so on. it accepts user input without login.


== Description ==

The rl2 plugin collects and displays resources - that is names, addresses,
URLs and so on. It accepts user input without login and stores the
information in the database system. It uses a custom post type as well as
custom post meta and custom taxonomy (categories). A form can be supplied to
users to allow them to add to the resources database. Resources submited by
users are held in a non-visible state until the adminstrator marks them as
visible. The admin can edit the various fields and post content.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `rl2.php` to the `/wp-content/plugins/rl2/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `[rl2_resources]` shortcode in your post or page

== Frequently Asked Questions ==

= What does rl2 provide? =

rl2 is implements a system to list community resources. It displays
entries with or wothout a brick and mortar address, URL, email address,
or telephone number.

= Who can enter a resource? =
Any user, even one not logged in, can submit a resource using the form.
The user entered resource listings are not visible until approved by an
administrator. An admin screen is also provided that allows the input of new
resources and the editing of existing resources.

= How do I display the listings? =

Put the shortcode [rl2_resources] on a page or post. This shortcode can be
used with an 'rcat' attribute: [rl2_resources rcat="food"] An rcat is the
slug for a resource category.

Another shortcode, [rl2_user_form] can be used to display a user input form.

== Screenshots ==

1. Admin page, list of all resources.
2. Admin page, extra fields input area
3. Admin page, category editing.
4. User's resource submission form

== Changelog ==
= 0.6 = 
* This version allows comments and is tested with 3.4.1

= 0.5 =
* Initial entry - not stable, alpha code.

== Upgrade Notice ==

= 0.6 = 
This version allows comments and is tested with 3.4.1
