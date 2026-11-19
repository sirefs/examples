// Minimal client for the sirefs REST API. No dependencies, Node 18+ (built-in fetch).
const BASE_URL = (process.env.SIREFS_API_URL ?? "https://api.sirefs.com").replace(/\/+$/, "");

export class ApiError extends Error {
  constructor(status, body, retryAfter) {
    super(`HTTP ${status}: ${body}`);
    this.status = status;
    this.body = body;
    this.retryAfter = retryAfter;
  }
}

// GET a path under /v1 and return the decoded JSON. Throws ApiError on any non-2xx
// answer: 401 means the key is missing, unknown or revoked, 402 that the workspace's
// plan doesn't include the API, 404 that the project doesn't exist in the workspace,
// 422 a bad argument, and 429 more than 120 requests in a minute (retryAfter holds
// the seconds to wait).
export async function get(path, params = {}, apiKey = process.env.SIREFS_API_KEY) {
  if (!apiKey) {
    console.error("Set SIREFS_API_KEY to a workspace API key (app.sirefs.com, Settings, API).");
    process.exit(1);
  }
  const url = new URL(`${BASE_URL}/v1${path}`);
  for (const [name, value] of Object.entries(params)) url.searchParams.set(name, value);

  const response = await fetch(url, {
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${apiKey}`,
      "User-Agent": "sirefs-examples-node",
    },
  });
  if (!response.ok) {
    throw new ApiError(response.status, await response.text(), response.headers.get("retry-after"));
  }
  return response.json();
}

// The project from the command line, or the workspace's first project.
export async function projectSlug() {
  if (process.argv[2]) return process.argv[2];
  const { projects } = await get("/projects");
  if (!projects.length) {
    console.error("This workspace has no projects yet.");
    process.exit(1);
  }
  return projects[0].slug;
}

export const percent = (value) => (value == null ? "n/a" : `${(value * 100).toFixed(1)}%`);
