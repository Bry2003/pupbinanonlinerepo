<?php
require_once(__DIR__.'/../config.php');
Class Users extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function save_users(){
		if(!isset($_POST['status']) && $this->settings->userdata('login_type') == 1){
			$_POST['status'] = 1;
			$_POST['type'] = 2;
		}
		// Enforce Adviser type only (remove Administrator option) - REMOVED
		// if(isset($_POST['type']) && $_POST['type'] == 1){
		// 	$_POST['type'] = 2;
		// }
		extract($_POST);
		$oid = $id;
		$data = '';
		if(isset($oldpassword)){
			if(md5($oldpassword) != $this->settings->userdata('password')){
				return 4;
			}
		}
		$chk = $this->conn->query("SELECT * FROM `users` where username ='{$username}' ".($id>0? " and id!= '{$id}' " : ""))->num_rows;
		if($chk > 0){
			return 3;
			exit;
		}
		foreach($_POST as $k => $v){
			if(in_array($k,array('firstname','middlename','lastname','username','type'))){
				if(!empty($data)) $data .=" , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		if(!empty($password)){
			$password = md5($password);
			if(!empty($data)) $data .=" , ";
			$data .= " `password` = '{$password}' ";
		}

		if(empty($id)){
			$qry = $this->conn->query("INSERT INTO users set {$data}");
			if($qry){
				$id = $this->conn->insert_id;
				$this->settings->set_flashdata('success','User Details successfully saved.');
				$resp['status'] = 1;
			}else{
				$resp['status'] = 2;
			}

		}else{
			$qry = $this->conn->query("UPDATE users set $data where id = {$id}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully updated.');
				if($id == $this->settings->userdata('id')){
					foreach($_POST as $k => $v){
						if($k != 'id'){
							if(!empty($data)) $data .=" , ";
							$this->settings->set_userdata($k,$v);
						}
					}
					
				}
				$resp['status'] = 1;
			}else{
				$resp['status'] = 2;
			}
			
		}
		
		// Upload user avatar to AWS S3 (PNG, 200x200). Fallback to original when GD is unavailable
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
					imagealphablending( $t_image, false );
					imagesavealpha( $t_image, true );
					$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
					imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					if($gdImg){
						ob_start();
						imagepng($t_image);
						$imageData = ob_get_clean();
						imagedestroy($gdImg);
						imagedestroy($t_image);
						$key = AWS_S3_AVATAR_SUBPATH.'user-'.intval($id).'-'.time().'-'.bin2hex(random_bytes(3)).'.png';
						$res = $sdk->putObject($key, $imageData, 'image/png', true);
					}
				}
				if(!$hasGd || !$res || !isset($res['ok']) || !$res['ok']){
					// Fallback: upload original bytes
					$ext = ($type == 'image/png') ? '.png' : '.jpg';
					$mime = $type;
					$key = AWS_S3_AVATAR_SUBPATH.'user-'.intval($id).'-'.time().'-'.bin2hex(random_bytes(3)).$ext;
					$res = $sdk->putObject($key, file_get_contents($upload), $mime, true);
				}
				if(isset($res['ok']) && $res['ok']){
					$url = $res['url'];
					$val = $this->conn->real_escape_string($url.'?v='.(time()));
					$this->conn->query("UPDATE users set `avatar` = '{$val}' where id = '{$id}' ");
					if($id == $this->settings->userdata('id')){
						$this->settings->set_userdata('avatar',$url);
					}
				}else{
					$resp['msg'].=" But Image failed to upload to S3.";
				}
			}
		}
		if(isset($resp['msg']))
		$this->settings->set_flashdata('success',$resp['msg']);
		return  $resp['status'];
	}
	public function delete_users(){
		extract($_POST);
		$avatar = $this->conn->query("SELECT avatar FROM users where id = '{$id}'")->fetch_array()['avatar'];
		$qry = $this->conn->query("DELETE FROM users where id = $id");
		if($qry){
			$avatar = explode("?",$avatar)[0];
			$this->settings->set_flashdata('success','User Details successfully deleted.');
			// Only unlink local files; skip remote URLs
			if(!preg_match('/^https?:\/\//i', $avatar)){
				if(is_file(base_app.$avatar))
					unlink(base_app.$avatar);
			}
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
	public function save_student(){
		extract($_POST);
        $id = isset($id) ? $id : '';
		$data = '';
		// Ensure table exists; create if missing
		$tbl = $this->conn->query("SHOW TABLES LIKE 'student_list'");
		if(!$tbl || $tbl->num_rows == 0){
			 $sql = "CREATE TABLE `student_list` (
			  `id` int(30) NOT NULL AUTO_INCREMENT,
			  `firstname` text NOT NULL,
			  `middlename` text NOT NULL,
			  `lastname` text NOT NULL,
			  `department_id` int(30) NULL,
			  `curriculum_id` int(30) NULL,
			  `email` text NOT NULL,
			  `password` text NOT NULL,
			  `gender` varchar(50) NOT NULL,
			  `status` tinyint(4) NOT NULL DEFAULT 1,
			  `avatar` text NOT NULL DEFAULT '',
			  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
			  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
			$mk = $this->conn->query($sql);
			if(!$mk){
				return json_encode(array(
					"status"=>'failed',
					"msg"=>'Unable to create student_list table. '.$this->conn->error.' If you see "tablespace exists", delete the orphan student_list.ibd from your MySQL data directory and retry.'
				));
			}
		}
		// Ensure new columns exist (account_type, id_doc_url, live_face_url, username)
		$colChk = $this->conn->query("SHOW COLUMNS FROM `student_list` LIKE 'account_type'");
		if(!$colChk || $colChk->num_rows == 0){
			$this->conn->query("ALTER TABLE `student_list` ADD COLUMN `account_type` varchar(20) NULL AFTER `gender`");
		}
		$colIDNo = $this->conn->query("SHOW COLUMNS FROM `student_list` LIKE 'id_number'");
		if(!$colIDNo || $colIDNo->num_rows == 0){
			$this->conn->query("ALTER TABLE `student_list` ADD COLUMN `id_number` varchar(50) NULL AFTER `account_type`");
		}
		$colChk2 = $this->conn->query("SHOW COLUMNS FROM `student_list` LIKE 'id_doc_url'");
		if(!$colChk2 || $colChk2->num_rows == 0){
			$this->conn->query("ALTER TABLE `student_list` ADD COLUMN `id_doc_url` text NULL AFTER `avatar`");
		}
		$colChk3 = $this->conn->query("SHOW COLUMNS FROM `student_list` LIKE 'live_face_url'");
		if(!$colChk3 || $colChk3->num_rows == 0){
			$this->conn->query("ALTER TABLE `student_list` ADD COLUMN `live_face_url` text NULL AFTER `id_doc_url`");
		}
		$usernameCol = $this->conn->query("SHOW COLUMNS FROM `student_list` LIKE 'username'");
		if(!$usernameCol || $usernameCol->num_rows == 0){
			$this->conn->query("ALTER TABLE `student_list` ADD COLUMN `username` varchar(100) NOT NULL DEFAULT '' AFTER `email`");
		}
		if(isset($oldpassword)){
			if(md5($oldpassword) != $this->settings->userdata('password')){
				return json_encode(array("status"=>'failed',
									 "msg"=>'Old Password is Incorrect'));
			}
		}
        if(isset($account_type) && in_array(strtolower($account_type), ['visitor','faculty'])){
			$colDept = $this->conn->query("SHOW COLUMNS FROM `student_list` LIKE 'department_id'");
			if($colDept && $colDept->num_rows){
				$col = $colDept->fetch_assoc();
				if(strtoupper($col['Null']) === 'NO'){
					$this->conn->query("ALTER TABLE `student_list` MODIFY `department_id` int(30) NULL");
				}
			}
			$colCur = $this->conn->query("SHOW COLUMNS FROM `student_list` LIKE 'curriculum_id'");
			if($colCur && $colCur->num_rows){
				$col = $colCur->fetch_assoc();
				if(strtoupper($col['Null']) === 'NO'){
					$this->conn->query("ALTER TABLE `student_list` MODIFY `curriculum_id` int(30) NULL");
				}
			}
			unset($_POST['department_id']);
            unset($_POST['curriculum_id']);
		}
		// Require ID Number to exist in ID Registry (active) - REMOVED per user request
		/*
		$id_number = isset($_POST['id_number']) ? trim($_POST['id_number']) : '';
		if($id_number === ''){
			return json_encode(["status"=>"blocked","msg"=>"ID Number is required to register. You can continue as Guest (view-only)."]);
		}
		$esc_id = $this->conn->real_escape_string($id_number);
		$reg = $this->conn->query("SELECT status FROM `id_registry` WHERE `id_number`='{$esc_id}' LIMIT 1");
		if(!$reg || $reg->num_rows == 0){
			return json_encode(["status"=>"blocked","msg"=>"ID Number not found in ID Registry. Please contact admin or continue as Guest (view-only)."]);
		}
		$regRow = $reg->fetch_assoc();
		if((int)$regRow['status'] !== 1){
			return json_encode(["status"=>"blocked","msg"=>"ID is inactive in ID Registry. Please contact admin or continue as Guest (view-only)."]);
		}
		*/

		$email = isset($email) ? trim($email) : '';
		$username = isset($username) ? trim($username) : '';
		if($username === ''){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Username is required.';
			return json_encode($resp);
		}
		$esc_email = $this->conn->real_escape_string($email);
		$esc_username = $this->conn->real_escape_string($username);
		$chk = $this->conn->query("SELECT * FROM `student_list` where email ='{$esc_email}' ".($id>0? " and id!= '{$id}' " : ""))->num_rows;
		if($chk > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Student Email already exists.';
			return json_encode($resp);
		}
		$chk_user = $this->conn->query("SELECT * FROM `student_list` where username ='{$esc_username}' ".($id>0? " and id!= '{$id}' " : ""))->num_rows;
		if($chk_user > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Username already exists.';
			return json_encode($resp);
		}
		// Handle adviser_id if empty string (unassigned) -> set to NULL
		if(isset($_POST['adviser_id']) && $_POST['adviser_id'] === ''){
			$_POST['adviser_id'] = 'NULL';
		}
		
		foreach($_POST as $k => $v){
			if(!in_array($k,array('id','oldpassword','cpassword','password')) && !is_array($v)){
				if(!empty($data)) $data .=" , ";
				if($k == 'adviser_id' && $v === 'NULL'){
					$data .= " {$k} = NULL ";
				} else {
					$v = $this->conn->real_escape_string($v);
					$data .= " {$k} = '{$v}' ";
				}
			}
		}
		if(!empty($password)){
			$password = md5($password);
			if(!empty($data)) $data .=" , ";
			$data .= " `password` = '{$password}' ";
		}
		
		// Ensure avatar is set (default to empty string if missing)
		if(!isset($_POST['avatar'])){
			if(!empty($data)) $data .=" , ";
			$data .= " `avatar` = '' ";
		}

        if(empty($id)){
			if(!empty($data)) $data .=" , ";
			$data .= " `status` = 1 ";
            $qry = $this->conn->query("INSERT INTO student_list set {$data}");
            if($qry){
                $id = $this->conn->insert_id;
                $this->settings->set_flashdata('success','Student User Details successfully saved.');
                $resp['status'] = "success";
                // Handle optional ID/COR upload to S3
                if(isset($_FILES['id_document']) && is_uploaded_file($_FILES['id_document']['tmp_name'])){
					$upload = $_FILES['id_document']['tmp_name'];
					$type = mime_content_type($upload);
					$allowed = array('image/png','image/jpeg','application/pdf');
					if(in_array($type,$allowed)){
						$ext = '.bin';
						if($type==='image/png') $ext='.png';
						else if($type==='image/jpeg') $ext='.jpg';
						else if($type==='application/pdf') $ext='.pdf';
						try{
							require_once(dirname(__FILE__).'/../libs/AwsSdkS3.php');
							$prefix = defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : 'Files/';
							$sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, $prefix);
							$key = 'ids/student-'.intval($id).'-'.time().'-'.bin2hex(random_bytes(3)).$ext;
							$put = $sdk->putObject($key, file_get_contents($upload), $type, false); // keep private
							if($put && isset($put['ok']) && $put['ok']){
								$url = $put['url'];
								$val = $this->conn->real_escape_string($url.'?v='.(time()));
								$this->conn->query("UPDATE student_list SET `id_doc_url` = '{$val}' WHERE id = '{$id}'");
							}else{
								$resp['msg'] = (isset($resp['msg'])? $resp['msg'].' ' : '').'ID/COR upload failed to S3.';
							}
						}catch(Exception $e){
							$resp['msg'] = (isset($resp['msg'])? $resp['msg'].' ' : '').'ID/COR upload error: '. $e->getMessage();
						}
					}else{
						$resp['msg'] = (isset($resp['msg'])? $resp['msg'].' ' : '').'Invalid ID/COR file type.';
					}
				}
				// Handle optional live face photo upload to S3 (image only)
				if(isset($_FILES['live_face']) && is_uploaded_file($_FILES['live_face']['tmp_name'])){
					$upload = $_FILES['live_face']['tmp_name'];
					$type = mime_content_type($upload);
					$allowed = array('image/png','image/jpeg');
					if(in_array($type,$allowed)){
						$ext = ($type==='image/png')?'.png':'.jpg';
						try{
							require_once(dirname(__FILE__).'/../libs/AwsSdkS3.php');
							$prefix = defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : 'Files/';
							$sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, $prefix);
							$key = 'faces/student-'.intval($id).'-'.time().'-'.bin2hex(random_bytes(3)).$ext;
							$put = $sdk->putObject($key, file_get_contents($upload), $type, false); // keep private
							if($put && isset($put['ok']) && $put['ok']){
								$url = $put['url'];
								$val = $this->conn->real_escape_string($url.'?v='.(time()));
								$this->conn->query("UPDATE student_list SET `live_face_url` = '{$val}' WHERE id = '{$id}'");
							}else{
								$resp['msg'] = (isset($resp['msg'])? $resp['msg'].' ' : '').'Live face upload failed to S3.';
							}
						}catch(Exception $e){
							$resp['msg'] = (isset($resp['msg'])? $resp['msg'].' ' : '').'Live face upload error: '. $e->getMessage();
						}
					}else{
						$resp['msg'] = (isset($resp['msg'])? $resp['msg'].' ' : '').'Invalid live face file type.';
					}
				}
            }else{
                // Retry strategy for broken AUTO_INCREMENT on some MySQL setups
                $err = $this->conn->error;
                if(stripos($err, 'Failed to read auto-increment value') !== false){
                    // Attempt to repair table definition and reinsert
                    $this->conn->query("ALTER TABLE `student_list` MODIFY COLUMN `id` int(30) NOT NULL AUTO_INCREMENT");
                    $this->conn->query("ALTER TABLE `student_list` ENGINE=InnoDB");
                    // Re-attempt insert
                    $retry = $this->conn->query("INSERT INTO student_list set {$data}");
                    if(!$retry){
                        // As a final fallback, manually compute next id and insert explicitly
                        $res = $this->conn->query("SELECT IFNULL(MAX(id),0)+1 AS next_id FROM student_list");
                        $nextId = 1;
                        if($res && $row = $res->fetch_assoc()) $nextId = intval($row['next_id']);
                        $retry2 = $this->conn->query("INSERT INTO student_list set id = {$nextId}, {$data}");
                        if($retry2){
                            $id = $nextId;
                            $this->settings->set_flashdata('success','Student User Details successfully saved.');
                            $resp['status'] = "success";
                        }else{
                            $resp['status'] = "failed";
                            $resp['msg'] = "An error occurred while saving the data after auto-increment repair. Error: ". $this->conn->error;
                        }
                    }else{
                        $id = $this->conn->insert_id;
                        $this->settings->set_flashdata('success','Student User Details successfully saved.');
                        $resp['status'] = "success";
                    }
                }else{
                    $resp['status'] = "failed";
                    $resp['msg'] = "An error occurred while saving the data. Error: ". $err;
                }
            }

		}else{
			$qry = $this->conn->query("UPDATE student_list set $data where id = {$id}");
			if($qry){
				$this->settings->set_flashdata('success','Student User Details successfully updated.');
				if($id == $this->settings->userdata('id')){
					foreach($_POST as $k => $v){
						if($k != 'id'){
							if(!empty($data)) $data .=" , ";
							$this->settings->set_userdata($k,$v);
						}
					}
					
				}
				$resp['status'] = "success";
			}else{
				$resp['status'] = "failed";
				$resp['msg'] = "An error occurred while saving the data. Error: ". $this->conn->error;
			}
			
		}
		
		// Upload student avatar to AWS S3 (PNG, 200x200). Fallback to original when GD is unavailable
		// Allow updating live face photo on profile update as well
		if(isset($_FILES['live_face']) && is_uploaded_file($_FILES['live_face']['tmp_name']) && isset($id)){
			$upload = $_FILES['live_face']['tmp_name'];
			$type = mime_content_type($upload);
			$allowed = array('image/png','image/jpeg');
			if(in_array($type,$allowed)){
				$ext = ($type==='image/png')?'.png':'.jpg';
				try{
					require_once(dirname(__FILE__).'/../libs/AwsSdkS3.php');
					$prefix = defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : 'Files/';
					$sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, $prefix);
					$key = 'faces/student-'.intval($id).'-'.time().'-'.bin2hex(random_bytes(3)).$ext;
					$put = $sdk->putObject($key, file_get_contents($upload), $type, false);
					if($put && isset($put['ok']) && $put['ok']){
						$url = $put['url'];
						$val = $this->conn->real_escape_string($url.'?v='.(time()));
						$this->conn->query("UPDATE student_list SET `live_face_url` = '{$val}' WHERE id = '{$id}'");
					}else{
						$resp['msg'] = (isset($resp['msg'])? $resp['msg'].' ' : '').'Live face upload failed to S3.';
					}
				}catch(Exception $e){
					$resp['msg'] = (isset($resp['msg'])? $resp['msg'].' ' : '').'Live face upload error: '. $e->getMessage();
				}
			}else{
				$resp['msg'] = (isset($resp['msg'])? $resp['msg'].' ' : '').'Invalid live face file type.';
			}
		}
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
					imagealphablending( $t_image, false );
					imagesavealpha( $t_image, true );
					$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
					imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					if($gdImg){
						ob_start();
						imagepng($t_image);
						$imageData = ob_get_clean();
						imagedestroy($gdImg);
						imagedestroy($t_image);
						$key = AWS_S3_AVATAR_SUBPATH.'student-'.intval($id).'-'.time().'-'.bin2hex(random_bytes(3)).'.png';
						$res = $sdk->putObject($key, $imageData, 'image/png', true);
					}
				}
				if(!$hasGd || !$res || !isset($res['ok']) || !$res['ok']){
					// Fallback: upload original bytes
					$ext = ($type == 'image/png') ? '.png' : '.jpg';
					$mime = $type;
					$key = AWS_S3_AVATAR_SUBPATH.'student-'.intval($id).'-'.time().'-'.bin2hex(random_bytes(3)).$ext;
					$res = $sdk->putObject($key, file_get_contents($upload), $mime, true);
				}
				if(isset($res['ok']) && $res['ok']){
					$url = $res['url'];
					$this->conn->query("UPDATE student_list set `avatar` = CONCAT('{$url}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$id}' ");
					if($id == $this->settings->userdata('id')){
						$this->settings->set_userdata('avatar',$url);
					}
				}else{
					$resp['msg'].=" But Image failed to upload to S3.";
				}
			}
		}
        
        if(isset($resp['status']) && $resp['status'] === 'success' && isset($id)){
            if(isset($id_number) && !empty($id_number)){
                $reg = $this->conn->query("SHOW TABLES LIKE 'id_registry'");
                if($reg && $reg->num_rows > 0){
                    $safeIdNum = $this->conn->real_escape_string($id_number);
                    $chkReg = $this->conn->query("SELECT * FROM `id_registry` WHERE `id_number` = '{$safeIdNum}' AND `status` = 1");
                    if($chkReg && $chkReg->num_rows > 0){
                        $this->conn->query("UPDATE `student_list` SET `status` = 1 WHERE id = '".intval($id)."'");
                        $resp['auto_login'] = true;
                    }
                }
            }
        }
        return  json_encode($resp);
	}
	public function delete_student(){
		extract($_POST);
		$avatar = $this->conn->query("SELECT avatar FROM student_list where id = '{$id}'")->fetch_array()['avatar'];
		$qry = $this->conn->query("DELETE FROM student_list where id = $id");
		if($qry){
			$avatar = explode("?",$avatar)[0];
			$this->settings->set_flashdata('success','Student User Details successfully deleted.');
			// Only unlink local files; skip remote URLs
			if(!preg_match('/^https?:\/\//i', $avatar)){
				if(is_file(base_app.$avatar))
					unlink(base_app.$avatar);
			}
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
	public function verify_student(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `student_list` set `status` = 1 where id = $id");
		if($update){
			$this->settings->set_flashdata('success','Student Account has verified successfully.');
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
	
}

$users = new users();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save':
		echo $users->save_users();
	break;
	case 'delete':
		echo $users->delete_users();
	break;
	case 'save_student':
		echo $users->save_student();
	break;
	case 'delete_student':
		echo $users->delete_student();
	break;
	case 'verify_student':
		echo $users->verify_student();
	break;
	default:
		// echo $sysset->index();
		break;
}
