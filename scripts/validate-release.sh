#!/usr/bin/env bash
# Simulate the wordpress.org zip contents using .distignore (same rules as CI BUILD_DIR).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
STAGE="${ROOT}/build/release-check"
PLUGIN="${STAGE}/wp-post-rating"

rm -rf "${STAGE}"
mkdir -p "${PLUGIN}"

rsync -a \
	--delete \
	--exclude='build/' \
	--exclude-from="${ROOT}/.distignore" \
	"${ROOT}/" "${PLUGIN}/"

echo "Release file count: $(find "${PLUGIN}" -type f | wc -l)"
echo "Must exist:"
test -f "${PLUGIN}/wp-post-rating.php"
test -f "${PLUGIN}/includes/autoload.php"
test -f "${PLUGIN}/dist/main.bundle.js"

echo "Must NOT exist:"
! test -f "${PLUGIN}/.distignore"
! test -f "${PLUGIN}/.editorconfig"
! test -d "${PLUGIN}/vendor_prefixed"
! test -d "${PLUGIN}/assets/js"

echo "OK — package matches expected wordpress.org layout."
