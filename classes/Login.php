<?php
require_once '../config.php';
class Login extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function index(){
		echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
	}
	public function login(){
		extract($_POST);

		$qry = $this->conn->query("SELECT * from users where username = '$username' and password = md5('$password') and `type` in (1, 2)");
		if($qry->num_rows > 0){
			$res = $qry->fetch_array();
			if($res['status'] != 1){
				return json_encode(array('status'=>'notverified'));
			}
			foreach($res as $k => $v){
				if(!is_numeric($k) && $k != 'password'){
					$this->settings->set_userdata($k,$v);
				}
			}
			$this->settings->set_userdata('login_type', $res['type']);
		return json_encode(array('status'=>'success'));
		}else{
		return json_encode(array('status'=>'incorrect','last_qry'=>"SELECT * from users where username = '$username' and password = md5('$password') and `type` in (1, 2)"));
		}
	}
	public function login_adviser(){
		extract($_POST);

		$qry = $this->conn->query("SELECT * from users where username = '$username' and password = md5('$password') and `type` in (1, 2)");
		if($qry->num_rows > 0){
			$res = $qry->fetch_array();
			if($res['status'] != 1){
				return json_encode(array('status'=>'notverified'));
			}
			foreach($res as $k => $v){
				if(!is_numeric($k) && $k != 'password'){
					$this->settings->set_userdata($k,$v);
				}
			}
			$this->settings->set_userdata('login_type', $res['type']);
		return json_encode(array('status'=>'success'));
		}else{
		return json_encode(array('status'=>'incorrect','last_qry'=>"SELECT * from users where username = '$username' and password = md5('$password') and `type` in (1, 2)"));
		}
	}
	public function logout(){
		if($this->settings->sess_des()){
			redirect('admin/login.php');
		}
	}
	public function student_login(){
		extract($_POST);
		$username = isset($username) ? trim($username) : '';
		$qry = $this->conn->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as fullname from student_list where username = '{$username}' and `password` = md5('$password') ");
		if($this->conn->error){
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred while fetching data. Error:". $this->conn->error;
		}else{
			if($qry->num_rows > 0){
				$res = $qry->fetch_array();
				foreach($res as $k => $v){
					$this->settings->set_userdata($k,$v);
				}
				$this->settings->set_userdata('login_type',2);
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = "Invalid username or password.";
			}
		}
		return json_encode($resp);
	}
	public function student_forgot_password(){
		extract($_POST);
		$email = isset($email) ? trim($email) : '';
		$resp = [];
		if($email === ''){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Email is required.';
			return json_encode($resp);
		}
		$safe_email = $this->conn->real_escape_string($email);
		$qry = $this->conn->query("SELECT id, email FROM student_list WHERE email = '{$safe_email}' LIMIT 1");
		if($this->conn->error){
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occurred. Please try again later.';
			return json_encode($resp);
		}
		if(!$qry || $qry->num_rows === 0){
			$resp['status'] = 'success';
			$resp['msg'] = 'If this email is registered, a reset link has been sent.';
			return json_encode($resp);
		}
		$row = $qry->fetch_assoc();
		$student_id = (int)$row['id'];
		$tbl = $this->conn->query("SHOW TABLES LIKE 'student_password_resets'");
		if(!$tbl || $tbl->num_rows == 0){
			$sql = "CREATE TABLE `student_password_resets` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `student_id` int(30) NOT NULL,
			  `token` varchar(128) NOT NULL,
			  `expires_at` datetime NOT NULL,
			  `used` tinyint(1) NOT NULL DEFAULT 0,
			  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
			  `date_used` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_token` (`token`),
			  KEY `idx_student` (`student_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
			$mk = $this->conn->query($sql);
			if(!$mk){
				$resp['status'] = 'failed';
				$resp['msg'] = 'Unable to initialize reset system. Please contact the administrator.';
				return json_encode($resp);
			}
		}
		$window_start = date('Y-m-d H:i:s', time() - 900);
		$rate_q = $this->conn->query("SELECT COUNT(*) as cnt FROM student_password_resets WHERE student_id = '{$student_id}' AND date_created >= '{$window_start}'");
		if($rate_q && !$this->conn->error){
			$rate = $rate_q->fetch_assoc();
			if(isset($rate['cnt']) && (int)$rate['cnt'] >= 3){
				$resp['status'] = 'success';
				$resp['msg'] = 'If this email is registered, a reset link has been sent.';
				return json_encode($resp);
			}
		}
		$this->conn->query("UPDATE student_password_resets SET used = 1, date_used = NOW() WHERE student_id = '{$student_id}' AND used = 0");
		$token = bin2hex(random_bytes(32));
		$expires_at = date('Y-m-d H:i:s', time() + 3600);
		$esc_token = $this->conn->real_escape_string($token);
		$esc_expires = $this->conn->real_escape_string($expires_at);
		$ins = $this->conn->query("INSERT INTO student_password_resets (student_id, token, expires_at, used) VALUES ('{$student_id}', '{$esc_token}', '{$esc_expires}', 0)");
		if(!$ins){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Unable to create reset request. Please try again later.';
			return json_encode($resp);
		}
		$reset_link = base_url."reset_password.php?token=".$token;
		$config = [];
		$config_path = dirname(__DIR__).'/email_config.php';
		if(file_exists($config_path)){
			$config = require $config_path;
		}
		$from_email = isset($config['from_email']) ? $config['from_email'] : 'noreply@'.$_SERVER['HTTP_HOST'];
		$from_name = isset($config['from_name']) ? $config['from_name'] : 'PUPBC System';
		$headers = 'From: '.$from_name.' <'.$from_email.'>'."\r\n";
		$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
		$subject = 'PUP Biñan Online Repository - Password Reset';
		$message = "You requested a password reset for your PUP Biñan Online Repository account.\n\n";
		$message .= "Open this link to set a new password (valid for 1 hour):\n{$reset_link}\n\n";
		$message .= "If you did not request this, you can ignore this email.";
		@mail($row['email'], $subject, $message, $headers);
		$resp['status'] = 'success';
		$resp['msg'] = 'If this email is registered, a reset link has been sent.';
		return json_encode($resp);
	}
	public function student_logout(){
		if($this->settings->sess_des()){
			redirect('./');
		}
	}
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'login_adviser':
		echo $auth->login_adviser();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	case 'student_login':
		echo $auth->student_login();
		break;
	case 'student_forgot_password':
		echo $auth->student_forgot_password();
		break;
	case 'student_logout':
		echo $auth->student_logout();
		break;
	default:
		echo $auth->index();
		break;
}

