"""Minimal client for the sirefs REST API. Standard library only, Python 3.8+."""
import json
import os
import sys
import urllib.error
import urllib.parse
import urllib.request

BASE_URL = os.environ.get("SIREFS_API_URL", "https://api.sirefs.com").rstrip("/")


class ApiError(Exception):
    def __init__(self, status, body, retry_after=None):
        super().__init__(f"HTTP {status}: {body}")
        self.status = status
        self.body = body
        self.retry_after = retry_after


def get(path, params=None, api_key=None):
    """GET a path under /v1 and return the decoded JSON.

    Raises ApiError on any non-2xx answer. 401 means the key is missing, unknown or
    revoked, 402 that the workspace's plan doesn't include the API, 404 that the
    project doesn't exist in the workspace, 422 a bad argument, and 429 more than
    120 requests in a minute (retry_after holds the seconds to wait).
    """
    key = api_key if api_key is not None else os.environ.get("SIREFS_API_KEY")
    if not key:
        sys.exit("Set SIREFS_API_KEY to a workspace API key (app.sirefs.com, Settings, API).")
    url = BASE_URL + "/v1" + path
    if params:
        url += "?" + urllib.parse.urlencode(params)
    headers = {"Accept": "application/json", "Authorization": f"Bearer {key}",
               "User-Agent": "sirefs-examples-python"}
    request = urllib.request.Request(url, headers=headers)
    try:
        with urllib.request.urlopen(request, timeout=30) as response:
            return json.load(response)
    except urllib.error.HTTPError as error:
        body = error.read().decode("utf-8", "replace")
        raise ApiError(error.code, body, error.headers.get("Retry-After")) from None


def project_slug():
    """The project from the command line, or the workspace's first project."""
    if len(sys.argv) > 1:
        return sys.argv[1]
    projects = get("/projects")["projects"]
    if not projects:
        sys.exit("This workspace has no projects yet.")
    return projects[0]["slug"]


def percent(value):
    return "n/a" if value is None else f"{value * 100:.1f}%"
