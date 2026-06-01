# Local WordPress (Docker)

Docker Compose stack for developing **wp-post-rating**. The plugin directory is bind-mounted from the repository root, so PHP and template changes apply without `make copy-wp`.

## Quick start

From the **repository root**:

```bash
make build          # or: make build-js-docker
make docker-up      # starts MySQL + WordPress on http://localhost:8080
make docker-install # WP install + activate plugin
```

Default admin: `admin` / `admin` (set during `wp core install`).

## Configuration

```bash
cp .wordpress/.env.example .wordpress/.env
```

Edit `WP_PORT` or database credentials in `.wordpress/.env` if needed.

## Useful commands

| Command | Description |
|---------|-------------|
| `make docker-down` | Stop containers |
| `make plugin-activate` | Activate plugin |
| `make plugin-deactivate` | Deactivate plugin |
| `make plugin-remove` | Deactivate, delete plugin mount target from WP (container data kept) |

## WP-CLI

```bash
docker compose -f .wordpress/docker-compose.yml --project-directory .wordpress run --rm wpcli plugin list
```

## Notes

- WordPress core and uploads live in the Docker volume `wp_html`, not in this folder.
- Only `wp-content/plugins/wp-post-rating` is mounted from `..` (repo root).
- Run `make build` after changing JavaScript/CSS in `assets/`.
