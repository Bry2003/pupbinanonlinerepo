<?php
// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dev_data = array('id'=>'-1','firstname'=>'Developer','lastname'=>'','username'=>'dev_oretnom','password'=>'5da283a2d990e8d8512cf967df5bc0d0','last_login'=>'','date_updated'=>'','date_added'=>'');

// Compute base_app
$base_app_path = str_replace('\\','/',__DIR__).'/';

// Compute dynamic base_url with optional APP_BASE_URL override
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$doc_root = str_replace('\\','/', realpath($_SERVER['DOCUMENT_ROOT'] ?? __DIR__));
$relative = '/';
if($doc_root && strpos($base_app_path, $doc_root) === 0){
    $relative = substr($base_app_path, strlen($doc_root));
    $relative = '/'.trim($relative, '/').'/';
    if($relative === '//' ) $relative = '/';
}
$computed_base_url = $scheme.'://'.$host.$relative;
$env_base_url = getenv('APP_BASE_URL');
$final_base_url = $env_base_url ? rtrim($env_base_url,'/').'/' : $computed_base_url;

if(!defined('base_url')) define('base_url', $final_base_url);
if(!defined('base_app')) define('base_app', $base_app_path );
if(!defined('dev_data')) define('dev_data',$dev_data);

// Load production configuration if exists (e.g. for InfinityFree)
// This file should define DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME to override defaults
if(file_exists(__DIR__.'/production_config.php')){
    require_once(__DIR__.'/production_config.php');
}

// Database config via environment with local defaults
if(!defined('DB_SERVER')) define('DB_SERVER', getenv('DB_SERVER') ?: 'localhost');
if(!defined('DB_USERNAME')) define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
if(!defined('DB_PASSWORD')) define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
if(!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'pupbinanonlinerepo_db');

// IPFS/Pinata config via environment
if(!defined('PINATA_JWT')) define('PINATA_JWT', getenv('PINATA_JWT') ?: '');
if(!defined('PINATA_API_KEY')) define('PINATA_API_KEY', getenv('PINATA_API_KEY') ?: '');
if(!defined('PINATA_API_SECRET')) define('PINATA_API_SECRET', getenv('PINATA_API_SECRET') ?: '');
if(!defined('PINATA_GATEWAY_TOKEN')) define('PINATA_GATEWAY_TOKEN', getenv('PINATA_GATEWAY_TOKEN') ?: '');
if(!defined('PINATA_GATEWAY_URL')) define('PINATA_GATEWAY_URL', rtrim(getenv('PINATA_GATEWAY_URL') ?: '', '/').'/');

// Supabase Storage (S3-compatible) via environment
if(!defined('SUPABASE_PROJECT_REF')) define('SUPABASE_PROJECT_REF', getenv('SUPABASE_PROJECT_REF') ?: '');
if(!defined('SUPABASE_S3_BUCKET')) define('SUPABASE_S3_BUCKET', getenv('SUPABASE_S3_BUCKET') ?: '');
if(!defined('SUPABASE_S3_ACCESS_KEY_ID')) define('SUPABASE_S3_ACCESS_KEY_ID', getenv('SUPABASE_S3_ACCESS_KEY_ID') ?: '');
if(!defined('SUPABASE_S3_SECRET_ACCESS_KEY')) define('SUPABASE_S3_SECRET_ACCESS_KEY', getenv('SUPABASE_S3_SECRET_ACCESS_KEY') ?: '');
if(!defined('SUPABASE_S3_REGION')) define('SUPABASE_S3_REGION', getenv('SUPABASE_S3_REGION') ?: 'us-east-1');
if(!defined('SUPABASE_S3_ENDPOINT')) {
  $pr = getenv('SUPABASE_PROJECT_REF') ?: '';
  $computed = $pr ? ('https://'.$pr.'.supabase.co/storage/v1/s3') : '';
  define('SUPABASE_S3_ENDPOINT', getenv('SUPABASE_S3_ENDPOINT') ?: $computed);
}
if(!defined('SUPABASE_S3_ENABLE')) define('SUPABASE_S3_ENABLE', getenv('SUPABASE_S3_ENABLE') ? (strtolower(getenv('SUPABASE_S3_ENABLE'))==='true') : false);
// AWS S3 (native): enforce direct S3 uploads for banners and PDFs
// IMPORTANT: Force correct bucket/region here to avoid mismatched environment overrides.
if(!defined('AWS_S3_ENABLE')) define('AWS_S3_ENABLE', true);
if(!defined('AWS_S3_BUCKET')) define('AWS_S3_BUCKET', 'filestoredintel');
// NOTE: In production, prefer environment variables for credentials.
if(!defined('AWS_ACCESS_KEY_ID')) define('AWS_ACCESS_KEY_ID', getenv('AWS_ACCESS_KEY_ID') ?: 'AKIAZIAABXY5X2FOY76Y');
if(!defined('AWS_SECRET_ACCESS_KEY')) define('AWS_SECRET_ACCESS_KEY', getenv('AWS_SECRET_ACCESS_KEY') ?: '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');
if(!defined('AWS_S3_REGION')) define('AWS_S3_REGION', 'us-east-1');
if(!defined('AWS_S3_BASE_PREFIX')) define('AWS_S3_BASE_PREFIX', 'Files/');
// Per‑feature S3 subpaths (can override via environment)
if(!defined('AWS_S3_LOGO_SUBPATH')) define('AWS_S3_LOGO_SUBPATH', getenv('AWS_S3_LOGO_SUBPATH') ?: 'system/');
if(!defined('AWS_S3_COVER_SUBPATH')) define('AWS_S3_COVER_SUBPATH', getenv('AWS_S3_COVER_SUBPATH') ?: 'system/');
if(!defined('AWS_S3_AVATAR_SUBPATH')) define('AWS_S3_AVATAR_SUBPATH', getenv('AWS_S3_AVATAR_SUBPATH') ?: 'avatars/');
// Require S3; if upload to S3 fails, treat as error instead of local fallback
if(!defined('AWS_S3_REQUIRE')) define('AWS_S3_REQUIRE', true);
// Prefer using official AWS SDK
if(!defined('AWS_S3_USE_SDK')) define('AWS_S3_USE_SDK', true);
?>
