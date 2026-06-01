.PHONY: help install setup build build-js-docker copy-wp copy-wp-build docker-up docker-down docker-install plugin-activate plugin-deactivate dev

WP_DIR := .wordpress
WP_PLUGIN_DIR := $(WP_DIR)/wp-content/plugins/wp-post-rating
WP_URL ?= http://localhost:8080
RSYNC_EXCLUDES := scripts/rsync-excludes.txt
NODE_IMAGE ?= node:20-bookworm-slim
COMPOSE_FILE := docker-compose.wordpress.yml
DC := docker compose -f $(COMPOSE_FILE) --project-directory $(WP_DIR)

# Prefer yarn from PATH; Make does not load nvm/fnm shell hooks by default.
YARN := $(shell command -v yarn 2>/dev/null)

help:
	@echo "Targets:"
	@echo "  install            composer install + yarn install + build"
	@echo "  setup              composer install + yarn install"
	@echo "  build              yarn build (needs yarn in PATH)"
	@echo "  build-js-docker    yarn install + build via Docker (no local yarn)"
	@echo "  copy-wp            rsync plugin -> $(WP_PLUGIN_DIR)"
	@echo "  copy-wp-build      build (or build-js-docker) + copy-wp"
	@echo "  docker-up          start WordPress stack"
	@echo "  docker-down        stop WordPress stack"
	@echo "  docker-install     WP install + activate plugin"
	@echo "  plugin-activate    activate wp-post-rating in Docker WP"
	@echo "  plugin-deactivate  deactivate wp-post-rating"
	@echo "  dev                setup + copy-wp-build + docker"

install: setup build

setup:
	composer install --no-interaction
	@$(MAKE) yarn-install

yarn-install:
ifneq ($(YARN),)
	$(YARN) install --frozen-lockfile
else
	@echo "yarn not in PATH — run: make build-js-docker  (or install Node + Yarn)"
	@exit 1
endif

build:
ifneq ($(YARN),)
	$(YARN) build
else
	@echo "yarn not in PATH. Use: make build-js-docker"
	@exit 1
endif

build-js-docker:
	docker run --rm -u $$(id -u):$$(id -g) \
		-v "$$(pwd):/app" -w /app $(NODE_IMAGE) \
		sh -c "corepack enable && corepack prepare yarn@1.22.22 --activate && yarn install --frozen-lockfile && yarn build"

copy-wp:
	@test -f dist/main.bundle.js || (echo "Missing dist/. Run: make build  OR  make build-js-docker" >&2; exit 1)
	@command -v composer >/dev/null && composer dump-autoload -o --no-interaction || true
	mkdir -p $(WP_PLUGIN_DIR)
	rsync -a --delete --exclude-from=$(RSYNC_EXCLUDES) ./ $(WP_PLUGIN_DIR)/
	@echo "Copied to $(WP_PLUGIN_DIR)"

copy-wp-build:
ifneq ($(YARN),)
	@$(MAKE) build copy-wp
else
	@$(MAKE) build-js-docker copy-wp
endif

docker-up:
	@test -f $(WP_DIR)/.env || cp .wordpress-env.example $(WP_DIR)/.env
	$(DC) up -d

docker-down:
	$(DC) down

docker-install:
	$(DC) run --rm wpcli --url="$(WP_URL)" core is-installed --allow-root \
		|| $(DC) run --rm wpcli --url="$(WP_URL)" core install \
			--title="WP Post Rating Dev" \
			--admin_user=admin \
			--admin_password=admin \
			--admin_email=admin@example.com \
			--skip-email \
			--allow-root
	$(MAKE) plugin-activate

plugin-activate:
	$(DC) run --rm wpcli plugin activate wp-post-rating --allow-root

plugin-deactivate:
	$(DC) run --rm wpcli plugin deactivate wp-post-rating --allow-root

dev: setup copy-wp-build docker-up docker-install
	@echo ""
	@echo "Site:  $(WP_URL)"
	@echo "Admin: $(WP_URL)/wp-admin  (admin / admin)"
