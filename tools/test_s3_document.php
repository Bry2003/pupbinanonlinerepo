<?php
// tools/test_s3_document.php
// Purpose: Upload a small PDF to S3 to verify document connectivity,
// then return a presigned URL for quick viewing.

require_once __DIR__.'/../initialize.php';

header('Content-Type: text/plain');

function fail($msg){
    echo "ERROR: $msg\n";
    $logDir = base_app.'uploads/logs';
    if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
    @file_put_contents($logDir.'/s3_upload.log', date('c')." PDF TEST FAIL: $msg\n", FILE_APPEND);
    exit(1);
}

// Basic config checks
if(!defined('AWS_S3_ENABLE') || !AWS_S3_ENABLE) fail('AWS_S3_ENABLE must be true.');
if(!defined('AWS_S3_BUCKET') || !AWS_S3_BUCKET) fail('Missing AWS_S3_BUCKET.');
if(!defined('AWS_ACCESS_KEY_ID') || !AWS_ACCESS_KEY_ID) fail('Missing AWS_ACCESS_KEY_ID.');
if(!defined('AWS_SECRET_ACCESS_KEY') || !AWS_SECRET_ACCESS_KEY) fail('Missing AWS_SECRET_ACCESS_KEY.');
if(!defined('AWS_S3_REGION') || !AWS_S3_REGION) fail('Missing AWS_S3_REGION.');

// Build a tiny valid PDF in-memory (single page, text "S3 Connectivity Test")
$pdfBase64 = 'JVBERi0xLjQKJcTl8uXrp/Og0MTGCjEgMCBvYmoKPDwKL1R5cGUgL1BhZ2UKL1BhcmVudCAzIDAgUgovUmVzb3VyY2VzIDw8IC9Gb250IDw8IC9GMCAyIDAgUiA+PiA+PgovTWVkaWFCb3ggWzAgMCA1OTUgODQyXQo+PgplbmRvYmoKMjAgb2JqCjw8IC9UeXBlIC9Gb250IC9TdWJ0eXBlIC9UeXBlMSAvTmFtZSAvRjAgL0Jhc2VGb250IC9IZWx2ZXRpY2EgL0VuY29kaW5nIC9XaW5BbnNpRW5jb2RpbmcgPj4KZW5kb2JqCjMgMCBvYmoKPDwgL1R5cGUgL1BhZ2VzIC9LaWRzIFsgMSAwIFIgXSAvQ291bnQgMSA+PgplbmRvYmoKNCAwIG9iago8PCAvVHlwZSAvQ2F0YWxvZyAvUGFnZXMgMyAwIFIgPj4KZW5kb2JqCjUgMCBvYmoKPDwgL1R5cGUgL1hPYmplY3QgL1N1YnR5cGUgL1Gb3JtIC9CYXNlU01DUiBbIDAgMCAgXSA+PgplbmRvYmoKMSAwIG9iago8PAovVHlwZSAvUGFnZQovUGFyZW50IDMgMCBSCi9SZXNvdXJjZXMgPDwgL0ZvbnQgPDwgL0YwIDIgMCBSID4+ID4+Ci9NZWRpYUJveCBbMCAwIDU5NSA4NDJdCi9Db250ZW50cyA2IDAgUiA+PgplbmRvYmoKNiAwIG9iago8PAovTGVuZ3RoIDc3ID4+CnN0cmVhbQpCBTAgMTIwIFRECi9GMCBTZiAxMiBURAooUzMgQ29ubmVjdGl2aXR5IFRlc3QpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKc3RhcnR4cmVmCjY5MwolJUVPRgo=';
$pdfData = base64_decode($pdfBase64);
if(!$pdfData) fail('Failed to build test PDF data.');

$prefix = defined('AWS_S3_BASE_PREFIX') ? trim(AWS_S3_BASE_PREFIX, '/') : 'Files';
$key = 'diagnostics/test-document-'.date('Ymd-His').'.pdf';

// Prefer AWS SDK wrapper for presigned URL support
$useSdk = (defined('AWS_S3_USE_SDK') && AWS_S3_USE_SDK);
try {
    if($useSdk){
        require_once __DIR__.'/../libs/AwsSdkS3.php';
        $client = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, $prefix.'/');
        $put = $client->putObject($key, $pdfData, 'application/pdf', false);
        if(!$put || empty($put['ok'])) fail('Upload FAILED: '.($put['error'] ?? 'unknown'));
        $signed = $client->getPresignedUrl($key, '+15 minutes');
        echo "Upload OK to s3://".AWS_S3_BUCKET."/".$prefix."/".$key."\n";
        echo "Presigned URL (15m):\n".$signed."\n";
    } else {
        require_once __DIR__.'/../libs/AwsS3Client.php';
        $client = new AwsS3Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, $prefix.'/');
        $put = $client->putObject($key, $pdfData, 'application/pdf', false);
        if(!$put || empty($put['ok'])) fail('Upload FAILED: '.($put['error'] ?? 'unknown'));
        echo "Upload OK to s3://".AWS_S3_BUCKET."/".$prefix."/".$key."\n";
        echo "Object URL (may require presign if private):\n".$put['url']."\n";
    }

    $logDir = base_app.'uploads/logs';
    if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
    @file_put_contents($logDir.'/s3_upload.log', date('c')." PDF TEST OK: ".$key."\n", FILE_APPEND);
} catch (Exception $e){
    fail('Exception: '.$e->getMessage());
}

?>