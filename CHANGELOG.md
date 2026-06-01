# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.5] - 2026-06-02

### Added

- Makefile target `plugin-remove` to deactivate and delete the plugin from the local Docker WordPress install (`.wordpress/wp-content/plugins/wp-post-rating`).

### Changed

- `.distignore` updated so WordPress.org release zips exclude `node_modules`, Docker/Makefile tooling, lockfiles, and repository docs.

## [1.2.4] - 2026-06-01

### Fixed

- WordPress.org and incomplete installs missing `src/Compat/ListTableLoader.php`, causing a fatal error on activation (`Failed opening required .../ListTableLoader.php`).
- Release packages now always include `src/Compat/` so `WPR\Compat\ListTableLoader` loads via Composer PSR-4 autoload.

## [1.2.3] - 2026-06-01

### Added

- Setting to display the rating **before** or **after** single post content, or keep **manual** placement (shortcode / PHP).
- Translation files for **English** (`en_US`), **Spanish** (`es_ES`), and updated **Russian** (`ru_RU`); `languages/wp-post-rating.pot` template.
- `make i18n` target to compile `.po` files to `.mo`.
- Local development setup: `docker-compose.wordpress.yml`, `Makefile`, `.wordpress-env.example`.
- WordPress Playground blueprint at `.plugin-assets/blueprints/blueprint.json` (SVN: `assets/blueprints/blueprint.json`) for plugin preview on WordPress.org.

### Changed

- Minimum **WordPress 6.0** and **PHP 8.1** (tested up to WordPress 6.8).
- Text domain loading uses locale `.mo` files with **en_US** fallback.
- Twig `__()` calls use the `wp-post-rating` text domain.
- Database table `wpr_rating` is created on activation and on plugin load when missing.
- Activation hook registered in `wp-post-rating.php`.
- Front-end build uses Yarn and `sass` (replaces deprecated `node-sass`).
- PSR-4 autoload for `src/`; Symfony DI excludes `Entity`, `Dto`, and `Compat`.
- `ListTableLoader` moved to `WPR\Compat\ListTableLoader` class (Composer autoload, no manual `require_once`).

### Fixed

- `WP_List_Table` / `convert_to_screen()` issues on newer WordPress admin.
- Deprecated `FILTER_SANITIZE_STRING` usage (PHP 8.2+).
- Dynamic `$wpdb` property notice on `ConfigService`.
- Plugin deactivating incorrectly after failed version check.
- Safe JSON decode when plugin options are empty.
- Admin SQL `ORDER BY` whitelist; settings and bulk-action sanitization.
- Admin copy: IP address label, “settings saved” message, and related strings.

### Security

- Removed hardcoded third-party API credentials from maintenance code.

### Removed

- Embedded Telegram bot credentials from `MaintenanceService` (rotate any exposed tokens if you used an older build).

## [1.2.0]

### Changed

- Stars rating UI moved to `@romua1d/star-rating-js`.

## [1.1.1.0]

### Changed

- Global refactoring; Symfony dependency injection container.

## [1.1.0.4]

### Fixed

- Critical bug fix.

## [1.1.0.3]

### Fixed

- Compatibility with page builders (Divi, Beaver Builder, Visual Composer, Themify, Elementor, Oxygen).

## [1.1.0.2]

### Fixed

- Ajax request handling.
- Schema.org JSON-LD output.

## [1.1.0]

### Added

- Twig templates.
- Schema markup toggle in settings.
- Shortcodes `[wp_rating_total]` and `[wp_rating_avg]`.

### Changed

- Major refactoring.

## [1.0.5]

### Added

- Full color customization for the rating widget.

## [1.0.4.2]

### Fixed

- Default sort order for votes table in admin.

## [1.0.4.1]

### Fixed

- Pagination for all votes in admin.

## [1.0.4]

### Added

- Star color picker in settings.

## [1.0.3.3]

### Changed

- Optimized XHR requests to the backend.

## [1.0.3.2]

### Fixed

- Incorrect ajax response handling.

## [1.0.3.1]

### Fixed

- Loader and display data issues; SEO schema output.

## [1.0.3]

### Added

- Ajax vote request validation.

### Changed

- Widget: username display, multilingual dates; removed star click handler in widget sidebar.

## [1.0.2]

### Fixed

- Visual styling issues.

## [1.0.1]

### Changed

- Horizontal positioning of the rating block.

## [1.0.0] - 2018-06-01

### Added

- Ajax voting.
- Votes list in admin.
- `[wp_rating]` shortcode support.

[1.2.5]: https://github.com/shmidtelson/wp-post-rating/compare/v1.2.4...v1.2.5
[1.2.4]: https://github.com/shmidtelson/wp-post-rating/compare/v1.2.3...v1.2.4
[1.2.3]: https://github.com/shmidtelson/wp-post-rating/compare/v1.2.0...v1.2.3
[1.2.0]: https://github.com/shmidtelson/wp-post-rating/releases/tag/v1.2.0
