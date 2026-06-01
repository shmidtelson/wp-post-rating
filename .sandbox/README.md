# Playground / Studio sandbox

`blueprint.json` in this folder installs **wp-post-rating** from the GitHub `production` branch zip and seeds a demo post with the `[wp_rating]` shortcode.

## WordPress Playground (browser)

Open [Playground](https://playground.wordpress.net/) and load the blueprint URL (replace branch/path if needed):

```
https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/shmidtelson/wp-post-rating/production/.sandbox/blueprint.json
```

Or paste the JSON from `.sandbox/blueprint.json` into the blueprint editor.

## WordPress Studio

1. **Add site** → **Build a new site**
2. Choose **Blueprint** and select `.sandbox/blueprint.json` (or the repo root `blueprint.json` for wordpress.org install)

Studio ignores some blueprint fields (`login`, `landingPage`); use the Studio UI to open the front end or wp-admin.

## wp-now (CLI)

From the repository root (after `yarn build` so `dist/` exists if you test assets):

```bash
npx @wp-now/wp-now start --blueprint=.sandbox/blueprint.json
```

## WordPress.org live preview

Commit `.plugin-assets/blueprints/blueprint.json` to plugin SVN as `assets/blueprints/blueprint.json` (installs from wordpress.org, not GitHub).
