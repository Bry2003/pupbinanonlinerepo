<?php
// Quick S3 connectivity test for your environment.
// Usage: Visit this file in the browser, or run: php aws_s3_smoke_test.php

// Load app bootstrap to get AWS_* constants.
require_once __DIR__ . '/initialize.php';
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain');

function fail($msg){
    http_response_code(500);
    echo "ERROR: $msg\n";
    exit(1);
}

// Basic config checks
$missing = [];
if(!defined('AWS_S3_ENABLE') || !AWS_S3_ENABLE) $missing[] = 'AWS_S3_ENABLE';
if(!defined('AWS_S3_BUCKET') || !AWS_S3_BUCKET) $missing[] = 'AWS_S3_BUCKET';
if(!defined('AWS_ACCESS_KEY_ID') || !AWS_ACCESS_KEY_ID) $missing[] = 'AWS_ACCESS_KEY_ID';
if(!defined('AWS_SECRET_ACCESS_KEY') || !AWS_SECRET_ACCESS_KEY) $missing[] = 'AWS_SECRET_ACCESS_KEY';
if(!defined('AWS_S3_REGION') || !AWS_S3_REGION) $missing[] = 'AWS_S3_REGION';

if(!empty($missing)){
    fail('Incomplete S3 config: ' . implode(',', $missing));
}

$bucket = AWS_S3_BUCKET;
// Key is relative; classes add AWS_S3_BASE_PREFIX automatically
$relKey = 'smoke-test/' . date('Ymd-His') . '-' . mt_rand(1000,9999) . '.txt';
$body = 'S3 smoke test OK at ' . date('c');

// Choose client
$useSdk = defined('AWS_S3_USE_SDK') && AWS_S3_USE_SDK;
try{
    if($useSdk){
        require_once __DIR__ . '/libs/AwsSdkS3.php';
        $s3 = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
        $res = $s3->putObject($relKey, $body, 'text/plain', true);
        if(!(is_array($res) && !empty($res['ok']))){
            fail('Upload failed using AwsSdkS3: ' . (is_array($res) && isset($res['error']) ? $res['error'] : 'unknown error'));
        }
        $url = $res['url'] ?? null;
    } else {
        require_once __DIR__ . '/libs/AwsS3Client.php';
        $client = new AwsS3Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
        $res = $client->putObject($relKey, $body, 'text/plain', true);
        if(!(is_array($res) && !empty($res['ok']))){
            fail('Upload failed using AwsS3Client: ' . (is_array($res) && isset($res['response']) ? $res['response'] : 'unknown error'));
        }
        $url = $res['url'] ?? null;
    }
} catch(Throwable $e){
    fail('Upload error: ' . $e->getMessage());
}

echo "Upload succeeded.\n";
echo "Bucket: $bucket\n";
echo "Key:    $relKey\n";
echo "URL:    " . ($url ?: '(none)') . "\n";

// Try presign (if SDK available)
try{
    if($useSdk){
        // Generate a short-lived presigned URL for validation
        $presigned = $s3->getPresignedUrl($relKey, '+5 minutes');
        echo "Presigned: " . (string)$presigned . "\n";
    }
} catch(Throwable $e){
    echo "Presign error (non-fatal): " . $e->getMessage() . "\n";
}

echo "Done.\n";