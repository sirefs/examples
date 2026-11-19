#!/usr/bin/env bash
# Ask the MCP server for its tool list with one JSON-RPC request.
# Usage: SIREFS_API_KEY=sir_... ./mcp-tools.sh
set -euo pipefail
BASE="${SIREFS_API_URL:-https://api.sirefs.com}"
: "${SIREFS_API_KEY:?Set SIREFS_API_KEY to a workspace API key (app.sirefs.com, Settings, API)}"

curl -sS --fail-with-body "$BASE/mcp" \
  -H "Authorization: Bearer $SIREFS_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc": "2.0", "id": 1, "method": "tools/list"}'
echo
