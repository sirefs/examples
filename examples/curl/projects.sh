#!/usr/bin/env bash
# List the workspace's projects with the brand's visibility over the last 28 days.
# Usage: SIREFS_API_KEY=sir_... ./projects.sh
set -euo pipefail
BASE="${SIREFS_API_URL:-https://api.sirefs.com}"
: "${SIREFS_API_KEY:?Set SIREFS_API_KEY to a workspace API key (app.sirefs.com, Settings, API)}"

curl -sS --fail-with-body "$BASE/v1/projects" \
  -H "Authorization: Bearer $SIREFS_API_KEY"
echo
