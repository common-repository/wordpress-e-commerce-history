=== WP-Ecommerce History ===
Contributors: anthonycole
Version: 1.0.1
Tags: wp-ecommerce, products
Tested up to: 3.1
Stable tag: /trunk/

WP-Ecommerce History is a plugin for the popular WP-Ecommerce plugin that allows users to view the products they have looked at in a sidebar widget.

== Description ==

By default, the widget generates an unordered list of permanent links to blog posts with the post title. The product listing ul has a class of `wpec-history`.

Coming in future versions is the following...

* Support for post thumbnails
* Support for logged out users
* Dashboard for tracking what products your users have looked at.
* Hooks and Filters (dependent on user feedback. I can't really see a need for them right now as the plugin is quite simplistic).
* Default styling.

== Installation ==

1. Make sure WP-Ecommerce is active. This plugin will not run unless WP-Ecommerce is installed.
2. Upload wpec-history.php to your plugins folder.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Navigate to Appearance -> Widgets in the WordPress admin. 

== FAQ ==

= Is widget styling provided? =
No, you have to style the unordered list yourself. 

== Changelog ==

= 1.0 =
* Initial Release

= 1.0.1 = 
* Make the plugin actually work. 
* BuddyPress Integration complete - a new activity item is now logged whenever you "view" a product.
* Fix up the README a little bit.