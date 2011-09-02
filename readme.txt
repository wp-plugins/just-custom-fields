=== Just Custom Fields ===
Contributors: aprokopenko
Plugin Name: Just Custom Fields for Wordpress
Plugin URI: http://justcoded.com/just-labs/just-custom-fields-for-wordpress-plugin/
Author: Alexander Prokopenko
Author URI: http://justcoded.com/
Tags: custom, fields, custom fields, meta, post meta, object meta, editor
Requires at least: 3.0.0
Tested up to: 3.2.1
Donate link: http://justcoded.com/just-labs/just-custom-fields-for-wordpress-plugin/
Stable tag: trunk

This plugin add custom fields for standard and custom post types in WordPress.

== Description ==

This plugin add custom fields for standard and custom post types in WordPress. After installation you will see simple settings page which is self-explanatory to use.

FILL FREE TO CONTACT ME IF YOU FIND ANY BUGS/ISSUES!

== Installation ==

1. Download, unzip and upload to your WordPress plugins directory
2. Activate the plugin within you WordPress Administration Backend
3. Go to Settings > Just Custom Fields
4. Choose Standard/Custom Post Type you want to edit custom fields
5. Create Fieldset
6. Add fields to the fieldset.

To use values from these fields in your theme, you can use usual post meta functions such as:

get_post_meta()
get_post_custom()

== Upgrade Notice ==
* Remove old plugin folder.
* Follow install steps 1-2. All settings will be saved.

== Screenshots ==

1. Plugin settings page where you can manage custom fields
2. The edit post page meta box with fields created on settings page

== Changelog ==
* Version 1.2:
	* Bug fix: Single checkbox uncheck problem
* Version 1.1.1:
	* Bug fix: Uploadmedia don't work if there are no main Content Editor field (for Custom Post Types)
* Version 1.1 :
	* Add feature to enable/disable fields without removing them
	* Add component "Related Content" (so you can add relation to another Custom Post Type with simple autocomplete field)
	* Bug fix: Randomly changing fieldset order after field update
	* Bug fix: Component css not loading without js
	* Improved css :)
* Version 1.0 :
	* Added support for multi-language
	* Added Russian translations
	* Added Italian translations (Thanks to Andrea Bersi for help with Italian version)
	* Fixed bug with blank screen on update post
	* Updated colors to match new Administrative UI in WordPress 3.2
* Version 0.9beta :
	* First version beta
	
== Frequently Asked Questions ==
= Q: Where I can edit new fields for Posts or Pages? =
A: After installing and activating plugin you will see new menu option Settings > Just Custom Fields

= Q: My site works slow, is it something wrong with your plugin? =
A: Plugin is loaded only in Backend and create all objects only on Post edit pages and on the Settings page. So it can't affect the site.

= Q: How can i add my own component (new field type)? =
A: first of all you need to create class for this field. You class should be extended from Just_Field main class!
You can copy class /[plugins-folder]/just-custom-fields/components/input-text.php to your plugin or theme and correct it:
- change name
- changle class methods to use your data.
Class structure is very similar to WordPress Widget classes.

Then you need to include your new component file.

And the last step:
- add new hook action "add_action('jcf_register_fields', 'my_register_fields_function')"
- create hook action function and call there:
jcf_field_register('YOUR_COMPONENT_CLASS_NAME');