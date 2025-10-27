<?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition" style="height: 100%;">
  <script>
    start_loader()
  </script>
  <link rel="stylesheet" href="./dist/css/pup-theme.css">
  <style>
    html, body{
      height:100% !important;
      width:100% !important;
      margin: 0;
      padding: 0;
    }
  </style>
<div class="pup-login-container">
    <div class="pup-login-left" style="background-image: url('uploads/cover-163884028.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; width: 80%; max-width: 500px; text-align: center;">
            <img src="<?= validate_image($_settings->info('logo')) ?>" alt="PUP Logo" class="pup-logo">
            <h1 class="pup-login-title">User Registration</h1>
        </div>
    </div>
    
    <div class="pup-login-right">
        <div class="pup-welcome-content">
            <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); font-weight: 700;">Registration</h2>
            <div class="pup-login-card" style="max-width: 600px;">
                <div class="pup-login-header">
                    <h3 style="font-size: 1.8rem; margin: 1rem 0; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); font-weight: 600;">Create an Account</h3>
                </div>
                <div class="text-right mb-3">
                    <a href="./" class="btn btn-sm btn-success"><i class="fa fa-home"></i> Back to Homepage</a>
                </div>
                <form action="" id="registration-form">
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="firstname" id="firstname" autofocus placeholder="Firstname" class="pup-form-control" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="middlename" id="middlename" placeholder="Middlename (optional)" class="pup-form-control">
                            </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input type="text" name="lastname" id="lastname" placeholder="Lastname" class="pup-form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-auto">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="genderMale" name="gender" value="Male" required checked>
                                    <label for="genderMale" class="custom-control-label">Male</label>
                                </div>
                            </div>
                            <div class="form-group col-auto">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="genderFemale" name="gender" value="Female">
                                    <label for="genderFemale" class="custom-control-label">Female</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <span class="text-navy"><small>Department</small></span>
                                    <select name="department_id" id="department_id" class="pup-form-control select2" data-placeholder="Select Here Department" required>
                                        <option value="" disabled></option>
                                        <?php 
                                        $department = $conn->query("SELECT * FROM `department_list` where status = 1 order by `name` asc");
                                        while($row = $department->fetch_assoc()):
                                        ?>
                                        <option value="<?= $row['id'] ?>"><?= ucwords($row['name']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <span class="text-navy"><small>Curriculum</small></span>
                                    <select name="curriculum_id" id="curriculum_id" class="pup-form-control select2" data-placeholder="Select Here Curriculum" required>
                                        <option value="" disabled selected>Select Department First</option>
                                        <?php 
                                        $curriculum = $conn->query("SELECT * FROM `curriculum_list` where status = 1 order by `name` asc");
                                        $cur_arr = [];
                                        while($row = $curriculum->fetch_assoc()){
                                            $row['name'] = ucwords($row['name']);
                                            $cur_arr[$row['department_id']][] = $row;
                                        }
                                        // Add default options for testing
                                        foreach($cur_arr as $dept_id => $curricula) {
                                            foreach($curricula as $curr) {
                                                echo "<option value='{$curr['id']}' data-department='{$dept_id}' style='display:none;'>{$curr['name']}</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <input type="email" name="email" id="email" placeholder="Email" class="pup-form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <input type="password" name="password" id="password" placeholder="Password" class="pup-form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <input type="password" id="cpassword" placeholder="Confirm Password" class="pup-form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group d-flex justify-content-between align-items-center">
                                    <a href="pup-login.php" class="pup-btn" style="background-color: #6c757d; border-color: #6c757d;"><i class="fas fa-arrow-left mr-2"></i>Back</a>
                                    <button class="pup-btn"> Register</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- Select2 -->
<script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>

<script>
    var cur_arr = $.parseJSON('<?= json_encode($cur_arr) ?>');
  $(document).ready(function(){
    end_loader();
    $('.select2').select2({
        width:"100%"
    })
    $('#department_id').change(function(){
        var did = $(this).val()
        // Hide all options first
        $('#curriculum_id option').hide();
        $('#curriculum_id option[value=""]').show();
        
        // Show only options for selected department
        $('#curriculum_id option[data-department="'+did+'"]').show();
        
        // Reset selection
        $('#curriculum_id').val('');
        $('#curriculum_id').trigger("change")
    })

    // Registration Form Submit
    $('#registration-form').submit(function(e){
        e.preventDefault()
        var _this = $(this)
            $(".pop-msg").remove()
            $('#password, #cpassword').removeClass("is-invalid")
        var el = $("<div>")
            el.addClass("alert pop-msg my-2")
            el.hide()
        if($("#password").val() != $("#cpassword").val()){
            el.addClass("alert-danger")
            el.text("Password does not match.")
            $('#password, #cpassword').addClass("is-invalid")
            $('#cpassword').after(el)
            el.show('slow')
            return false;
        }
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Users.php?f=save_student",
            method:'POST',
            data:_this.serialize(),
            dataType:'json',
            error:err=>{
                console.log(err)
                el.text("An error occured while saving the data")
                el.addClass("alert-danger")
                _this.prepend(el)
                el.show('slow')
                end_loader()
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.href= "./login.php"
                }else if(!!resp.msg){
                    el.text(resp.msg)
                    el.addClass("alert-danger")
                    _this.prepend(el)
                    el.show('show')
                }else{
                    el.text("An error occured while saving the data")
                    el.addClass("alert-danger")
                    _this.prepend(el)
                    el.show('show')
                }
                end_loader();
                $('html, body').animate({scrollTop: 0},'fast')
            }
        })
    })
  })
</script>
</body>
</html>