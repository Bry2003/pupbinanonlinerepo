<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
    $link = "https"; 
else
    $link = "http"; 
$link .= "://"; 
$link .= $_SERVER['HTTP_HOST']; 
$link .= $_SERVER['REQUEST_URI'];
if(!isset($_SESSION['userdata']) && !strpos($link, 'login.php') && !strpos($link, 'register.php')){
    if(strpos($link, 'adviser/')){
        redirect('adviser/login.php');
    }
	redirect('admin/login.php');
}
if(isset($_SESSION['userdata']) && strpos($link, 'login.php') && !strpos($link, 'adviser/login.php')){
	redirect('admin/index.php');
}
if(isset($_SESSION['userdata']) && strpos($link, 'adviser/login.php')){
	redirect('admin/index.php'); // Or wherever adviser should go
}
$module = array('','admin','admin','student'); // Adviser (2) shares admin folder now? Or should we make sure?
// Adviser is type 2. $module[2] was 'faculty'. But we are using 'admin' folder for adviser UI now?
// Previous task modified admin/home.php etc. so Adviser uses admin panel.
// So module[2] should be 'admin' if we want them to stay in admin panel.

if(isset($_SESSION['userdata']) && (strpos($link, 'index.php') || strpos($link, 'admin/')) && $_SESSION['userdata']['login_type'] !=  1 && $_SESSION['userdata']['login_type'] != 2){
	echo "<script>alert('Access Denied!');location.replace('".base_url.$module[$_SESSION['userdata']['login_type']]."');</script>";
    exit;
}
