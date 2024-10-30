=== Mass Page Maker ===
Contributors: wesg
Tags: pages, posts, multiple, automate, create
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 2.8

Automate the insertion of multiple pages or posts.

== Description ==

From a simple admin panel, Mass Page Maker makes it easy to create multiple pages or posts. This is dramatically faster than doing it manually. Options are available to customize every aspect of the page -- from the date to page excerpt.

Feedback is greatly appreciated, whether the plugin works or not.

**Due to plugin repository policies, this version of the plugin has now been limited to 10 posts at a time using only the web interface. To use CSV files again, please <a href="http://www.wesg.ca/wordpress-plugins/mass-page-maker-pro/" target="_blank">purchase Mass Page Maker Pro</a>.**

For a complete list of the changes from each version, please visit <a href="http://www.wesg.ca/2008/06/wordpress-plugin-mass-page-maker/#changelog">the plugin homepage</a>.

For examples and tips on using the plugin, please check <a href="http://www.wesg.ca/2008/06/wordpress-plugin-mass-page-maker/#usage">the examples</a> on the plugin homepage.

= Acknowledgements =

*	<a href="http://www.jquery.com" target="_blank">jQuery</a>
*	<a href="http://www.jqueryui.com" target="_blank">jQueryUI</a>
*	<a href="http://trentrichardson.com/examples/timepicker/" target="_blank">Timepicker jQuery Plugin</a>

= Contact =
*	<a href="http://twitter.com/wesgood" target="_blank">Twitter</a>
*	<a href="http://www.wesg.ca/contact" target="_blank">Contact form</a>

= Usage =

1. After activating the plugin, navigate to the admin panel interface, where the options can be entered to insert the required number of pages or posts.
1. The web interface inserts all content the same way. For unique pages, use a .CSV file.
1. Enter the post date according to your WP timezone; adjustments are made automatically.
1. Post dates that are in the future will published on their entered date.
1. To insert custom fields, separate the fields with semicolons. All new posts are given each custom field.
1. Use the placeholders [blog\_title], [blog\_description] or [blog\_url] to add their respective data.

= Caution =
*This plugin is extremely powerful. A slip of the mouse can cause your blog to insert many more pages than you intended, which can take a lot of time to delete. Read the options carefully before inserting pages.*

== Installation ==

1. Copy the folder mass-page-maker into your WordPress plugins directory (wp-content/plugins).
1. Log in to WordPress Admin. Go to the Plugins page and click Activate.
1. Navigate to the Admin Panel for Mass Page Maker (under Settings).

== Frequently Asked Questions ==

= What is the purpose of this plugin? =

Mass Page Maker automates the task of making pages or posts that are similar to each other. This can be a very time consuming process that can now be done in a matter of seconds. The ability to rapidly generate pages and posts has the potential to turn Wordpress from an excellent blog platform to an even better CMS.

= What options are available? =

In the interface panel, you have the ability to customize the page insertion.
You can change:

*	 Number of pages
*	 Starting number of page
*	 Post title
*	 Post content
*	 Post status (published or draft)
*	 Post type
*	 Post category
*	 Post excerpt
*	 Post date
*	 Interval between pages
*	 Page template
*	 Custom fields
*	 Post password
*	 Post visibility
*	 Sticky post

== Screenshots ==

1. The admin panel interface.

== Changelog ==
**2.8** -- February 28, 2013

*	 Full update to web interface
*	 Modifications to fit repository

**2.7** -- March 10, 2012

*	 Major overhaul for WP 3.3.1
*	 Uses WordPress native functions
*	 Provides immediate progress feedback during CSV import

**2.6.7** -- July 19, 2010

*	 Rewritten for WP 3.0
*	 Improved the reliability of the CSV and web interface
*	 Fixed tags in CSVs

**2.6.6** -- December 17, 2009

*	 Added support for page orders
*	 Improved handling of escaped characters in CSV and web interface
*	 Various other bug fixes

**2.6.4** -- August 20, 2009

*	 Repaired category support

**2.6.3** -- August 10, 2009

*	 Cleaned page insertion problem
*	 Repaired template issues

**2.6.2** -- August 9, 2009

*	 Fixed quotation handling

**2.6.1** -- August 9, 2009

*	 Improved quotation handling
*	 Added page visibility options

**2.6** -- July 23, 2009

*	 Fixed WP 2.8.x compatibility

**2.5.9** -- July 22, 2009

*	 Added troubleshooting information

**2.5.8** -- June 28, 2009

*	 Improved support for WP 2.8
*	 Added tag support
*	 Better compatibility with CSV files

**2.5.7** -- May 17, 2009

*	 Added better compatibility and feedback with CSV files

**2.5.6** -- April 30, 2009

*	 Added placeholder support
*	 Added better troubleshooting feedback for CSV files

**2.5.5** -- April 6, 2009

*	 fixed increment tag in page content and excerpt

**2.5.4** -- March 21, 2009

*	 Made custom field work in each way
*	 Add German translation

**2.5.3** -- March 18, 2009

*	 Added custom field support

*	**2.5.2** -- March 15, 2009

*	 Finally fixed future posting cron errors

**2.5.1** -- March 14, 2009

*	 Corrected some type wrong in v2.5

**2.5** -- March 14, 2009

*	 Corrected cron behaviour with future posts
*	 Added page template option
*	 Add CSV file imports

**2.1** -- February 8, 2009

*	 fixed what should have worked in 2.0

**2.0** -- February 6, 2009

*	 Overhauled date and time system
*	 Added page excerpt data
*	 Added ability to insert posts in the future or the past

**1.5** -- January 18, 2009

*	 Added ability to create pages with different content

**1.5** -- January 18, 2009

*	 Added ability to create pages with different content

**1.4** -- January 6, 2009

*	 Updated for WordPress 2.7
*	 Completed localization capability

**1.3** -- November 1, 2008

*	 Enabled page parents
*	 Made the plugin compatible with Wordpress internationalization

**1.2** -- September 2, 2008

*	 Enabled adding categories to posts
*	 Optimized code

**1.1.1** -- July 28, 2008

*	 Cleaned up code and readme file

**1.1** -- June 19, 2008

*	 Added support for multiple page titles
*	 Built fault tolerance in
*	 Added new ways to enter pages faster
*	 Posts and pages can how have comments and pings that are open or closed

**1.0** -- June 18, 2008

*	 Initial release