<?php
if(!class_exists('DBConnection')){
	// Use absolute path for includes to avoid path issues
	$dir = dirname(__FILE__);
	require_once($dir . '/../initialize.php');
	require_once($dir . '/DBConnection.php');
}
class SystemSettings extends DBConnection{
	public function __construct(){
		parent::__construct();
		// Initialize session if not already started
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
	}
	function check_connection(){
		return($this->conn);
	}
	function load_system_info(){
		// Initialize system_info array if not set
		if(!isset($_SESSION['system_info'])){
			$_SESSION['system_info'] = array();
		}
		
		$sql = "SELECT * FROM system_info";
		$qry = $this->conn->query($sql);
		
		if($qry) {
			while($row = $qry->fetch_assoc()){
				$_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
			}
			return true;
		} else {
			return false;
		}
	}
	function update_system_info(){
		// Initialize system_info array if not set
		if(!isset($_SESSION['system_info'])){
			$_SESSION['system_info'] = array();
		}
		
		$sql = "SELECT * FROM system_info";
		$qry = $this->conn->query($sql);
		
		if($qry) {
			while($row = $qry->fetch_assoc()){
				$_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
			}
			return true;
		} else {
			return false;
		}
	}
	function update_settings_info(){
		$data = "";
		$resp = array('status'=>'success');
		
		foreach ($_POST as $key => $value) {
			if(!in_array($key,array("content"))) {
				// Sanitize input to prevent SQL injection
				$key = $this->conn->real_escape_string($key);
				$value = $this->conn->real_escape_string($value);
				
				if(isset($_SESSION['system_info'][$key])){
					$qry = $this->conn->query("UPDATE system_info SET meta_value = '{$value}' WHERE meta_field = '{$key}'");
				} else {
					$qry = $this->conn->query("INSERT INTO system_info SET meta_value = '{$value}', meta_field = '{$key}'");
				}
				
				if(!$qry) {
					$resp['status'] = 'failed';
					$resp['error'] = $this->conn->error;
				}
			}
		}
		if(isset($_POST['content'])) {
			foreach($_POST['content'] as $k => $v){
				// Sanitize filename to prevent directory traversal attacks
				$k = preg_replace('/[^a-zA-Z0-9_-]/', '', $k);
				$file_path = dirname(__FILE__) . "/../{$k}.html";
				
				// Write content to file
				if(file_put_contents($file_path, $v) === false) {
					$resp['status'] = 'failed';
					$resp['error'] = "Failed to write to file {$k}.html";
				}
			}
		}
		
		// Upload system logo to AWS S3 (PNG, 200x200). Fallback to original when GD is unavailable
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$upload = $_FILES['img']['tmp_name'];
			$type = mime_content_type($upload);
			$allowed = array('image/png','image/jpeg');
			if(!in_array($type,$allowed)){
				$resp['msg'].=" But Image failed to upload due to invalid file type.";
			}else{
				$hasGd = function_exists('imagecreatetruecolor') && function_exists('imagepng') && function_exists('imagecopyresampled');
				require_once(dirname(__FILE__).'/../libs/AwsSdkS3.php');
				$sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : 'Files/');
				$res = null;
				if($hasGd){
					$new_height = 200; 
					$new_width = 200; 
					list($width, $height) = getimagesize($upload);
					$t_image = imagecreatetruecolor($new_width, $new_height);
					imagealphablending($t_image, false);
					imagesavealpha($t_image, true);
					$gdImg = ($type == 'image/png') ? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
					imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					if($gdImg){
						ob_start();
						imagepng($t_image);
						$imageData = ob_get_clean();
						imagedestroy($gdImg);
						imagedestroy($t_image);
						$key = AWS_S3_LOGO_SUBPATH.'logo-'.time().'-'.bin2hex(random_bytes(4)).'.png';
						$res = $sdk->putObject($key, $imageData, 'image/png', true);
					}
				}
				if(!$hasGd || !$res || !isset($res['ok']) || !$res['ok']){
					// Fallback: upload original bytes with appropriate extension/MIME
					$ext = ($type == 'image/png') ? '.png' : '.jpg';
					$mime = $type;
					$key = AWS_S3_LOGO_SUBPATH.'logo-'.time().'-'.bin2hex(random_bytes(4)).$ext;
					$res = $sdk->putObject($key, file_get_contents($upload), $mime, true);
				}
				if(isset($res['ok']) && $res['ok']){
					$url = $res['url'];
					$val = $url.'?v='.(time());
					if(isset($_SESSION['system_info']['logo'])){
						$qry = $this->conn->query("UPDATE system_info set meta_value = '{$this->conn->real_escape_string($val)}' where meta_field = 'logo' ");
					}else{
						$qry = $this->conn->query("INSERT into system_info set meta_value = '{$this->conn->real_escape_string($val)}',meta_field = 'logo' ");
					}
				}else{
					$resp['status'] = 'failed';
					$resp['error'] = isset($res['error']) ? $res['error'] : 'S3 upload failed for logo';
				}
			}
		}
		// Upload system cover to AWS S3 (images resized, others original)
		if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != ''){
			$upload = $_FILES['cover']['tmp_name'];
			$type = mime_content_type($upload);
			$hasGd = function_exists('imagecreatetruecolor') && function_exists('imagepng') && function_exists('imagecopyresampled');
			require_once(dirname(__FILE__).'/../libs/AwsSdkS3.php');
			$sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : 'Files/');
			$res = null;
			if($hasGd && ($type === 'image/png' || $type === 'image/jpeg')){
				$new_height = 720;
				$new_width = 1280;
				list($width, $height) = getimagesize($upload);
				$t_image = imagecreatetruecolor($new_width, $new_height);
				imagealphablending($t_image, false);
				imagesavealpha($t_image, true);
				$gdImg = ($type == 'image/png') ? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
				imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				if($gdImg){
					ob_start();
					imagepng($t_image);
					$imageData = ob_get_clean();
					imagedestroy($gdImg);
					imagedestroy($t_image);
					$key = AWS_S3_COVER_SUBPATH.'cover-'.time().'-'.bin2hex(random_bytes(4)).'.png';
					$res = $sdk->putObject($key, $imageData, 'image/png', true);
				}
			}
			if(!$res || !isset($res['ok']) || !$res['ok']){
				$origName = isset($_FILES['cover']['name']) ? $_FILES['cover']['name'] : '';
				$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
				if(!$ext){
					if($type === 'image/png') $ext = 'png';
					elseif($type === 'image/jpeg') $ext = 'jpg';
					elseif($type === 'image/gif') $ext = 'gif';
					elseif($type === 'video/mp4') $ext = 'mp4';
					elseif($type === 'video/webm') $ext = 'webm';
					elseif($type === 'video/ogg') $ext = 'ogg';
				}
				$mime = $type ? $type : 'application/octet-stream';
				$key = AWS_S3_COVER_SUBPATH.'cover-'.time().'-'.bin2hex(random_bytes(4)).($ext ? ('.'.$ext) : '');
				$res = $sdk->putObject($key, file_get_contents($upload), $mime, true);
			}
			if(isset($res['ok']) && $res['ok']){
				$url = $res['url'];
				$val = $url.'?v='.(time());
				if(isset($_SESSION['system_info']['cover'])){
					$qry = $this->conn->query("UPDATE system_info set meta_value = '{$this->conn->real_escape_string($val)}' where meta_field = 'cover' ");
				}else{
					$qry = $this->conn->query("INSERT into system_info set meta_value = '{$this->conn->real_escape_string($val)}',meta_field = 'cover' ");
				}
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = isset($res['error']) ? $res['error'] : 'S3 upload failed for cover';
			}
		}
		
		$update = $this->update_system_info();
		
		if($resp['status'] == 'success' && $update) {
			$flash = $this->set_flashdata('success','System Info Successfully Updated.');
			return true;
		} else {
			$this->set_flashdata('error', isset($resp['error']) ? $resp['error'] : 'Failed to update system info.');
			return false;
		}
	}
	function upload_media(){
		$resp = ['status'=>'failed'];
		if(!isset($_FILES['file']) || empty($_FILES['file']['tmp_name'])){
			if(isset($_FILES['media'])) $_FILES['file'] = $_FILES['media'];
		}
		if(!isset($_FILES['file']) || empty($_FILES['file']['tmp_name'])){
			return json_encode($resp);
		}
		$tmp = $_FILES['file']['tmp_name'];
		$name = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : 'upload';
		$type = function_exists('mime_content_type') ? @mime_content_type($tmp) : '';
		$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
		if(!$ext){
			if($type === 'image/png') $ext = 'png';
			elseif($type === 'image/jpeg') $ext = 'jpg';
			elseif($type === 'image/gif') $ext = 'gif';
			elseif($type === 'video/mp4') $ext = 'mp4';
			elseif($type === 'video/webm') $ext = 'webm';
			elseif($type === 'video/ogg') $ext = 'ogg';
		}
		$sub = defined('AWS_S3_COVER_SUBPATH') ? AWS_S3_COVER_SUBPATH : 'system/';
		$key = rtrim($sub,'/').'/welcome/'.date('Ymd-His').'-'.bin2hex(random_bytes(8)).($ext?'.'.$ext:'');
		$mime = $type ?: 'application/octet-stream';
		$url = '';
		if(defined('AWS_S3_USE_SDK') && AWS_S3_USE_SDK){
			require_once dirname(__FILE__).'/../libs/AwsSdkS3.php';
			$sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : '');
			$res = $sdk->putObject($key, file_get_contents($tmp), $mime, true);
			if($res && isset($res['ok']) && $res['ok']) $url = $res['url'];
		}else{
			require_once dirname(__FILE__).'/../libs/AwsS3Client.php';
			$client = new AwsS3Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : '');
			$res = $client->putObject($key, file_get_contents($tmp), $mime, true);
			if($res && isset($res['ok']) && $res['ok']) $url = $res['url'];
		}
		if($url){
			$resp = ['status'=>'success','url'=>$url];
		}
		return json_encode($resp);
	}
	function set_userdata($field='',$value=''){
		if(!empty($field) && !empty($value)){
			$_SESSION['userdata'][$field]= $value;
		}
	}
	function userdata($field = ''){
		if(!empty($field)){
			if(isset($_SESSION['userdata'][$field]))
				return $_SESSION['userdata'][$field];
			else
				return null;
		}else{
			return false;
		}
	}
	function set_flashdata($flash='',$value=''){
		if(!empty($flash) && !empty($value)){
			$_SESSION['flashdata'][$flash]= $value;
		return true;
		}
	}
	function chk_flashdata($flash = ''){
		if(isset($_SESSION['flashdata'][$flash])){
			return true;
		}else{
			return false;
		}
	}
	function flashdata($flash = ''){
	if(!empty($flash) && isset($_SESSION['flashdata'][$flash])){
		$_tmp = $_SESSION['flashdata'][$flash];
		unset($_SESSION['flashdata'][$flash]);
		return $_tmp;
	}else{
		return false;
	}
}
	function sess_des(){
	if(isset($_SESSION['userdata'])){
		unset($_SESSION['userdata']);
	}
	return true;
}
	function info($field=''){
		if(!empty($field)){
			if(isset($_SESSION['system_info'][$field]))
				return $_SESSION['system_info'][$field];
			else
				return false;
		}else{
			return false;
		}
	}
	function set_info($field='',$value=''){
		if(!empty($field) && !empty($value)){
			$_SESSION['system_info'][$field] = $value;
		}
	}
}
$_settings = new SystemSettings();
$_settings->load_system_info();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'update_settings':
		echo $sysset->update_settings_info();
		break;
	case 'upload_media':
		echo $sysset->upload_media();
		break;
	default:
		// echo $sysset->index();
		break;
}
?>
