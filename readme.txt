=== Organized Docs ===
Contributors: isabel104
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=me%40isabelcastillo%2ecom
Tags: documentation, docs, organize documentation, organized documentation, instruction guides, wiki
Requires at least: 3.7
Tested up to: 3.9
Stable Tag: 1.2.0
License: GNU Version 2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily create organized documentation for multiple products, organized by product and by subsections within each product.

== Description ==

Create documentation for multiple items, for example, for multiple software products. This is for you if you need to create documentation for multiple products, and must have the docs organized neatly, by product. The main "Docs Page" will list all the products. Clicking on a product will take you to the docs only for that product.

Each product's docs is further organized into subsections. The subsections list each individual article in that docs section. 

The single docs posts will have a Docs Table of Contents widget added to the sidebar (only if your theme has a dynamic sidebar). This will show a nice Table of Contents for docs pertaining only to the product which this current single doc belongs to. 

Demo [Organized Docs on Twenty Thirteen](http://smartestthemes.com/organized-docs-wordpress-plugin/docs/) theme

Demo [Organized Docs on Twenty Twelve](http://smartestthemes.com/organized-docs-on-twentytwelve/docs/) theme

[Setup Instructions](http://isabelcastillo.com/docs/first-steps-after-installing)

[Documentation](http://isabelcastillo.com/docs/category/organized-docs-wordpress-plugin)

For support or to report bugs, use the support forum link above, or use [GitHub](https://github.com/isabelc/Organized-Docs). Forking welcome.

== Installation ==

1.  Download the plugin file (`.zip`).
2.  In your WordPress dashboard, go to "Plugins -> Add New", and click "Upload".
3.  Upload the plugin file and activate it.
4.  In your WordPress dashboard, go to **"Appearance -> Widgets"**.
5.  Drag the "**Organized Docs Section Contents**" widget to the "**Docs Widget Area**". If you change your WordPress theme, this step must be done again.
6.  See [First Steps After Installing](http://isabelcastillo.com/docs/first-steps-after-installing)
== Frequently Asked Questions ==

= Why does the Table of Contents widget appear multiple times on the same page? =

This does not happen with the default WordPress themes. For a custom solution for your theme, please see:

[Table Of Contents Widget Appears Multiple Times](http://isabelcastillo.com/docs/table-of-contents-widget-appears-multiple-times)


== Screenshots ==
1. Back-end Admin screen – Organized Docs WordPress Plugin
2. All Docs For 1 Product
3. Single docs post with Table of Contents

== Changelog ==

= 1.2.0 =
* New: option to hide custom sidebar IDs to avoid multiple Table of Contents widgets.
* New: option to hide printer icon and print link.
* New: Navigate throught next and previous docs. If you are not using Twenty Fourteen theme, call twentyfourteen_post_nav() in your single.php to use this feature.
* Tweak: better styling and padding for compatibility with Twenty Fourteen theme. 
* Maintenance: updated .pot file.

= 1.1.10 =
* New: added .pot file for localization.
* Maintenance: reset option to update all docs posts with default sort order.
* Maintenance: removed warning from array_combine when docs category terms are missing.

= 1.1.9 =
* New: option to delete all Docs data upon uninstall.
* Fix: 1 line which caused posts to save order number as 99999 and ignore the custom number.

= 1.1.8 =
* Fix: Typo in function name stopped script from assigning default sort-order to existing posts.

= 1.1.7 =
* Fix: Docs would not display if custom sort-order was not set. Now, it is not necessary to set a custom sort oder. It remains optional.
* Maintenance: removed script to give subheadings a default order since no longer needed.

= 1.1.6.1 =
Bug fix: Custom sort-order for Categories was not saving.

= 1.1.6 =
* Fix: give default order to sub-headings. PLEASE UPDATE NOW.

= 1.1.5 =
* New: added sort order for Top-level Doc Items, and for Sub-headings, and for individual Doc articles.
* New: uninstall.php to remove docs, taxonomies, and custom term options upon uninstall.
* Fix: PHP error notices on admin Parent column if no category was assigned.
* Tweak: removed generator tag for less markup.
* Tweak: added links to 2 demos in the description.
* Maintenance: updated plugin URL in readme.
* Maintenance: tested and passed for WP 3.9 compatibility.
* Maintenance: Removed CSS styling for docs-template for better compatibility with Twenty Fourteen Theme. See http://isabelcastillo.com/docs/page-is-too-wide-and-not-centered-since-updating-to-1-1-5

= 1.1.4 =
* Maintenance: removed unused icons.

= 1.1.3 =
* Bug fix: Table of Contents Widget was showing up 3 times on Twenty Fourteen theme.
* New: added Print button to single docs.
* New: added link to Setup Instructions.
* Maintenance: added support for Twenty Fourteen Theme.
* Maintenance: updated the icon for Docs.
* Maintenance: removed PHP notices and errors.

= 1.1.2 =
* Bug fix: Main Docs page query was broken.
* Tested for WP 3.8 compatibility.

= 1.1 =
* Bug fix: slug for docs category taxonomy was broken.
* Tested for WP 3.7.1 compatibility.

= 1.0 =
* Initial release.

== Upgrade Notice ==
= 1.2.0 =
New: option to hide custom sidebar IDs to avoid multiple Table of Contents widgets.

= 1.1.9 =
Fix 1 line which caused posts to save order number as 99999 and ignore the custom number.

= 1.1.8 =
Fix: Typo in function name stopped script from assigning default sort-order to existing posts.

= 1.1.7 =
Fix: Docs would not display if sort-order was not set. Now, it is not necessary to set custom sort oder. 

= 1.1.6.1 =
Bug fix: Custom sort-order for Categories was not saving.

= 1.1.6 =
Fix: Give default order to sub-headings. New: set custom sort order.

= 1.1.5 =
New: set custom sort order for Top-level Doc Items, Sub-headings, and individual articles.

= 1.1.4 =
Bug fix: Table of Contents Widget was showing up 3 times on Twenty Fourteen theme.