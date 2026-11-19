<?php
// The project's prompts (the questions asked of the engines) with their results.
// For each prompt: how many answers name the brand, in total and per engine.
// Prompts where the brand is never named are where to act.
// Usage: SIREFS_API_KEY=sir_... php prompts.php [project-slug] [days]
require __DIR__ . '/sirefs.php';

$slug = sirefs_project_slug($argv);
$days = (int) ($argv[2] ?? 28);
$data = sirefs_get("/projects/$slug/prompts", ['days' => $days]);

$share = fn (array $prompt) => $prompt['brand_named'][0] / max($prompt['brand_named'][1], 1);
$prompts = $data['prompts'];
usort($prompts, fn ($a, $b) => $share($a) <=> $share($b));

printf("Prompts, %s to %s. Brand named in k of n answers.\n", $data['window']['start'], $data['window']['end']);
foreach ($prompts as $prompt) {
    [$named, $answers] = $prompt['brand_named'];
    printf("\n%3d of %-4d %s  [%s, %s]%s\n", $named, $answers, $prompt['text'], $prompt['market'],
        $prompt['topic'] ?? 'no topic', $prompt['active'] ? '' : '  (paused)');
    $perEngine = [];
    foreach ($prompt['by_engine'] as $engine => [$k, $n]) {
        $perEngine[] = "$engine $k/$n";
    }
    if ($perEngine) {
        echo '            ' . implode(', ', $perEngine) . "\n";
    }
}
