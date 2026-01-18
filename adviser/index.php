<?php
require_once('../config.php');
if(isset($_SESSION['userdata'])){
    redirect('admin/index.php');
} else {
    redirect('adviser/login.php');
}
?>