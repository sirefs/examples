<?php
// List the workspace's projects with the brand's visibility over the last 28 days.
// Usage: SIREFS_API_KEY=sir_... php projects.php
require __DIR__ . '/sirefs.php';

$data = sirefs_get('/projects');
printf("Workspace: %s (%s plan)\n", $data['workspace']['name'], $data['workspace']['plan']);

foreach ($data['projects'] as $project) {
    echo "\n";
    printf("%s  (slug: %s, site: %s)\n", $project['name'], $project['slug'], $project['domain']);
    printf("  Competitors:    %s\n", implode(', ', $project['competitors']) ?: 'none');
    printf("  Engines:        %s\n", implode(', ', $project['engines']));
    printf("  Active prompts: %d\n", $project['prompts_active']);
    $v = $project['visibility_28d'];
    if ($v) {
        printf("  Visibility:     %s (named in %d of %d answers, 95%% interval %s to %s)\n",
            sirefs_percent($v['rate']), $v['k'], $v['n'], sirefs_percent($v['low']), sirefs_percent($v['high']));
    } else {
        echo "  Visibility:     no answers yet\n";
    }
}
