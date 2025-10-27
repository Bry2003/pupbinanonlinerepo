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
?>