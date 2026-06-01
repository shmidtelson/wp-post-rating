.PHONY: help install setup build build-js-docker i18n validate-release copy-wp copy-wp-build docker-up docker-down docker-install plugin-activate plugin-deactivate plugin-remove plugin-remove-mount dev dev-mount

WP_DIR := .wordpress
WP_VOLUME := wp-post-rating_wp_html
WP_PLUGIN_PATH := wp-content/plugins/wp-post-rating
WP_URL ?= http://localhost:8080
SYNC_IMAGE ?= alpine:3.20
RSYNC_EXCLUDES := scripts/rsync-excludes.txt
NODE_IMAGE ?= node:20-bookworm-slim
COMPOSE_FILE := $(WP_DIR)/docker-compose.yml
DC := docker compose -f $(COMPOSE_FILE) --project-directory $(WP_DIR)

YARN := $(shell command -v yarn 2>/dev/null)

help:
	@echo "Targets:"
	@echo "  install            yarn install + build"
	@echo "  setup              yarn install"
	@echo "  build              yarn build (needs yarn in PATH)"
	@echo "  build-js-docker    yarn install + build via Docker (no local yarn)"
	@echo "  i18n               compile .po -> .mo in languages/"
	@echo "  validate-release   check wordpress.org zip contents via .distignore"
	@echo "  copy-wp            rsync plugin into Docker WP (required after code changes)"
	@echo "  copy-wp-build      build + copy-wp"
	@echo "  docker-up          start WordPress stack (.wordpress/docker-compose.yml)"
	@echo "  docker-down        stop WordPress stack"
	@echo "  docker-install     WP install + activate plugin"
	@echo "  plugin-activate    activate wp-post-rating in Docker WP"
	@echo "  plugin-deactivate  deactivate wp-post-rating"
	@echo "  plugin-remove      deactivate + delete plugin in Docker (admin-style)"
	@echo "  plugin-remove-mount  remove bind-mount override; use when live reload blocked delete"
	@echo "  dev                setup + build + copy-wp + docker-up + docker-install"
	@echo "  dev-mount          dev + enable bind-mount override for live PHP edits"

install: setup build

setup:
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

validate-release:
	@./scripts/validate-release.sh

i18n:
	@for po in languages/*.po; do \
		msgfmt -o "$${po%.po}.mo" "$$po" && echo "Compiled $$po"; \
	done

copy-wp:
	@test -f dist/main.bundle.js || (echo "Missing dist/. Run: make build  OR  make build-js-docker" >&2; exit 1)
	@if [ -f $(WP_DIR)/docker-compose.override.yml ]; then \
		echo "Note: bind-mount override is active — copy-wp is skipped (edits apply from repo)."; \
		echo "To delete the plugin from admin: make plugin-remove-mount"; \
	else \
		docker run --rm \
			-v "$$(pwd):/src:ro" \
			-v $(WP_VOLUME):/var/www/html \
			$(SYNC_IMAGE) sh -c '\
				apk add --no-cache rsync >/dev/null && \
				mkdir -p /var/www/html/$(WP_PLUGIN_PATH) && \
				rsync -a --delete --exclude-from=/src/$(RSYNC_EXCLUDES) \
					/src/ /var/www/html/$(WP_PLUGIN_PATH)/ \
			' && \
		echo "Copied to Docker volume $(WP_VOLUME):/$(WP_PLUGIN_PATH)"; \
	fi

copy-wp-build:
ifneq ($(YARN),)
	@$(MAKE) build copy-wp
else
	@$(MAKE) build-js-docker copy-wp
endif

docker-up:
	@test -f $(WP_DIR)/.env || cp $(WP_DIR)/.env.example $(WP_DIR)/.env
	$(DC) up -d

docker-down:
	$(DC) down

docker-install: copy-wp
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

plugin-remove:
	-$(DC) run --rm wpcli plugin delete wp-post-rating --allow-root 2>/dev/null || true
	@echo "Plugin removed from Docker WP (repo source unchanged)"

plugin-remove-mount:
	-$(DC) run --rm wpcli plugin deactivate wp-post-rating --allow-root 2>/dev/null || true
	rm -f $(WP_DIR)/docker-compose.override.yml
	$(DC) up -d --force-recreate wordpress
	@echo "Bind-mount override removed. Run: make copy-wp && make plugin-activate"

dev: setup build docker-up docker-install

dev-mount: dev
	@test -f $(WP_DIR)/docker-compose.override.yml || cp $(WP_DIR)/docker-compose.override.yml.example $(WP_DIR)/docker-compose.override.yml
	$(DC) up -d --force-recreate wordpress wpcli
	@echo "Live bind-mount enabled — plugin delete from admin is disabled until: make plugin-remove-mount"
	@echo ""
	@echo "Site:  $(WP_URL)"
	@echo "Admin: $(WP_URL)/wp-admin  (admin / admin)"
	@echo "Playground: .sandbox/blueprint.json or root blueprint.json"
