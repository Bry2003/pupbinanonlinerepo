<?php
// Migrate existing local banner/document files to AWS S3 and update DB paths
// Usage (Windows XAMPP): C:\xampp\php\php.exe tools\migrate_archives_to_s3.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/../initialize.php';
require_once __DIR__.'/../classes/DBConnection.php';
require_once __DIR__.'/../libs/AwsSdkS3.php';

if(!defined('AWS_S3_ENABLE') || !AWS_S3_ENABLE){
    fwrite(STDERR, "AWS_S3_ENABLE is false. Enable S3 then rerun.\n");
    exit(1);
}

$db = new DBConnection();
$conn = $db->conn;
if(!$conn){
    fwrite(STDERR, "DB connection failed.\n");
    exit(1);
}

$sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);

$dryRun = isset($argv[1]) && strtolower($argv[1]) === '--dry-run';
echo $dryRun ? "[DRY RUN] " : "";
echo "Starting migration of archive_list banner/document files to S3...\n";

$res = $conn->query("SELECT id, banner_path, document_path FROM archive_list ORDER BY id ASC");
$migrated = 0; $skipped = 0; $failed = 0;

function isRemote($url){
    return preg_match('/^https?:\/\//i', $url);
}

while($row = $res->fetch_assoc()){
    $id = (int)$row['id'];
    $banner = $row['banner_path'] ?? '';
    $doc = $row['document_path'] ?? '';
    $bannerBase = $banner ? explode('?', $banner)[0] : '';
    $docBase = $doc ? explode('?', $doc)[0] : '';

    $didAnything = false;

    // Migrate banner
    if($bannerBase && !isRemote($bannerBase)){
        $localBannerPath = base_app . ltrim($bannerBase, '/');
        if(is_file($localBannerPath)){
            $content = file_get_contents($localBannerPath);
            $key = 'banners/'.basename($localBannerPath);
            echo "[#{$id}] Upload banner -> {$key}... ";
            if(!$dryRun){
                $put = $sdk->putObject($key, $content, mime_content_type($localBannerPath) ?: 'image/png', true);
                if(isset($put['ok']) && $put['ok']){
                    $url = $put['url'];
                    $conn->query("UPDATE archive_list SET banner_path = CONCAT('".$conn->real_escape_string($url)."','?v=',unix_timestamp(CURRENT_TIMESTAMP)) WHERE id = '{$id}'");
                    echo "OK\n";
                    $didAnything = true;
                } else {
                    echo "FAIL: ".($put['error'] ?? 'unknown')."\n";
                    $failed++;
                }
            } else {
                echo "SKIPPED (dry-run)\n";
            }
        } else {
            echo "[#{$id}] Banner missing locally: {$localBannerPath}\n";
        }
    }

    // Migrate document
    if($docBase && !isRemote($docBase)){
        $localDocPath = base_app . ltrim($docBase, '/');
        if(is_file($localDocPath)){
            $content = file_get_contents($localDocPath);
            $key = 'documents/'.basename($localDocPath);
            echo "[#{$id}] Upload document -> {$key}... ";
            if(!$dryRun){
                $put = $sdk->putObject($key, $content, 'application/pdf', true);
                if(isset($put['ok']) && $put['ok']){
                    $url = $put['url'];
                    $conn->query("UPDATE archive_list SET document_path = CONCAT('".$conn->real_escape_string($url)."','?v=',unix_timestamp(CURRENT_TIMESTAMP)) WHERE id = '{$id}'");
                    echo "OK\n";
                    $didAnything = true;
                } else {
                    echo "FAIL: ".($put['error'] ?? 'unknown')."\n";
                    $failed++;
                }
            } else {
                echo "SKIPPED (dry-run)\n";
            }
        } else {
            echo "[#{$id}] Document missing locally: {$localDocPath}\n";
        }
    }

    if(!$didAnything){ $skipped++; }
    else { $migrated++; }
}

echo "\nMigration complete. Migrated: {$migrated}, Skipped: {$skipped}, Failed: {$failed}.\n";
echo "Tip: run again without --dry-run to apply changes.\n";

?>