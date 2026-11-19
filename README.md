# sirefs API and MCP examples: see how AI answer engines mention your brand

[![license MIT](https://img.shields.io/badge/license-MIT-blue)](LICENSE)
[![docs](https://img.shields.io/badge/docs-sirefs.com-0369A1)](https://sirefs.com/docs/api)

**Get your brand's AI visibility as JSON: how often ChatGPT, Google AI Overviews, Google AI Mode, Bing AI Mode, Gemini, Perplexity, Claude and Grok name you, rank you and cite you, next to your competitors.**

These are runnable examples for the [sirefs REST API](https://sirefs.com/docs/api) in curl, Python, JavaScript and PHP, plus the setup for the [sirefs MCP server](docs/mcp.md). [sirefs](https://sirefs.com) asks the AI answer engines your customers' questions on a schedule and records every answer. With the API you read the results from your own scripts: the share of answers that name your brand, the prompts where only competitors show up, the pages the engines cite, and a ranked list of what to do next. The examples have no dependencies, so you can copy one file and run it.

```bash
curl "https://api.sirefs.com/v1/projects/acme/overview" \
  -H "Authorization: Bearer sir_your_key"
```

```json
{
  "window": { "start": "2026-10-23", "end": "2026-11-19", "days": 28 },
  "entities": [
    {
      "entity": "brand",
      "name": "Acme",
      "answers": 370,
      "visibility": { "rate": 0.422, "k": 156, "n": 370, "low": 0.205, "high": 0.673 },
      "citation_rate": { "rate": 0.159, "k": 59, "n": 370, "low": 0.082, "high": 0.287 },
      "recommendation_rate": { "rate": 0.33, "k": 122, "n": 370, "low": 0.164, "high": 0.552 },
      "avg_rank": 1.76,
      "share_of_voice": 0.265,
      "visibility_by_engine": { "chatgpt": [32, 65], "perplexity": [10, 15], "grok": [4, 15] }
    }
  ]
}
```

The answer is shortened here. The full one has one entry per tracked competitor too, every engine, and the project itself. Acme and the other brands in this repository are made up.

## Get started

1. You need a sirefs workspace on the Pro or Agency plan. Sign in at [app.sirefs.com](https://app.sirefs.com), open **Settings, API** and create a key (workspace owners and admins can). The key starts with `sir_` and is shown once.
2. Set the key as an environment variable:

   ```bash
   export SIREFS_API_KEY=sir_your_key
   ```

3. Run an example:

   ```bash
   python examples/python/projects.py
   node examples/javascript/projects.mjs
   php examples/php/projects.php
   ./examples/curl/projects.sh
   ```

`projects` prints your project slugs. The other examples take a slug as their first argument and the number of days to look back as the second (28 when you leave it out). The Python, JavaScript and PHP examples use your first project when you give no slug.

Want to see the numbers before you have an account? The [live demo](https://app.sirefs.com/demo) shows the dashboard of a real project, built on the same numbers the API returns.

## The examples

| What you want | Endpoint | curl | Python | JavaScript | PHP |
|---|---|---|---|---|---|
| List your projects with their visibility | `GET /v1/projects` | [projects.sh](examples/curl/projects.sh) | [projects.py](examples/python/projects.py) | [projects.mjs](examples/javascript/projects.mjs) | [projects.php](examples/php/projects.php) |
| Compare your brand with its competitors | `GET /v1/projects/{project}/overview` | [overview.sh](examples/curl/overview.sh) | [overview.py](examples/python/overview.py) | [overview.mjs](examples/javascript/overview.mjs) | [overview.php](examples/php/overview.php) |
| Find the prompts where you are never named | `GET /v1/projects/{project}/prompts` | [prompts.sh](examples/curl/prompts.sh) | [prompts.py](examples/python/prompts.py) | [prompts.mjs](examples/javascript/prompts.mjs) | [prompts.php](examples/php/prompts.php) |
| See which sites and pages the engines cite | `GET /v1/projects/{project}/sources` | [sources.sh](examples/curl/sources.sh) | [sources.py](examples/python/sources.py) | [sources.mjs](examples/javascript/sources.mjs) | [sources.php](examples/php/sources.php) |
| Get a ranked list of what to do next | `GET /v1/projects/{project}/next-steps` | [next-steps.sh](examples/curl/next-steps.sh) | [next_steps.py](examples/python/next_steps.py) | [next-steps.mjs](examples/javascript/next-steps.mjs) | [next-steps.php](examples/php/next-steps.php) |
| List the MCP server's tools | `POST /mcp` | [mcp-tools.sh](examples/curl/mcp-tools.sh) | | | |

Each language has a small client file (`sirefs.py`, `sirefs.mjs`, `sirefs.php`). It puts the key in the header and raises an error with the status code when the API answers with anything but 2xx. The curl examples print the raw JSON; pipe it through `jq` to read it. All examples only read.

This is the overview example when it runs:

```
$ python examples/python/overview.py acme
Acme: 2026-10-23 to 2026-11-19 (28 days)

Brand            Answers  Visibility      95% interval   Cited  Recommended  Avg rank  Share of voice
Acme                 370       42.2%    20.5% to 67.3%   15.9%        33.0%      1.76           26.5%
Globex               296       78.4%    72.0% to 83.6%   35.5%        62.2%      1.82           39.5%
Initech              370       54.1%    49.0% to 59.1%   25.9%        44.3%      1.76           34.0%

Acme per engine (answers naming it of answers collected):
  bing_ai_mode      20 of 65
  chatgpt           32 of 65
  claude             5 of 15
  gemini            29 of 65
  google_ai_mode    31 of 65
  google_aio        25 of 65
  grok               4 of 15
  perplexity        10 of 15
```

And the next steps:

```
$ node examples/javascript/next-steps.mjs acme
1. [high] Answer “affordable ticketing system for startups” on your site
   8 engines name Globex and Initech instead, in 65 of 74 answers. If a page of yours covers this, open it with the answer and name Acme there; if none does, publish one.
   kind: missing_prompt, id: 3bf0066ab02e3e76
   See the answers: https://app.sirefs.com/acme/projects/6afecc973a517c79d463ab0a/answers?prompt_id=6afecc973a517c79d463ab0e

2. [high] Get mentioned on helpdeskweekly-demo.com
   Cited in 174 answers to your prompts; 109 of them don't mention you.
   kind: get_cited, id: 8377852304afb250
   See the cited pages: https://app.sirefs.com/acme/projects/6afecc973a517c79d463ab0a/sources
```

## What the API gives you

- **Visibility per brand and engine**: the share of answers that name your brand and each tracked competitor, with citation rate, recommendation rate, average rank and share of voice.
- **Results per prompt**: for every question you track, how many answers name you, in total and per engine.
- **Cited sources**: the domains and pages the engines cite, marked as your own, a competitor's or a third party's.
- **Next steps**: what to do about the numbers, ranked by impact, each with its evidence and a link into the dashboard.
- **The answers themselves**: every collected answer with its text, citations and analysis.
- **Trend, competitors, community threads, brand claims, alerts and the GEO audit** of your site.

All of it is under `https://api.sirefs.com/v1`. `{project}` is a project's slug or id.

| Method and path | What it returns |
|---|---|
| `GET /projects` | The workspace's projects with their visibility over 28 days |
| `GET /projects/{project}/overview` | The brand and each competitor: visibility, citation rate, recommendation rate, average rank, share of voice |
| `GET /projects/{project}/prompts` | The prompts with market, topic, status and the brand's visibility per engine |
| `GET /projects/{project}/trend` | Visibility over time per engine for the brand or a competitor (`entity`) |
| `GET /projects/{project}/competitors` | Tracked brands head to head, and untracked brands the engines name (`suggested`) |
| `GET /projects/{project}/sources` | Cited domains and pages |
| `GET /projects/{project}/threads` | Forum and community threads the engines cite |
| `GET /projects/{project}/claims` | The factual claims the engines make about the brand |
| `GET /projects/{project}/next-steps` | What to do, ranked by impact |
| `GET /projects/{project}/audit` | The latest GEO audit of the project's site |
| `GET /projects/{project}/answers` | Collected answers, newest first (`engine`, `prompt_id`, `mentioned`, `limit`, `skip`) |
| `GET /projects/{project}/answers/{answer_id}` | One answer in full |
| `GET /projects/{project}/alerts` | Recent alerts |

`days` sets how far back to count, ending today (UTC): 28 by default and 1 to 365 on overview, prompts, competitors, sources, claims and next steps; 90 by default on threads and on trend (7 to 730). `detail=full` on overview, prompts, competitors and answers returns every rate per engine instead of the summary.

The API can also change a project (add or pause prompts, track a competitor, mark a next step done, run the audit). Those routes are in the [API documentation](https://sirefs.com/docs/api); this repository only reads.

## Reading the numbers

Every rate is an object: `{"rate": 0.422, "k": 156, "n": 370, "low": 0.205, "high": 0.673}`. `k` answers of `n` named the brand, and `low` to `high` is the 95% interval. Treat rates on fewer than 20 answers as rough, and compare intervals when you decide whether something changed.

In the summaries, pairs such as `"brand_named": [36, 74]` and `"visibility_by_engine": {"chatgpt": [32, 65]}` are `[answers naming the brand, answers]`.

Engines appear by key:

| Key | Engine |
|---|---|
| `chatgpt` | ChatGPT |
| `google_aio` | Google AI Overviews |
| `google_ai_mode` | Google AI Mode |
| `bing_ai_mode` | Bing AI Mode |
| `gemini` | Gemini |
| `perplexity` | Perplexity |
| `claude` | Claude |
| `grok` | Grok |

Each answer also carries its `method`: `ui` for the consumer app, `serp` for the search engine's results page, `api` for the vendor's API.

## Access, limits and prices

| | Starter | Pro | Agency |
|---|---|---|---|
| REST API and MCP server | No | Yes | Yes |
| Projects | 1 | 3 | 25 |
| Active prompts | 25 | 60 | 150 |
| How often each prompt is asked | Weekly | Daily | Daily |
| Price a month | €29 | €99 | €249 |
| Price a month in DKK | 219 | 739 | 1,859 |

Prices exclude VAT. Yearly billing costs ten months. Every plan covers all eight engines; Perplexity, Claude and Grok are asked weekly on every plan. There is no free plan. The first month of a monthly plan is €9 (DKK 69) for a workspace that hasn't had a plan before. See [the prices](https://sirefs.com/pricing), or read them as JSON without a key:

```bash
curl "https://api.sirefs.com/pricing/plans"
```

- **Rate limit:** 120 requests a minute per key, counted together across the REST API and the MCP server. Over the limit you get `429` with `Retry-After: 60`.
- **Reads cost nothing against your plan.** The API returns what is already collected and never asks an AI engine on your behalf.
- **A key stands for the whole workspace** and doesn't expire. A workspace can have up to 20 live keys, so make one per tool and revoke it under Settings, API when the tool is retired.

Errors are HTTP status codes with a JSON body:

| Status | Meaning |
|---|---|
| `401` | No token, or an unknown, expired or revoked one |
| `402` | The workspace's plan doesn't include the API |
| `404` | No such project in the workspace |
| `422` | A bad argument; the body names it |
| `429` | More than 120 requests in a minute |

## Use sirefs from an AI assistant

sirefs has an MCP server at `https://api.sirefs.com/mcp`, so Claude, Cursor, VS Code and other MCP clients can read your numbers and work through the next steps with you. You sign in with OAuth or send the same API key. See [the setup and the tool list](docs/mcp.md).

## Documentation

- [REST API guide](https://sirefs.com/docs/api)
- [Interactive API reference](https://api.sirefs.com/docs) and the [OpenAPI specification](https://api.sirefs.com/openapi.json), under the **v1** tag
- [MCP server guide](https://sirefs.com/docs/mcp)
- [Reading the numbers](https://sirefs.com/docs/reading-the-numbers)
- [Plans and limits](https://sirefs.com/docs/plans-and-limits)

Questions? Write to support@sirefs.com or open an issue here.

## License

The examples are under the [MIT license](LICENSE). Use them as you like.
