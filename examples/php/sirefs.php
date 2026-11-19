<?php
// Minimal client for the sirefs REST API. PHP 8.0+ with the curl extension.

class SirefsApiError extends Exception
{
    public function __construct(public int $status, public string $body, public ?string $retryAfter = null)
    {
        parent::__construct("HTTP $status: $body");
    }
}

/**
 * GET a path under /v1 and return the decoded JSON as an array.
 *
 * Throws SirefsApiError on any non-2xx answer: 401 means the key is missing, unknown
 * or revoked, 402 that the workspace's plan doesn't include the API, 404 that the
 * project doesn't exist in the workspace, 422 a bad argument, and 429 more than 120
 * requests in a minute (retryAfter holds the seconds to wait).
 */
function sirefs_get(string $path, array $params = [], ?string $apiKey = null): array
{
    $base = rtrim(getenv('SIREFS_API_URL') ?: 'https://api.sirefs.com', '/');
    $apiKey ??= getenv('SIREFS_API_KEY') ?: null;
    if (!$apiKey) {
        fwrite(STDERR, "Set SIREFS_API_KEY to a workspace API key (app.sirefs.com, Settings, API).\n");
        exit(1);
    }
    $url = $base . '/v1' . $path . ($params ? '?' . http_build_query($params) : '');

    $retryAfter = null;
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Accept: application/json', "Authorization: Bearer $apiKey"],
        CURLOPT_USERAGENT => 'sirefs-examples-php',
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HEADERFUNCTION => function ($curl, $line) use (&$retryAfter) {
            if (stripos($line, 'retry-after:') === 0) {
                $retryAfter = trim(substr($line, 12));
            }
            return strlen($line);
        },
    ]);
    $body = curl_exec($curl);
    if ($body === false) {
        throw new RuntimeException('Request failed: ' . curl_error($curl));
    }
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

    if ($status < 200 || $status >= 300) {
        throw new SirefsApiError($status, $body, $retryAfter);
    }
    return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
}

/** The project from the command line, or the workspace's first project. */
function sirefs_project_slug(array $argv): string
{
    if (isset($argv[1])) {
        return $argv[1];
    }
    $projects = sirefs_get('/projects')['projects'];
    if (!$projects) {
        fwrite(STDERR, "This workspace has no projects yet.\n");
        exit(1);
    }
    return $projects[0]['slug'];
}

function sirefs_percent(?float $value): string
{
    return $value === null ? 'n/a' : number_format($value * 100, 1) . '%';
}
