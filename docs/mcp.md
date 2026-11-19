# sirefs MCP server: your AI visibility numbers for AI assistants

sirefs runs a remote MCP server, so an AI assistant can read how the AI answer engines mention, rank and cite your brand, explain the numbers and work through the next steps with you.

- **Endpoint:** `https://api.sirefs.com/mcp` (Streamable HTTP, stateless: every call is one `POST`)
- **Auth:** OAuth sign-in from clients that support it, or a workspace API key as `Authorization: Bearer sir_...`
- **Plans:** included in Pro and Agency
- **Protocol versions:** `2025-06-18`, `2025-03-26`, `2024-11-05`

## Two ways to sign in

**OAuth (assistants).** Give the client the endpoint and nothing else. It discovers the authorization server, registers itself, and sends you to sirefs to sign in and pick the workspace. It is OAuth 2.1 with PKCE (S256) and dynamic client registration, so a client that isn't listed below works too. Access tokens last an hour and the client renews them with a refresh token. A connection acts as the person who signed in, on the workspace they chose. Connected assistants are listed under **Settings, API** in the app, where you can disconnect them.

**API key (scripts and clients without OAuth).** Create a key under **Settings, API** at [app.sirefs.com](https://app.sirefs.com) (workspace owners and admins) and send it as a bearer token. A key stands for the whole workspace and doesn't expire. The same key works for the [REST API](../README.md).

The metadata is public:

```bash
curl "https://api.sirefs.com/.well-known/oauth-protected-resource"
curl "https://api.sirefs.com/.well-known/oauth-authorization-server"
```

## Scopes

| Scope | What it allows |
|---|---|
| `read` | Read your projects' numbers, answers, sources, audit and next steps |
| `write` | Add or pause prompts, track competitors and run the audit, within your plan |

An OAuth client gets the scopes it asks for, both when it asks for none. A token with only `read` gets a `scope` error from the tools that change a project. An API key has both scopes.

## Claude Code

With OAuth:

```bash
claude mcp add --transport http sirefs https://api.sirefs.com/mcp
```

Then type `/mcp` in Claude Code and choose sirefs to sign in.

With an API key:

```bash
claude mcp add --transport http sirefs https://api.sirefs.com/mcp \
  --header "Authorization: Bearer sir_your_key"
```

## Claude (web and desktop)

Open Settings, then Connectors, choose "Add custom connector" and paste `https://api.sirefs.com/mcp`. Claude sends you to sirefs to sign in and choose the workspace.

## Cursor

`.cursor/mcp.json`, with OAuth (Cursor opens the sign-in itself):

```json
{
  "mcpServers": {
    "sirefs": { "url": "https://api.sirefs.com/mcp" }
  }
}
```

Or with an API key:

```json
{
  "mcpServers": {
    "sirefs": {
      "url": "https://api.sirefs.com/mcp",
      "headers": { "Authorization": "Bearer sir_your_key" }
    }
  }
}
```

## VS Code

`.vscode/mcp.json`. VS Code asks for the key once and keeps it out of the file:

```json
{
  "inputs": [
    { "type": "promptString", "id": "sirefs-key", "description": "sirefs API key", "password": true }
  ],
  "servers": {
    "sirefs": {
      "type": "http",
      "url": "https://api.sirefs.com/mcp",
      "headers": { "Authorization": "Bearer ${input:sirefs-key}" }
    }
  }
}
```

## Any other client, or no client at all

The server answers plain JSON-RPC 2.0, so you can call it with curl:

```bash
curl "https://api.sirefs.com/mcp" \
  -H "Authorization: Bearer sir_your_key" \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc": "2.0", "id": 1, "method": "tools/call",
       "params": {"name": "get_next_steps", "arguments": {"project": "acme"}}}'
```

[examples/curl/mcp-tools.sh](../examples/curl/mcp-tools.sh) lists the tools the same way. There are no sessions and no server-to-client stream; `GET /mcp` answers `405`.

## Tools

There are 19 tools. `project` is a project's slug or id; `list_projects` returns them.

### Reading (scope `read`)

| Tool | Arguments | What it returns |
|---|---|---|
| `list_projects` | none | The workspace's projects with their visibility over the last 28 days. Start here |
| `get_overview` | `project`, `days`, `detail` | For the brand and each tracked competitor: visibility, citation rate, recommendation rate, average rank and share of voice, each rate with its sample size and 95% interval |
| `get_prompts` | `project`, `days`, `detail` | The prompts with market, topic, whether each is active, and the brand's visibility per engine |
| `get_trend` | `project`, `days`, `entity` | Visibility over time for the brand or a competitor, per engine: daily points up to 35 days, weekly beyond |
| `get_competitors` | `project`, `days`, `detail` | Tracked brands head to head, and the untracked brands the engines name most (`suggested`) |
| `get_sources` | `project`, `days` | The domains and pages the engines cite, and whether each is the brand's own site, a competitor's or a third party |
| `get_community_threads` | `project`, `days` | Forum and community threads the engines cite, with the prompts they answer |
| `get_brand_claims` | `project`, `days` | The factual claims the engines make about the brand, and the reasons they give for naming each competitor |
| `get_next_steps` | `project`, `days` | What to do about the numbers, ranked by impact, each step with its evidence and a dashboard link |
| `get_audit` | `project` | The latest GEO audit of the project's site: a score from 0 to 100 and every check's status |
| `list_answers` | `project`, `engine`, `prompt_id`, `mentioned`, `limit`, `skip`, `detail` | Collected answers, newest first, without their text |
| `get_answer` | `project`, `answer_id` | One answer in full: its text, the citations and the analysis |
| `list_alerts` | `project`, `limit` | Recent alerts: significant visibility changes, and competitors the engines started naming |

### Changing a project (scope `write`)

| Tool | Arguments | What it does |
|---|---|---|
| `add_prompts` | `project`, `prompts` | Adds prompts; they count against the plan's prompt allowance |
| `set_prompt_active` | `project`, `prompt_id`, `active` | Pauses or resumes a prompt |
| `track_competitor` | `project`, `name`, `aliases`, `domains` | Starts tracking a brand as a competitor; stored answers are re-matched |
| `mark_not_competitor` | `project`, `name` | Marks a brand the engines name as not a competitor, so it is no longer suggested |
| `set_step_status` | `project`, `step_id`, `status`, `reason` | Marks a next step `done`, `dismissed` or `open` |
| `run_audit` | `project` | Runs the GEO audit of the project's site now, at most once an hour per project |

The server tells the assistant to confirm with you before it changes anything. The live list with every argument's schema is what `tools/list` returns.

## Errors and limits

A problem the assistant should tell you about comes back as a tool result with `isError: true` and a JSON body, not as an HTTP error:

| `error` | Meaning |
|---|---|
| `not_found` | No such project, prompt or answer in the workspace |
| `bad_arguments` | An argument is missing or wrong; the message names it |
| `plan` | The workspace's plan doesn't include the MCP server |
| `plan_limit` | The plan's allowance is used up; the message says which |
| `scope` | The connection may only read |
| `too_soon` | The audit ran less than an hour ago |
| `rate_limit` | More than 120 calls in a minute |

HTTP `401` is only for a missing, unknown, expired or revoked token.

- 120 calls a minute per key or connection, counted together with the REST API.
- The tools read what is already collected and never ask an AI engine on your behalf.

## Things to ask

- "What should we work on this week for Acme?"
- "Which prompts name our competitors but never us?"
- "Which third-party sites do the engines cite most for our prompts?"
- "Did our visibility on Perplexity change this month? Give me the sample sizes."
