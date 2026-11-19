<?php
// What to do about the numbers, ranked by impact, each step with its evidence.
// Usage: SIREFS_API_KEY=sir_... php next-steps.php [project-slug] [days]
require __DIR__ . '/sirefs.php';

$slug = sirefs_project_slug($argv);
$days = (int) ($argv[2] ?? 28);
$data = sirefs_get("/projects/$slug/next-steps", ['days' => $days]);

if (!$data['steps']) {
    echo "No open steps.\n";
}
foreach ($data['steps'] as $index => $step) {
    printf("%d. [%s] %s\n", $index + 1, $step['impact'], $step['title']);
    printf("   %s\n", $step['detail']);
    printf("   kind: %s, id: %s\n", $step['kind'], $step['id']);
    if (!empty($step['link'])) {
        printf("   %s: %s\n", $step['link']['label'], $step['link']['href']);
    }
    echo "\n";
}
