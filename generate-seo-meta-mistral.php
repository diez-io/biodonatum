<?php

set_time_limit( 0 );

$dir = __DIR__;
$sourceFile = $dir . '/seo-meta-output.json';
$outputFile = $dir . '/generated-seo-meta.json';

$apiKey = '';
if ($apiKey === false || $apiKey === '') {
    fwrite(STDERR, "Error: Set MISTRAL_API_KEY environment variable.\n");
    exit(1);
}

const TEXT_MAX_LEN = 10000;
const SAVE_EVERY = 5;
const DELAY_SECONDS = 2;

if (!is_readable($sourceFile)) {
    fwrite(STDERR, "Error: Cannot read file: {$sourceFile}\n");
    exit(1);
}

$raw = file_get_contents($sourceFile);
$source = json_decode($raw, true);

if ($source === null && json_last_error() !== JSON_ERROR_NONE) {
    fwrite(STDERR, "Error: Invalid JSON - " . json_last_error_msg() . "\n");
    exit(1);
}

if (!is_array($source)) {
    fwrite(STDERR, "Error: JSON root is not an array\n");
    exit(1);
}

$sourceIndexByPostId = [];
foreach ($source as $i => $item) {
    $pid = $item['post_id'] ?? null;
    if ($pid !== null) {
        $sourceIndexByPostId[$pid] = $i;
    }
}

$output = [];
if (is_readable($outputFile)) {
    $savedRaw = file_get_contents($outputFile);
    $saved = json_decode($savedRaw, true);
    if (is_array($saved)) {
        if (count($saved) === count($source)) {
            $output = $saved;
        } else {
            $output = $source;
            foreach ($saved as $item) {
                $pid = $item['post_id'] ?? null;
                if (isset($sourceIndexByPostId[$pid])) {
                    $output[$sourceIndexByPostId[$pid]] = $item;
                }
            }
        }
        echo "Resumed from {$outputFile}, " . count(array_filter($output, function ($o) { return isset($o['generated']); })) . " already processed.\n";
    } else {
        $output = $source;
    }
} else {
    $output = $source;
}

function saveOutput(array $output, string $path): void
{
    $json = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false || file_put_contents($path, $json) === false) {
        fwrite(STDERR, "Error: Failed to write {$path}\n");
    }
}

function extractJsonFromContent(string $content): ?array
{
    $content = trim($content);
    if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $content, $m)) {
        $content = trim($m[1]);
    }
    $decoded = json_decode($content, true);
    if (is_array($decoded)) {
        return $decoded;
    }
    $decoded = json_decode($content, true);
    return is_array($decoded) ? $decoded : null;
}

function requestMistral(string $apiKey, string $userContent): array
{
    $url = 'https://api.mistral.ai/v1/chat/completions';
    $body = [
        'model' => 'mistral-small-latest',
        'messages' => [
            ['role' => 'user', 'content' => $userContent],
        ],
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_TIMEOUT => 120,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($response === false || $httpCode < 200 || $httpCode >= 300) {
            return ['error' => 'HTTP ' . $httpCode . ' or curl error', 'raw' => $response];
        }
    } else {
        $ctx = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAccept: application/json\r\nAuthorization: Bearer " . $apiKey . "\r\n",
                'content' => json_encode($body),
                'timeout' => 120,
            ],
        ]);
        $response = @file_get_contents($url, false, $ctx);
        if ($response === false) {
            return ['error' => 'HTTP request failed', 'raw' => null];
        }
    }

    $data = json_decode($response, true);
    if (!is_array($data) || empty($data['choices'][0]['message']['content'])) {
        return ['error' => 'Invalid API response', 'raw' => $response];
    }

    $content = $data['choices'][0]['message']['content'];
    $parsed = extractJsonFromContent($content);
    if ($parsed !== null) {
        return $parsed;
    }
    return ['error' => 'Response is not valid JSON', 'raw' => $content];
}

$promptPrefix = "Я делаю SEO для сайта. Мне нужно из контента страницы выудить title, description и keywords. Вот контент страницы:\n\n";
$promptSuffix = "\n\nОтвет предоставь в виде json {title: '', description: '', keywords: ''}.";

$requestCount = 0;
$total = count($source);

for ($i = 0; $i < $total; $i++) {
    if (isset($output[$i]['generated'])) {
        continue;
    }

    $item = $source[$i];
    $text = $item['text'] ?? '';
    if (!is_string($text)) {
        $text = '';
    }
    $text = mb_substr($text, 0, TEXT_MAX_LEN);
    $userContent = $promptPrefix . $text . $promptSuffix;

    $postId = $item['post_id'] ?? $i;
    echo "Request " . ($requestCount + 1) . " / post_id={$postId} (index {$i})... ";

    $result = requestMistral($apiKey, $userContent);

    if (isset($result['error'])) {
        echo "ERROR: " . $result['error'] . "\n";
        $output[$i]['generated'] = $result;
    } else {
        echo "OK\n";
        $output[$i]['generated'] = $result;
    }

    $requestCount++;

    if ($requestCount % SAVE_EVERY === 0) {
        saveOutput($output, $outputFile);
        echo "Saved after {$requestCount} requests.\n";
    }

    if ($i < $total - 1) {
        sleep(DELAY_SECONDS);
    }
}

saveOutput($output, $outputFile);
echo "Done. Total requests: {$requestCount}, output written to {$outputFile}\n";
