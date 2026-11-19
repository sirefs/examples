<?php
// A project's numbers: the brand against its tracked competitors.
// Visibility is the share of answers that name the brand. Every rate comes with
// its count (k of n) and a 95% interval.
// Usage: SIREFS_API_KEY=sir_... php overview.php [project-slug] [days]
require __DIR__ . '/sirefs.php';

$slug = sirefs_project_slug($argv);
$days = (int) ($argv[2] ?? 28);
$data = sirefs_get("/projects/$slug/overview", ['days' => $days]);

$window = $data['window'];
printf("%s: %s to %s (%d days)\n\n", $data['project']['name'], $window['start'], $window['end'], $window['days']);
printf("%-16s%8s%12s%18s%8s%13s%10s%16s\n",
    'Brand', 'Answers', 'Visibility', '95% interval', 'Cited', 'Recommended', 'Avg rank', 'Share of voice');
foreach ($data['entities'] as $entity) {
    $v = $entity['visibility'];
    printf("%-16s%8d%12s%18s%8s%13s%10s%16s\n",
        $entity['name'],
        $entity['answers'],
        sirefs_percent($v['rate'] ?? null),
        $v ? sirefs_percent($v['low']) . ' to ' . sirefs_percent($v['high']) : 'n/a',
        sirefs_percent($entity['citation_rate']['rate'] ?? null),
        sirefs_percent($entity['recommendation_rate']['rate'] ?? null),
        $entity['avg_rank'] === null ? 'n/a' : number_format($entity['avg_rank'], 2),
        sirefs_percent($entity['share_of_voice']));
}

foreach ($data['entities'] as $entity) {
    if ($entity['entity'] !== 'brand') {
        continue;
    }
    printf("\n%s per engine (answers naming it of answers collected):\n", $entity['name']);
    foreach ($entity['visibility_by_engine'] as $engine => [$named, $answers]) {
        printf("  %-16s%4d of %d\n", $engine, $named, $answers);
    }
}
