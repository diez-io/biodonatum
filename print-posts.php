<?php

$jsonFile = __DIR__ . '/seo-meta-output.json';

if (!is_readable($jsonFile)) {
    fwrite(STDERR, "Error: Cannot read file: {$jsonFile}\n");
    exit(1);
}

$raw = file_get_contents($jsonFile);
$data = json_decode($raw, true);

if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    fwrite(STDERR, "Error: Invalid JSON - " . json_last_error_msg() . "\n");
    exit(1);
}

if (!is_array($data)) {
    fwrite(STDERR, "Error: JSON root is not an array\n");
    exit(1);
}

usort($data, function ($a, $b) {
    return strcasecmp($a['post_name'] ?? '', $b['post_name'] ?? '');
});

echo '<pre>';

foreach ($data as $item) {
    $postId = $item['post_id'] ?? '';
    $postName = $item['post_name'] ?? '';
    $postName = is_string($postName) ? urldecode($postName) : (string) $postName;
    echo $postId . ' | ' . $postName . "\n";
}