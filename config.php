<?php
ob_start();
ini_set('date.timezone','Asia/Manila');
date_default_timezone_set('Asia/Manila');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Load core configuration and classes using absolute paths
require_once(__DIR__.'/initialize.php');
if(!file_exists(__DIR__.'/classes/DBConnection.php')){
    die("Missing classes/DBConnection.php. Ensure the 'classes' directory exists at ".__DIR__."/classes");
}
if(!file_exists(__DIR__.'/classes/SystemSettings.php')){
    die("Missing classes/SystemSettings.php. Ensure the 'classes' directory exists at ".__DIR__."/classes");
}
require_once(__DIR__.'/classes/DBConnection.php');
require_once(__DIR__.'/classes/SystemSettings.php');

$db = new DBConnection();
$conn = $db->conn;
$_settings = new SystemSettings();
$_settings->load_system_info();

if(!function_exists('redirect')){
    function redirect($url=''){
        if(!empty($url))
        echo '<script>location.href="'.base_url.$url.'"</script>';
    }
}
if(!function_exists('validate_image')){
    function validate_image($file){
    if(!empty($file)){
        $ex = explode('?', $file);
        $file = $ex[0];
        $param = isset($ex[1]) ? '?'.$ex[1] : '';

        // Remote URLs: prefer presigned S3 when applicable
        if(preg_match('/^https?:\/\//i', $file)){
            if(defined('AWS_S3_ENABLE') && AWS_S3_ENABLE && defined('AWS_S3_BUCKET')){
                $bucket = preg_quote(AWS_S3_BUCKET, '/');
                $region = defined('AWS_S3_REGION') ? preg_quote(strtolower(AWS_S3_REGION), '/') : '([a-z0-9\-]+)';

                // Support multiple S3 URL styles
                $patterns = [
                    // Virtual-hosted with region
                    '/https?:\/\/'.$bucket.'\.s3\.'.$region.'\.amazonaws\.com\/(.+)$/i',
                    // Virtual-hosted without region
                    '/https?:\/\/'.$bucket.'\.s3\.amazonaws\.com\/(.+)$/i',
                    // Path-style with region
                    '/https?:\/\/?s3\.'.$region.'\.amazonaws\.com\/'.$bucket.'\/(.+)$/i',
                    // Path-style global
                    '/https?:\/\/?s3\.amazonaws\.com\/'.$bucket.'\/(.+)$/i',
                    // Legacy dashed region style
                    '/https?:\/\/'.$bucket.'\.s3\-'.$region.'\.amazonaws\.com\/(.+)$/i',
                ];
                $matchedKey = null;
                foreach($patterns as $p){
                    if(preg_match($p, $file, $m)){
                        $matchedKey = ltrim($m[1], '/');
                        break;
                    }
                }
                if($matchedKey !== null && defined('AWS_S3_USE_SDK') && AWS_S3_USE_SDK){
                    require_once(__DIR__.'/libs/AwsSdkS3.php');
                    try{
                        // Strip base prefix if it is already included in the URL key
                        $basePrefix = defined('AWS_S3_BASE_PREFIX') ? trim(AWS_S3_BASE_PREFIX, '/') : '';
                        if($basePrefix && (strpos($matchedKey, $basePrefix.'/') === 0)){
                            $matchedKey = substr($matchedKey, strlen($basePrefix)+1);
                        }
                        $sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : '');
                        $signed = $sdk->getPresignedUrl($matchedKey, '+15 minutes');
                        return $signed; // presigned URLs already include query params
                    }catch(Exception $e){
                        // Fall through to returning the original URL
                    }
                }
            }
            // Return original remote URL if not S3 or presign not possible
            return $file.$param;
        }

        // Local file path
        if(is_file(base_app.$file)){
            return base_url.$file.$param;
        }
        // Fallback placeholder
        return base_url.'dist/img/no-image-available.png';
    }
    // Empty input fallback
    return base_url.'dist/img/no-image-available.png';
}
}
function isMobileDevice(){
    $aMobileUA = array(
        '/iphone/i' => 'iPhone', 
        '/ipod/i' => 'iPod', 
        '/ipad/i' => 'iPad', 
        '/android/i' => 'Android', 
        '/blackberry/i' => 'BlackBerry', 
        '/webos/i' => 'Mobile'
    );

    //Return true if Mobile User Agent is detected
    foreach($aMobileUA as $sMobileKey => $sMobileOS){
        if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
            return true;
        }
    }
    //Otherwise return false..  
    return false;
}
ob_end_flush();
?>
