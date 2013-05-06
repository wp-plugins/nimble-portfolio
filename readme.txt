=== Filterable jQuery Portfolio Plugin ===
Contributors: nimble3,mamirulamin
Donate link: http://www.nimble3.com
Tags: portfolio, filterable portfolio, jquery portfolio, sortable portfolio, portfolio pagination, template based portfolio, quick sand, quicksand, CSS3 transformation, multiple portfolios, portfolio templates, prettyphoto, lightbox, prettyphoto lightbox, prettyphoto gallery, lightbox gallery, multiple prettyphoto galleries, multiple lightbox galleries
Requires at least: 3.0.1
Tested up to: 3.5.1
Stable tag: 1.2.5
License: GPLv2 or later

This powerful portfolio plugin is highly suitable to showcase your work / portfolio and group them nicely under jQuery powered tabs.
== Description ==

<h3>Nimble Portfolio</h3>
Using this free plugin you can transform your portfolio in to a cutting edge jQuery powered gallery that lets you feature and sort your work like a pro.

= Plugin Features =

1. Custom post types for portfolio items.
2. Youtube, Vimeo, Quicktime video support.
3. Built-in PrettyPhoto gallery for image and video preview.
4. Easy categorization and jQuery sort feature.

= Usage Instructions =

1) Add portfolio item types under `Nimble Portfolio -> Item Type`, such as web, mobile, graphics e.t.c.
2) Add new portfolio items using custom post type under `Nimble Portfolio -> Add Portfolio Item`. Add item title, description e.t.c.
3) Upload and set featured image from the far right bottom box.
4) Specify full-size Image URL or Video URL (youtube, vimeo) in the input field `Image/Video URL` on the left. You can also use `URL from Media Library` button to select the URL of full-size image from Media Library.
5) Specify a live URL for your project in the input field `Portfolio URL`.

= Shortcode =

Insert the portfolio in your page or post with this shortcode

`[nimble-portfolio]`

= PHP Code =

`nimble_portfolio()` 
and 
`nimble_portfolio_show()`

= Demo =

Go to http://www.nimble3.com/portfolio-demo/

= Premium Version =

With Premium features such as:<br />
1. Category Order Sorting.<br />
2. Item Order Sorting.<br />
3. Display Selected Types and Manage Multiple Portfolios.<br />
4. Shortcode Generator.<br />
5. Pagination Support for Large Portfolios.<br />
6. Configurable Separate Gallery for each Item.<br />
7. Making use of Quick Sand Plugin and Configurable CSS3 Transformation.<br />
8. More templates.<br /><br />
To learn more please visit http://www.nimble3.com/shop/premium-nimble-portfolio-plugin/

= Upgrade Notice =

When you upgrade, the following problems may arise.<br />
1. If you have customized the template using the previous version, then your template may be lost. Therefore, take out a backup of your template files that you can re-deploy after upgrade.<br />
2. (Only if upgrading from version 1.0.0) Your portfolio categories may be lost. Therefore, note down your category names and items under each category as you might be required to re-create all categories and re-assign items under them.<br />

== Installation ==

1. Download the plugin<br />
2. Unzip the ZIP package on your hard drive<br />
3. FTP upload the plugin files to the `plugins` folder on your WP installation<br />

== Frequently Asked Questions ==

Please post your comments and questions at `http://www.nimble3.com/nimble-portfolio-free-filterable-jquery-porfolio-wordpress-plugin/`

== Screenshots ==

1. Nimble Portfolio backend
2. Nimble Portfolio frontend

== Changelog ==

=29/06/2012=

1.0.0 – First release<br />

=25/08/2012= 

1.1.0<br /><br />
1. A small bug that did not allow selection of featured images from library files has now been fixed. You can select from the images available in your library for portfolio items.<br />
2. Another bug that stopped sorting of portfolio category names that contained special charcters has been resolved. Now category names with special characters can be sorted. This is useful if you want to create a price-range sort feature using $ or £ etc in your category names.<br />

=31/08/2012=

1.2.0<br /><br />
1. A small bug that was hiding the item types.<br />
2. A lot of people were asking for PrettyPhoto so now plugin uses PrettyPhoto instead of fancybox for gallery.<br />

=12/10/2012=

1.2.1<br /><br />
1. A small bug that was printing the shortcode out, instead of replacing the shortcode.<br />
2. Function to use in php code i.e. `nimble_portfolio_show()`.<br />

=05/02/2013=

1.2.2<br /><br />
1. Fixed - Jetpack compatibility issue (http://wordpress.org/support/topic/jetpack-compatibility-issue)<br />
2. Fixed - Two menu items for 'Item Type' (http://wordpress.org/support/topic/plugin-nimble-portfolio-observation-on-install)<br />
3. Fixed - No "Nimble Portfolio" tab on Dashboard/Admin Menu (http://wordpress.org/support/topic/no-nimble-tab-on-dashboard)<br />

=04/03/2013=

1.2.3<br /><br />
1. Fixed - Error on above thumbnails on page (http://wordpress.org/support/topic/error-on-above-thumbnails-on-page)<br />

=05/03/2013=

1.2.4<br /><br />
1. Fixed - Warning: Illegal string offset 'template' issue (http://wordpress.org/support/topic/issues-5)<br />
2. Fixed - After filter, gallery showing all images<br />

=06/05/2013=

1.2.5<br /><br />
1. Added `URL from Media Library` button to select full size Image URL much easier from your site's Media Library.<br />

== Upgrade Notice ==

When you upgrade, the following problems may arise.<br /><br />
1. If you have customized the template using the previous version, then your template may be lost. Therefore, take out a backup of your template files that you can re-deploy after upgrade.<br />
2. (Only if upgrading from version 1.0.0) Your portfolio categories may be lost. Therefore, note down your category names and items under each category as you might be required to re-create all categories and re-assign items under them.<br />
