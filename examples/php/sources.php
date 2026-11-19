<?php
// The domains and pages the engines cite when answering the project's prompts.
// `owner` says whose site it is: own, competitor or third_party. A third-party
// site cited often is a place to get mentioned.
// Usage: SIREFS_API_KEY=sir_... php sources.php [project-slug] [days]
require __DIR__ . '/sirefs.php';

$slug = sirefs_project_slug($argv);
$days = (int) ($argv[2] ?? 28);
$data = sirefs_get("/projects/$slug/sources", ['days' => $days]);

printf("Cited sources across %d answers\n\n", $data['answers']);
printf("%8s  %-12s%8s  %s\n", 'Answers', 'Owner', 'Engines', 'Domain');
foreach (array_slice($data['domains'], 0, 15) as $domain) {
    printf("%8d  %-12s%8d  %s\n", $domain['answers'], $domain['owner'], count($domain['engines']), $domain['domain']);
}

echo "\nMost cited pages:\n";
foreach (array_slice($data['pages'], 0, 10) as $page) {
    printf("%8d  %-12s%s\n", $page['answers'], $page['owner'], $page['url']);
}
