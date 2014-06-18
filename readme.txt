=== Wordpress Picture / Portfolio / Media Gallery ===
Contributors: nimble3,mamirulamin
Donate link: http://www.nimble3.com
Tags: picture, portfolio, gallery, picture gallery, media gallery, filterable portfolio, filterable gallery, jquery portfolio, sortable portfolio, skin based portfolio, prettyphoto, lightbox, prettyphoto lightbox, media gallery, prettyphoto gallery, lightbox gallery, responsive portfolio, responsive picture gallery, responsive media gallery, responsive design
Requires at least: 3.5
Tested up to: 3.9
Stable tag: 2.0.0
License: GPLv2 or later

A powerful portfolio/gallery plugin, highly suitable to showcase your portfolio/pictures/videos/media and sort them nicely under filterable tabs.
== Description ==

<h3>Nimble Portfolio</h3>

This free plugin can transform a humble wordpress website into a feature rich media gallery where you can proudly showcase your projects, client logos, photography, or any other pictures or videos of your choice. You can group the contents of your media gallery using the built-in jQuery sort filters and display them on any theme of your choice. It comes with built-in PrettyPhoto lighbox but can also be customized easily by using other add-ons. This plugin is also responsive which means it would work perfectly on desktop, tablet and mobile screen sizes.  If you read through our documentation we have provided step by step instructions to use the various features and functionality of this plugin.

= Plugin Features =

1. Custom post types for portfolio items.
2. Youtube, Vimeo, Quicktime video support.
3. Built-in PrettyPhoto gallery for picture, video and <strong>PDF</strong> preview.
4. Easy categorization and sort/filter feature.
5. Configurable default skin, with 3 Responsive designs. (Version 2)
6. Widget enabled. (Version 2)

= Version 2 Released =

Whats New in Version 2

- More of a  Framework than a Plugin
     1. Developer/Designers can make additional Add-ons to extend features such as:
        * Skins
        * Lightbox galleries (like Swipebox, Fancybox)
        * Sorting/Filtering libraries (like Isotope).

- Default Skin Features
     1. Fully Responsive Default Skin for Desktop, Tablet and Mobile.
     2. 3 additional Skin Styles Included (Normal, Round and Square).
     3. Default Skin can be fully customized to set Columns, Show/Hide Links.

= Quick User Guide =

1) Add portfolio filter under `Nimble Portfolio -> Filters`, such as web, mobile, graphics e.t.c.<br />
2) Add new portfolio items using custom post type under `Nimble Portfolio -> Add Portfolio Item`. Add item title, description e.t.c.<br />
3) Upload and set featured image from the far right bottom box.<br />
4) Specify full-size Image URL or Video URL (youtube, vimeo) in the input field `Image/Video URL` on the left. You can also use `URL from Media Library` button to select the URL of full-size image from Media Library.<br />
5) Specify a live URL for your project in the input field `Portfolio URL`.<br />

= Step by Step User Guide =

Coming Soon!

= Shortcode =

`[nimble-portfolio]` 

<em>TinyMCE editor button ([screenshot](http://s.w.org/plugins/nimble-portfolio/screenshot-1.jpg)) is providing convenience to insert shortcode of our plugin on page editor.</em>

= PHP Code =

`echo nimble_portfolio()` 
or
`nimble_portfolio_show()`

= Demo =

http://nimble3.com/demo/nimble-portfolio-free/

= Add-ons =

Coming Soon!

= Upgrade Notice =

When upgrading from version 1 to version 2, your current plugin skin (template) will be replaced by new default skin, there is no going back to your old plugin skin. But who cares when your old fixed style skin is replaced by modern responsive design :)

== Installation ==

= Minimum Requirements =

* WordPress 3.5 onwards
* PHP 5.3 onwards
* MySQL 5.0 onwards

No extra ordinary step to install this plugin, use following generic plugin installation guide

http://codex.wordpress.org/Managing_Plugins#Installing_Plugins

== Frequently Asked Questions ==

Please use Support tab OR use this link http://wordpress.org/support/plugin/nimble-portfolio

== Screenshots ==

1. Nimble Portfolio - TinyMCE button for shortcode generation
2. Nimble Portfolio - Widget
3. Nimble Portfolio - Options meta box on Add New Item page
4. Nimble Portfolio - Items listing
5. Nimble Portfolio - Frontend Display
6. Nimble Portfolio - Picture in lightbox
7. Nimble Portfolio - Video in lightbox

== Changelog ==

When upgrading from version 1 to version 2, your current plugin skin (template) will be replaced by new default skin, there is no going back to your old plugin skin. But who cares when your old fixed style skin is replaced by modern responsive design :)

= 16 Jun 2014 =

"Version 2.0.0 Released!"

What's New in Version 2
----------------------
- More of a  Framework than a Plugin
     1. Developer/Designers can make additional Add-ons to extend features such as:
        a. Skins
        b. Lightbox galleries (like Swipebox, Fancybox)
        c. Sorting/Filtering libraries (like IsoTope).

- Default Skin Features
     1. Fully Responsive Default Skin for Desktop, Tablet and Mobile.
     2. 3 additional Skin Styles Included (Normal, Round and Square).
     3. Default Skin can be fully customized to set Columns, Show/Hide Links.

= 09 Sep 2013 =

1.3.2<br /><br />
rect-1 template CSS fix

= 09 Sep 2013 =

1.3.1<br /><br />
1. PDF support for lightbox gallery.<br />
2. New Template variation for Round template. template code: 'round-2' <br />
3. New Template variation for Rectangular template. template code: 'rect-2' <br />

= 05 Aug 2013 =

1.3.0<br /><br />
1. New Template. template code: 'round-1' <br />
2. New Template. template code: 'rect-1' <br />
3. Better thumbnail generation.<br />
4. flush_rewrite_rules() on plugin activation to take care of 404 error on single portfolio post.<br />
5. Taxonomy slug changed to 'portfolio-type'<br />

= 06 May 2013 =

1.2.5<br /><br />
1. Added `URL from Media Library` button to select full size Image URL much easier from your site's Media Library.<br />

= 05 Mar 2013 =

1.2.4<br /><br />
1. Fixed - Warning: Illegal string offset 'template' issue (http://wordpress.org/support/topic/issues-5)<br />
2. Fixed - After filter, gallery showing all images<br />

= 04 Mar 2013 =

1.2.3<br /><br />
1. Fixed - Error on above thumbnails on page (http://wordpress.org/support/topic/error-on-above-thumbnails-on-page)<br />

= 05 Feb 2013 =

1.2.2<br /><br />
1. Fixed - Jetpack compatibility issue (http://wordpress.org/support/topic/jetpack-compatibility-issue)<br />
2. Fixed - Two menu items for 'Item Type' (http://wordpress.org/support/topic/plugin-nimble-portfolio-observation-on-install)<br />
3. Fixed - No "Nimble Portfolio" tab on Dashboard/Admin Menu (http://wordpress.org/support/topic/no-nimble-tab-on-dashboard)<br />

= 12 Oct 2012 =

1.2.1<br /><br />
1. A small bug that was printing the shortcode out, instead of replacing the shortcode.<br />
2. Function to use in php code i.e. `nimble_portfolio_show()`.<br />

= 31 Aug 2012 =

1.2.0<br /><br />
1. A small bug that was hiding the item types.<br />
2. A lot of people were asking for PrettyPhoto so now plugin uses PrettyPhoto instead of fancybox for gallery.<br />

= 25 Aug 2012 = 

1.1.0<br /><br />
1. A small bug that did not allow selection of featured images from library files has now been fixed. You can select from the images available in your library for portfolio items.<br />
2. Another bug that stopped sorting of portfolio category names that contained special charcters has been resolved. Now category names with special characters can be sorted. This is useful if you want to create a price-range sort feature using $ or £ etc in your category names.<br />


= 29 Jun 2012 =

1.0.0 – First release<br />

== Upgrade Notice ==

= Version 2 released =

When upgrading from version 1 to version 2, your current plugin skin (template) will be replaced by new default skin, there is no going back to your old plugin skin. But who cares when your old fixed style skin is replaced by modern responsive design :)
