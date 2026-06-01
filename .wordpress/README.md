# Local WordPress (Docker)

Docker Compose stack for developing **wp-post-rating**.

## Quick start

From the **repository root**:

```bash
make build          # or: make build-js-docker
make docker-up
make docker-install # copy plugin, install WP, activate
```

- Site: http://localhost:8080  
- Admin: http://localhost:8080/wp-admin (`admin` / `admin`)

After changing PHP/templates, run `make copy-wp` (or `make copy-wp-build` if JS/CSS changed).

## Delete plugin from WordPress admin

By default the plugin is **copied** into the container, not bind-mounted — you can deactivate and delete it under **Plugins** like any normal plugin.

If you use `docker-compose.override.yml` for live reload (see below), deletion from the admin **will not work** until you remove the override.

## Live reload (optional)

```bash
cp .wordpress/docker-compose.override.yml.example .wordpress/docker-compose.override.yml
make copy-wp
make docker-up
```

Edits in the repo apply immediately, but the plugin **cannot be removed from the admin** while the bind mount is active.

To switch back:

```bash
make plugin-remove-mount   # deactivate + remove override + restart
make copy-wp
make plugin-activate
```

## Commands

| Command | Description |
|---------|-------------|
| `make copy-wp` | Sync plugin into the container |
| `make copy-wp-build` | Build assets + copy-wp |
| `make docker-down` | Stop containers |
| `make plugin-activate` | Activate plugin |
| `make plugin-deactivate` | Deactivate plugin |
| `make plugin-remove` | Deactivate and delete plugin (admin-style) |
| `make plugin-remove-mount` | Tear down bind-mount override |

## Configuration

```bash
cp .wordpress/.env.example .wordpress/.env
```

## WP-CLI

```bash
docker compose -f .wordpress/docker-compose.yml --project-directory .wordpress run --rm wpcli plugin list
```
