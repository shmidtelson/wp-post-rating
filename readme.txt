=== Wp Post Rating ===
Contributors: shmidtelson
Donate link: https://github.com/shmidtelson/wp-post-rating
Tags: 5 star, google rating, postrating, rating, ratings, seo rating, rating snippet
Requires at least: 4.9.8
Tested up to: 5.7.2
Requires PHP: 7.2
Stable tag: 1.1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

## WP-POST-RATING is powerful rating plugin with ajax security requests.

###Features:
* Very faster
*Plugin use OOP and Vanilla JS, svg icons and CSS variables*

* Seo-friendly
*Plugin use Schema for show stars rating in google search results*

* Without jQuery (Native js)
*Native js is very fast*

* Customize color of stars
*You can customize color of stars and the second color will generate automatic*

* MultiLanguages
+English
+Russian

###Functional:
* Widget for show latest (any sort) votes in sidebar (other place)
####Shortcodes:
* `[wp_rating]` show rating
* `[wp_rating_total]` show total votes for current post
* `[wp_rating_avg]` show total votes for current post
== Installation ==

1. Upload the `wp-post-rating` directory to the `/wp-content/plugins/` directory on your web server
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In settings copy shortcode and put it in your template.

== Changelog ==
= 1.2.0 =
* Stars rating library moved to @romua1d/star-rating-js library
= 1.1.1.0 =
* Global refactoring. Moved to Symfony DI
= 1.1.0.4 =
* Fix critical bug
= 1.1.0.3 =
* Fixed bug which blocked work with page builders, like as Divi, Beaver, Visual Composer, Themify, Elementor, Oxygen
= 1.1.0.2 =
* Fixed bug with ajax request
* Fixed schema.org json
= 1.1.0 =
* Global refactoring
* Added twig templates
* Added checkbox for activating schema
* Added shortcodes total and avg count votes
= 1.0.5 =
* Added full color constructor for rating view
* Small refactoring
= 1.0.4.2 =
* Fix vote table default sort in admin area
* Small refactoring
= 1.0.4.1 =
* Fix bug with pagination all votes in admin area
= 1.0.4 =
* Added color chooser support
= 1.0.3.3 =
* Optimize XHR to backend
= 1.0.3.2 =
* Fix ajax incorrect answer
= 1.0.3.1 =
* Fixes with loader and display data. Correct SEO schema
= 1.0.3 =
* Fix username in widget
* Fix date in widget for multilang sites
* Remove event click from star in widget
* Add validator for ajax vote request
* Chore code
* Update language
= 1.0.2 =
* Visual fixes
= 1.0.1 =
* Change horizontal position
= 1.0.0 =
Release.
* Ajax voting
* Votes in admin panel
* Shortcode support

== Screenshots ==
1. How to displaing on your site
2. List of all votes
3. Settings page
4. Display widget on frontend
5. Widget settings
