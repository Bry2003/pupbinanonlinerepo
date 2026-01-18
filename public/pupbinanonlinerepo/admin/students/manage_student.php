<?php 
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `student_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="container-fluid">
	<form action="" id="student-form">
		<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="form-group">
			<label for="firstname" class="control-label">First Name</label>
			<input type="text" name="firstname" id="firstname" class="form-control form-control-sm" required value="<?php echo isset($firstname) ? $firstname : '' ?>">
		</div>
        <div class="form-group">
			<label for="middlename" class="control-label">Middle Name</label>
			<input type="text" name="middlename" id="middlename" class="form-control form-control-sm" value="<?php echo isset($middlename) ? $middlename : '' ?>">
		</div>
		<div class="form-group">
			<label for="lastname" class="control-label">Last Name</label>
			<input type="text" name="lastname" id="lastname" class="form-control form-control-sm" required value="<?php echo isset($lastname) ? $lastname : '' ?>">
		</div>
        <div class="form-group">
            <label for="gender" class="control-label">Gender</label>
            <select name="gender" id="gender" class="custom-select custom-select-sm" required>
                <option <?php echo isset($gender) && $gender == 'Male' ? 'selected' : '' ?>>Male</option>
                <option <?php echo isset($gender) && $gender == 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="department_id" class="control-label">Program</label>
            <select name="department_id" id="department_id" class="custom-select custom-select-sm select2" required>
                <option value="" disabled <?php echo !isset($department_id) ? "selected" : "" ?>></option>
                <?php 
                $department = $conn->query("SELECT * FROM `department_list` where status = 1 order by `name` asc");
                while($row = $department->fetch_assoc()):
                ?>
                <option value="<?= $row['id'] ?>" <?php echo isset($department_id) && $department_id == $row['id'] ? 'selected' : '' ?>><?= ucwords($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <?php if($_settings->userdata('login_type') == 1): ?>
        <div class="form-group">
            <label for="adviser_id" class="control-label">Adviser</label>
            <select name="adviser_id" id="adviser_id" class="custom-select custom-select-sm select2">
                <option value="" <?php echo !isset($adviser_id) ? "selected" : "" ?>>Unassigned</option>
                <?php 
                $adviser_qry = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) as fullname FROM `users` where type = 2 order by fullname asc");
                while($row = $adviser_qry->fetch_assoc()):
                ?>
                <option value="<?= $row['id'] ?>" <?php echo isset($adviser_id) && $adviser_id == $row['id'] ? 'selected' : '' ?>><?= ucwords($row['fullname']) ?></option>
                <?php endwhile; ?>
            </select>
            </div>
        <?php else: ?>
            <?php if(isset($adviser_id)): ?>
            <div class="form-group">
                <label class="control-label">Adviser</label>
                <?php 
                $adviser_name_qry = $conn->query("SELECT CONCAT(firstname, ' ', lastname) as fullname FROM `users` where id = '{$adviser_id}'");
                $adviser_name = ($adviser_name_qry->num_rows > 0) ? $adviser_name_qry->fetch_assoc()['fullname'] : 'Unknown';
                ?>
                <input type="text" class="form-control form-control-sm" value="<?= ucwords($adviser_name) ?>" readonly>
            </div>
            <?php else: ?>
                <div class="form-group">
                    <label class="control-label">Adviser</label>
                    <input type="text" class="form-control form-control-sm" value="<?php echo $_settings->userdata('firstname').' '.$_settings->userdata('lastname') ?>" readonly>
                    <input type="hidden" name="adviser_id" value="<?php echo $_settings->userdata('id') ?>">
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="form-group">
			<label for="id_number" class="control-label">ID Number</label>
			<input type="text" name="id_number" id="id_number" class="form-control form-control-sm" value="<?php echo isset($id_number) ? $id_number : '' ?>">
		</div>
		<div class="form-group">
			<label for="email" class="control-label">Email</label>
			<input type="email" name="email" id="email" class="form-control form-control-sm" required value="<?php echo isset($email) ? $email : '' ?>">
		</div>
		<div class="form-group">
			<label for="password" class="control-label">Password</label>
			<input type="password" name="password" id="password" class="form-control form-control-sm" <?php echo !isset($id) ? "required" : "" ?>>
            <?php if(isset($id)): ?>
                <small class="text-muted">Leave blank if you don't want to change it.</small>
            <?php endif; ?>
		</div>
        <div class="form-group">
            <label for="status" class="control-label">Status</label>
            <select name="status" id="status" class="custom-select custom-select-sm" required>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Not Verified</option>
                <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Verified</option>
            </select>
        </div>
	</form>
</div>
<script>
	$(document).ready(function(){
        $('.select2').select2({
            placeholder:"Select here",
            width: "100%"
        })
		$('#student-form').submit(function(e){
			e.preventDefault();
			var _this = $(this)
			$('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Users.php?f=save_student",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
				dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.reload();
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                    }else if(resp.status == 'blocked' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                    }else{
						alert_toast("An error occured",'error');
						console.log(resp)
					}
					end_loader();
				}
			})
		})
	})
</script>