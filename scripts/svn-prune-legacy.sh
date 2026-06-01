#!/usr/bin/env bash
# Remove pre-1.3.0 paths from wordpress.org SVN trunk so deploy can drop Twig/vendor trees.
set -euo pipefail

SLUG="${1:-wp-post-rating}"
SVN_URL="https://plugins.svn.wordpress.org/${SLUG}"
WORKDIR="${RUNNER_TEMP:-/tmp}/svn-prune-${SLUG}"

if [[ -z "${SVN_USERNAME:-}" || -z "${SVN_PASSWORD:-}" ]]; then
	echo "SVN_USERNAME and SVN_PASSWORD must be set." >&2
	exit 1
fi

SVN_AUTH=(--username "$SVN_USERNAME" --password "$SVN_PASSWORD" --no-auth-cache --non-interactive)

LEGACY_PATHS=(
	vendor
	vendor_prefixed
	views
	dependencies
	config
	docker-compose.wordpress.yml
	.wordpress-env.example
)

echo "Checking out ${SVN_URL}/trunk ..."
rm -rf "$WORKDIR"
svn co "${SVN_URL}/trunk" "$WORKDIR/trunk" "${SVN_AUTH[@]}"

cd "$WORKDIR/trunk"
REMOVED=()
for path in "${LEGACY_PATHS[@]}"; do
	if [[ -e "$path" ]]; then
		svn rm --force "$path"
		REMOVED+=("$path")
	fi
done

if [[ ${#REMOVED[@]} -eq 0 ]]; then
	echo "No legacy paths to remove on trunk."
	exit 0
fi

echo "Committing removal of: ${REMOVED[*]}"
svn commit -m "Remove legacy Composer/Twig paths before 1.3.0 release" "${SVN_AUTH[@]}"
