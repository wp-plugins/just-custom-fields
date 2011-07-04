=== Just Custom Fields ===
Contributors: aprokopenko
Tags: custom, fields, custom fields, term meta, meta, post meta, object meta, editor
Author URI: http://justcoded.com/
Author: Alexander Prokopenko
Requires at least: 3.0.0
Tested up to: 3.1.4
Donate link: http://justcoded.com/just-labs/just-custom-fields-for-wordpress-plugin/
Plugin URI: http://justcoded.com/just-labs/just-custom-fields-for-wordpress-plugin/
Version: 0.9beta
Stable tag: 0.9beta

This plugin add custom fields for standard and custom post types in WordPress.

== Description ==

This plugin add custom fields for standard and custom post types in WordPress. After installation you will see simple settings page which is self-explanatory to use.

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
This is first plugin version. You need just install it.

== Screenshots ==

1. Plugin settings page where you can manage custom fields
2. The edit post page meta box with fields created on settings page

== Changelog ==
* Version 1.0beta :
	* Added support for multi-language
	* Added RU translation
* Version 0.9beta :
	* First version beta
	
== Frequently Asked Questions ==
Q: Where I can edit new fields for Posts or Pages?
A: After installing and activating plugin you will see new menu option Settings > Just Custom Fields

Q: My site works slow, is it something wrong with your plugin?
A: Plugin is loaded only in Backend and create all objects only on Post edit pages and on the Settings page. So it can't affect the site.

Q: How can i add my own component (new field type)?
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