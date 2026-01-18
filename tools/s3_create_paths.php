<?php
// Helper script to create S3 "folder" markers for logo, cover, and avatars
require_once __DIR__.'/../initialize.php';
require_once __DIR__.'/../libs/AwsSdkS3.php';

function ensure_path($sdk, $relativePrefix){
    $relativePrefix = trim($relativePrefix, '/').'/';
    // Create a marker file to make the prefix visible in consoles
    $key = $relativePrefix.'.keep';
    // Avoid ACLs to satisfy buckets with Block Public ACLs enabled
    $res = $sdk->putObject($key, '1', 'text/plain', false);
    if(isset($res['ok']) && $res['ok']){
        echo "Created: ".$res['url'].PHP_EOL;
    } else {
        echo "Failed: ".$relativePrefix." => ".($res['error'] ?? 'unknown').PHP_EOL;
    }
}

$sdk = new AwsSdkS3(
    AWS_ACCESS_KEY_ID,
    AWS_SECRET_ACCESS_KEY,
    AWS_S3_REGION,
    AWS_S3_BUCKET,
    defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : 'Files/'
);

echo "Initializing S3 paths in bucket '".AWS_S3_BUCKET."'...".PHP_EOL;
ensure_path($sdk, AWS_S3_LOGO_SUBPATH);
ensure_path($sdk, AWS_S3_COVER_SUBPATH);
ensure_path($sdk, AWS_S3_AVATAR_SUBPATH);
echo "Done.".PHP_EOL;
?>