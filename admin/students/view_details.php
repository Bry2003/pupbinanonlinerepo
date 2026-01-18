
<?php 
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    // Use LEFT JOINs so visitors without department/curriculum still resolve
    $sql = "SELECT s.*, d.name AS department, c.name AS curriculum,
            CONCAT(s.lastname, ', ', s.firstname, ' ', COALESCE(s.middlename, '')) AS fullname
            FROM student_list s
            LEFT JOIN department_list d ON s.department_id = d.id
            LEFT JOIN curriculum_list c ON s.curriculum_id = c.id
            WHERE s.id = '{$_GET['id']}'";
    $user = $conn->query($sql);
    if($user && $user->num_rows > 0){
        foreach($user->fetch_array() as $k => $v){
            $$k = $v;
        }
    }
}
// Prepare a presigned view URL for ID/COR if stored in S3
$id_doc_view_url = isset($id_doc_url) ? $id_doc_url : '';
if(!empty($id_doc_view_url)){
    $path = parse_url($id_doc_view_url, PHP_URL_PATH);
    $path = ltrim($path, '/');
    $prefix = defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : 'Files/';
    if(strpos($path, $prefix) === 0){
        $key = substr($path, strlen($prefix));
        try{
            require_once('../../libs/AwsSdkS3.php');
            $sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, $prefix);
            $id_doc_view_url = $sdk->getPresignedUrl($key, '+15 minutes');
        }catch(Exception $e){
            // ignore presign errors, fallback to original URL
        }
    }
}
$live_face_view_url = isset($live_face_url) ? $live_face_url : '';
if(!empty($live_face_view_url)){
    $path = parse_url($live_face_view_url, PHP_URL_PATH);
    $path = ltrim($path, '/');
    $prefix = defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : 'Files/';
    if(strpos($path, $prefix) === 0){
        $key = substr($path, strlen($prefix));
        try{
            require_once('../../libs/AwsSdkS3.php');
            $sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, $prefix);
            $live_face_view_url = $sdk->getPresignedUrl($key, '+15 minutes');
        }catch(Exception $e){
            // ignore
        }
    }
}
?>
<style>
	#uni_modal .modal-footer{
		display:none
	}
	.student-img{
		object-fit:scale-down;
		object-position:center center;
	}
</style>
<div class="container-fluid">
	<div class="col-md-12">
		<div class="row">
			<div class="col-6">
				<center>
					<img src="<?= validate_image($avatar) ?>" alt="Student Image" class="img-fluid student-img bg-gradient-dark border">
				</center>
			</div>
			<div class="col-6">
				<dl>
					<dt class="text-navy">Student Name:</dt>
                    <dd class="pl-4"><?= isset($fullname) && $fullname ? ucwords($fullname) : '—' ?></dd>
					<dt class="text-navy">Gender:</dt>
                    <dd class="pl-4"><?= isset($gender) && $gender !== null ? ucwords($gender) : '—' ?></dd>
					<dt class="text-navy">Email:</dt>
                    <dd class="pl-4"><?= isset($email) ? $email : '—' ?></dd>
					<dt class="text-navy">Department:</dt>
                    <dd class="pl-4"><?= isset($department) && $department ? ucwords($department) : 'None' ?></dd>
                    <dt class="text-navy">Program:</dt>
                    <dd class="pl-4"><?= isset($curriculum) && $curriculum ? ucwords($curriculum) : 'None' ?></dd>
					<dt class="text-navy">Account Type:</dt>
					<dd class="pl-4"><?= isset($account_type) && $account_type ? ucwords($account_type) : 'Student' ?></dd>
					<dt class="text-navy">System Account Status:</dt>
					<dd class="pl-4">
                        <?php if(isset($status) && $status == 1): ?>
							<span class="badge badge-pill badge-success">Verified</span>
						<?php else: ?>
						<span class="badge badge-pill badge-primary">Not Verified</span>
						<?php endif; ?>
					</dd>
				</dl>
			</div>
		</div>
		<div class="row">
			<div class="col-12 text-right">
				<button class="btn btn-dark btn-flat btn-sm" data-dismiss="modal" type="button"><i class="fa fa-times"></i> Close</button>
			</div>
		</div>
	</div>
</div>
