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
		
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = 'uploads/logo-'.(time()).'.png';
			$dir_path = base_app . $fname;
			// Create directory if it doesn't exist
			$upload_dir = dirname(base_app . $fname);
			if(!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}
			$upload = $_FILES['img']['tmp_name'];
			$type = mime_content_type($upload);
			$allowed = array('image/png','image/jpeg');
			if(!in_array($type,$allowed)){
				$resp['msg'].=" But Image failed to upload due to invalid file type.";
			}else{
				$new_height = 200; 
				$new_width = 200; 
		
				list($width, $height) = getimagesize($upload);
				$t_image = imagecreatetruecolor($new_width, $new_height);
				imagealphablending($t_image, false);
				imagesavealpha($t_image, true);
				$gdImg = null;
				if($type == 'image/png') {
					$gdImg = imagecreatefrompng($upload);
				} elseif($type == 'image/jpeg') {
					$gdImg = imagecreatefromjpeg($upload);
				}
				imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				if($gdImg){
						if(is_file($dir_path))
						unlink($dir_path);
						$uploaded_img = imagepng($t_image,$dir_path);
						imagedestroy($gdImg);
						imagedestroy($t_image);
				}else{
				$resp['msg'].=" But Image failed to upload due to unkown reason.";
				}
			}
			if(isset($uploaded_img) && $uploaded_img == true){
				if(isset($_SESSION['system_info']['logo'])){
					$qry = $this->conn->query("UPDATE system_info set meta_value = '{$fname}' where meta_field = 'logo' ");
					if(is_file(base_app.$_SESSION['system_info']['logo'])) unlink(base_app.$_SESSION['system_info']['logo']);
				}else{
					$qry = $this->conn->query("INSERT into system_info set meta_value = '{$fname}',meta_field = 'logo' ");
				}
				unset($uploaded_img);
			}
		}
		if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != ''){
			$fname = 'uploads/cover-'.time().'.png';
			$dir_path = base_app . $fname;
			// Create directory if it doesn't exist
			$upload_dir = dirname(base_app . $fname);
			if(!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}
			$upload = $_FILES['cover']['tmp_name'];
			$type = mime_content_type($upload);
			$allowed = array('image/png','image/jpeg');
			if(!in_array($type,$allowed)){
				$resp['msg'].=" But Image failed to upload due to invalid file type.";
			}else{
				$new_height = 720; 
				$new_width = 1280; 
		
				list($width, $height) = getimagesize($upload);
				$t_image = imagecreatetruecolor($new_width, $new_height);
				imagealphablending($t_image, false);
				imagesavealpha($t_image, true);
				$gdImg = null;
				if($type == 'image/png') {
					$gdImg = imagecreatefrompng($upload);
				} elseif($type == 'image/jpeg') {
					$gdImg = imagecreatefromjpeg($upload);
				}
				imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				if($gdImg){
						if(is_file($dir_path))
						unlink($dir_path);
						$uploaded_img = imagepng($t_image,$dir_path);
						imagedestroy($gdImg);
						imagedestroy($t_image);
				}else{
				$resp['msg'].=" But Image failed to upload due to unkown reason.";
				}
			}
			if(isset($uploaded_img) && $uploaded_img == true){
				if(isset($_SESSION['system_info']['cover'])){
					$qry = $this->conn->query("UPDATE system_info set meta_value = '{$fname}' where meta_field = 'cover' ");
					if(is_file(base_app.$_SESSION['system_info']['cover'])) unlink(base_app.$_SESSION['system_info']['cover']);
				}else{
					$qry = $this->conn->query("INSERT into system_info set meta_value = '{$fname}',meta_field = 'cover' ");
				}
				unset($uploaded_img);
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
	default:
		// echo $sysset->index();
		break;
}
?>