![Screenshot](https://raw.githubusercontent.com/shmidtelson/wp-post-rating/dev/.plugin-assets/screenshot-1.png)

[![Latest Version](https://img.shields.io/github/release/shmidtelson/wp-post-rating.svg?style=flat-square)](https://github.com/shmidtelson/wp-post-rating/releases)
[![Build Status](https://img.shields.io/github/workflow/status/shmidtelson/wp-post-rating/Deploy%20Plugin%20to%20WordPress.org?label=Deploy%20Plugin%20to%20WordPress.org&style=flat-square)](https://github.com/shmidtelson/wp-post-rating/actions?query=workflow%3A%22Deploy+Plugin+to+WordPress.org%22)

# Wordpress post stars rating plugin
## Description ##
WP-POST-RATING is powerful rating plugin with ajax security requests. 

Plugin created based on the https://github.com/wppunk/WPPlugin

Deploy through https://github.com/10up/action-wordpress-plugin-deploy
### Features:
* Very faster
* Seo-friendly
* without jQuery (Native js)

## Requirements ##
* WordPress >= 6.0
* PHP >= 8.1

## Local development ##

**Docker** (plugin bind-mounted from repo):

```bash
make dev
# http://localhost:8080 — admin / admin
```

See [.wordpress/README.md](.wordpress/README.md).

If the plugin cannot be deleted from **Plugins** in wp-admin, you likely have the Docker bind-mount override enabled — run `make plugin-remove-mount`, then delete again or use `make plugin-remove`.

**Playground / Studio**: [`.sandbox/blueprint.json`](.sandbox/blueprint.json) (GitHub zip) or root [`blueprint.json`](blueprint.json) (wordpress.org). See [.sandbox/README.md](.sandbox/README.md).

## Links ##
https://wordpress.org/plugins/wp-post-rating/

## Changelog & release notes ##
- [CHANGELOG.md](CHANGELOG.md) — full version history
- [RELEASE_NOTES.md](RELEASE_NOTES.md) — **1.3.0** upgrade guide and highlights
- [readme.txt](readme.txt) — WordPress.org plugin readme (includes changelog)

## License ##
[MIT](https://raw.githubusercontent.com/shmidtelson/wp-post-rating/master/LICENSE)
