<?php 
$user = $conn->query("SELECT s.*, d.name as program, CONCAT(lastname,', ',firstname,' ',middlename) as fullname FROM student_list s LEFT JOIN department_list d ON s.department_id = d.id WHERE s.id = '{$_settings->userdata('id')}'");
if($user && $user->num_rows > 0){
    $res = $user->fetch_array();
    foreach($res as $k =>$v){ if(!is_numeric($k)) $$k = $v; }
}else{
    $firstname = $_settings->userdata('firstname');
    $middlename = $_settings->userdata('middlename');
    $lastname = $_settings->userdata('lastname');
    $fullname = trim(($lastname? $lastname: '').', '.($firstname? $firstname: '').' '.($middlename? $middlename: ''));
    $gender = $_settings->userdata('gender');
    $email = $_settings->userdata('email');
    $avatar = $_settings->userdata('avatar');
    $id_number = $_settings->userdata('id_number');
    $program = '';
}
?>
<style>
    .student-img{
		object-fit:scale-down;
		object-position:center center;
        height:200px;
        width:200px;
	}
</style>
<div class="content py-4">
    <div class="card card-outline card-primary shadow rounded-0">
        <div class="card-header rounded-0">
            <h5 class="card-title">Your Information:</h5>
            <div class="card-tools">
                <a href="./?page=my_archives" class="btn btn-default bg-primary btn-flat"><i class="fa fa-archive"></i> My Archives</a>
                <a href="./?page=manage_account" class="btn btn-default bg-navy btn-flat"><i class="fa fa-edit"></i> Update Account</a>
            </div>
        </div>
        <div class="card-body rounded-0">
            <div class="container-fluid">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-lg-4 col-sm-12">
                            <center>
                                <img src="<?= validate_image(isset($avatar)? $avatar : '') ?>" alt="Student Image" class="img-fluid student-img bg-gradient-dark border">
                            </center>
                        </div>
                        <div class="col-lg-8 col-sm-12">
                            <dl>
                                <dt class="text-navy">Student Name:</dt>
                                <dd class="pl-4"><?= isset($fullname) ? ucwords($fullname) : '' ?></dd>
                                <dt class="text-navy">Gender:</dt>
                                <dd class="pl-4"><?= isset($gender) ? ucwords($gender) : '' ?></dd>
                                <dt class="text-navy">Email:</dt>
                                <dd class="pl-4"><?= isset($email) ? $email : '' ?></dd>
                                <dt class="text-navy">ID Number:</dt>
                                <dd class="pl-4"><?= isset($id_number) ? $id_number : 'N/A' ?></dd>
                                <dt class="text-navy">Program:</dt>
                                <dd class="pl-4"><?= isset($program) ? ucwords($program) : '' ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
