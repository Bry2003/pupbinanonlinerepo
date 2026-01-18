<?php
// Simple AWS S3 connectivity test for pupbinanonlinerepo
// Usage (Windows XAMPP): C:\xampp\php\php.exe tools\test_s3.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/../initialize.php';
require_once __DIR__.'/../libs/AwsSdkS3.php';

if(!defined('AWS_S3_ENABLE') || !AWS_S3_ENABLE){
    fwrite(STDERR, "AWS_S3_ENABLE is false. Enable S3 in initialize.php then rerun.\n");
    exit(1);
}

$bucket = defined('AWS_S3_BUCKET') ? AWS_S3_BUCKET : '';
$region = defined('AWS_S3_REGION') ? AWS_S3_REGION : 'us-east-1';
$basePrefix = defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : 'Files/';

echo "Configured bucket: {$bucket}\n";
echo "Region: {$region}\n";
echo "Base prefix: {$basePrefix}\n";

$sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, $region, $bucket, $basePrefix);

// Upload a tiny text file under diagnostics to verify PutObject and presign
$relativeKey = 'diagnostics/connectivity.txt'; // Relative to base prefix
$content = "S3 connectivity test from pupbinanonlinerepo at ".date('c')."\n";
$put = $sdk->putObject($relativeKey, $content, 'text/plain', false); // keep private

$logPath = __DIR__.'/../uploads/logs/s3_upload.log';

if(isset($put['ok']) && $put['ok']){
    $fullKey = rtrim($basePrefix, '/').'/'.ltrim($relativeKey, '/');
    echo "Upload OK. Key: {$fullKey}\n";
    $presigned = $sdk->getPresignedUrl($relativeKey, '+10 minutes');
    echo "Presigned URL (valid ~10 minutes):\n{$presigned}\n";
    @file_put_contents($logPath, '['.date('Y-m-d H:i:s')."] TEST upload OK key={$fullKey} url={$presigned}\n", FILE_APPEND);
    exit(0);
}

$err = isset($put['error']) ? $put['error'] : 'unknown error';
echo "Upload FAILED: {$err}\n";
@file_put_contents($logPath, '['.date('Y-m-d H:i:s')."] TEST upload FAIL: {$err}\n", FILE_APPEND);
exit(2);