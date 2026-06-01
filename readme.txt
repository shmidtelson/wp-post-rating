=== Wp Post Rating ===
Contributors: shmidtelson
Donate link: https://github.com/shmidtelson/wp-post-rating
Tags: rating, star rating, post rating, ajax, schema
Requires at least: 6.0
Tested up to: 6.8.5
Requires PHP: 8.1
Stable tag: 1.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lightweight AJAX 5-star post ratings with Schema.org markup, shortcodes, a sidebar widget, and admin vote management.

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
+English (en_US)
+Spanish (es_ES)
+Russian (ru_RU)

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
= 1.3.0 =
* Changed: Removed runtime Composer vendor (Twig, Symfony DI); plugin uses a lightweight PSR-4 autoloader and PHP templates
* Added: `PluginBootstrap` service wiring, `.wordpress/` Docker dev stack with plugin bind-mount, Playground blueprints (`.sandbox/`, root `blueprint.json`)
* Fixed: Text domain loaded on `init` (WordPress 6.7+ notice); lazy loading for admin list table and widget classes
= 1.2.5 =
* Added: Makefile `plugin-remove` to deactivate and remove the plugin from local Docker installs
* Improved: `.distignore` excludes dev tooling from WordPress.org packages (node_modules, Docker/Makefile, docs)
= 1.2.4 =
* Fixed: wordpress.org and incomplete installs missing `src/Compat/ListTableLoader.php` (fatal on activation)
* Fixed: release package now always ships `src/Compat/` for `WPR\Compat\ListTableLoader` (Composer PSR-4 autoload)
= 1.2.3 =
* Requires WordPress 6.0+ and PHP 8.1+ (tested up to WordPress 6.8)
* Added: display rating before or after single post content, or manual shortcode/PHP only
* Added: English (en_US), Spanish (es_ES), and updated Russian (ru_RU) translations
* Added: `make i18n` to compile language files; Docker/Makefile for local development
* Added: WordPress Playground blueprint (`assets/blueprints/blueprint.json`) for plugin directory preview
* Improved text domain loading with English (en_US) fallback
* Improved activation and automatic `wpr_rating` table creation when missing after deploy
* Improved admin settings sanitization, bulk actions, and SQL ORDER BY handling
* Improved Twig translations and PSR-4 autoload; build uses Yarn and sass
* Fixed WP_List_Table and admin ratings screen on newer WordPress
* Fixed deprecated PHP filter usage (PHP 8.2+), ConfigService notices, and version-check deactivation
* Fixed empty plugin options JSON decode and several admin UI strings
* Security: removed hardcoded third-party API credentials from plugin code
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
