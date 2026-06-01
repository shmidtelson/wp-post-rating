# Release notes

## 1.2.3 — 2026-06-01

### Requirements

- **WordPress** 6.0 or newer (tested up to 6.8)
- **PHP** 8.1 or newer

### Highlights

- Compatibility and security pass for modern WordPress and PHP 8.x
- Optional automatic rating block **before** or **after** single post content
- Expanded translations: **English**, **Spanish**, **Russian**
- Local development workflow with Docker and Makefile

### New features

- **Display position** setting on single posts: manual (shortcode/PHP only), before content, or after content (`PostContentHookService`, admin settings).
- **Translation catalogs**: `languages/wp-post-rating-en_US`, `es_ES`, `ru_RU`, plus `wp-post-rating.pot`. Run `make i18n` to recompile `.mo` files after editing `.po` files.
- **Developer tooling**: `docker-compose.wordpress.yml`, `Makefile` targets (`copy-wp`, `docker-up`, `build-js-docker`, `i18n`, etc.), `.wordpress-env.example`.

### Improvements

- Plugin text domain loads from locale-specific `.mo` files with **en_US** fallback (`TranslateService`).
- Twig templates use the correct text domain for translatable strings.
- Database table `wpr_rating` is created on activation and on load if missing (helps when deploying without re-activating).
- Activation hook registered in `wp-post-rating.php` (reliable with Symfony bootstrap).
- Admin ratings list: safer `ORDER BY` handling, deferred `WP_List_Table` loading via `ListTableLoader`.
- Settings and options handling hardened (sanitization, safe JSON decode when options are empty).
- Front-end assets build updated (Yarn, `sass` instead of deprecated `node-sass`).
- PSR-4 autoload for `src/`; Symfony DI scan excludes `Entity`, `Dto`, and `Compat` paths.

### Bug fixes

- Removed deprecated `FILTER_SANITIZE_STRING` usage (PHP 8.2+).
- Fixed dynamic `$wpdb` property notice on `ConfigService`.
- Fixed admin fatal errors around `WP_List_Table` / `convert_to_screen()` on newer WordPress versions.
- Plugin no longer deactivates itself incorrectly when version checks fail.
- Corrected several admin/UI strings (e.g. IP address label, “settings saved” message).

### Security

- Removed embedded third-party API credentials from maintenance code; use your own integrations if needed.
- Stricter sanitization for admin settings and bulk actions.

### Upgrade notes

1. Back up your site before upgrading.
2. After uploading the plugin, visit **Plugins** and ensure it stays active; the rating table is created automatically if needed.
3. Review **Stars rating → Settings** for the new **Display on single posts** option.
4. For custom deployments, run `make copy-wp` (or sync `languages/*.mo`) so translations are on the server.

### Contributors

Thanks to everyone who reported issues and tested on WordPress 6.x and PHP 8.1+.

---

See also [CHANGELOG.md](CHANGELOG.md) (full history) and [readme.txt](readme.txt) (WordPress.org changelog).
