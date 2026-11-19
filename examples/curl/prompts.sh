#!/usr/bin/env bash
# The project's prompts with how many answers name the brand, in total and per engine.
# Usage: SIREFS_API_KEY=sir_... ./prompts.sh <project-slug> [days]
set -euo pipefail
BASE="${SIREFS_API_URL:-https://api.sirefs.com}"
: "${SIREFS_API_KEY:?Set SIREFS_API_KEY to a workspace API key (app.sirefs.com, Settings, API)}"
PROJECT="${1:?Give a project slug (projects.sh lists them)}"
DAYS="${2:-28}"

curl -sS --fail-with-body "$BASE/v1/projects/$PROJECT/prompts?days=$DAYS" \
  -H "Authorization: Bearer $SIREFS_API_KEY"
echo
