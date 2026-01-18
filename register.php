<?php require_once('./config.php');
$cover = $_settings->info('cover');
$cover_url = $cover ? validate_image($cover) : base_url.'dist/img/no-image-available.png';
$is_video = preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $cover_url);
?>
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
    <div class="pup-login-left" style="<?php echo !$is_video ? 'background-image: url(\''.$cover_url.'\');' : '' ?> background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
        <?php if($is_video): ?>
        <video autoplay muted loop playsinline style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;">
            <source src="<?= $cover_url ?>" type="video/mp4">
        </video>
        <?php endif; ?>
        <div style="position: relative; z-index: 1; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; width: 80%; max-width: 500px; text-align: center;">
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
                
                <form action="" id="registration-form" enctype="multipart/form-data">
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
                                    <span class="text-navy"><small>Program</small></span>
                                    <select name="department_id" id="department_id" class="pup-form-control select2" data-placeholder="Select Here Program" required>
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
                                    <input type="text" name="id_number" id="id_number" placeholder="ID Number" class="pup-form-control">
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
                                <div class="form-group">
                                    <span class="text-navy"><small>Account Type</small></span>
                                    <div class="d-flex gap-3">
                                        <div class="custom-control custom-radio mr-3">
                                            <input class="custom-control-input" type="radio" id="acctStudent" name="account_type" value="student" checked>
                                            <label for="acctStudent" class="custom-control-label">Student</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="acctFaculty" name="account_type" value="faculty">
                                            <label for="acctFaculty" class="custom-control-label">Faculty</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group d-flex justify-content-between align-items-center">
                                    <a href="pup-login.php" class="pup-btn" style="background-color: var(--pup-secondary); border-color: var(--pup-secondary); color: var(--pup-text-light);"><i class="fas fa-arrow-left mr-2"></i>Back</a>
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
<!-- Global helpers -->
<script src="dist/js/script.js"></script>

<script>
  $(document).ready(function(){
    end_loader();
    $('.select2').select2({
        width:"100%"
    })
    

    // Toggle Program select
    function updateAccountTypeUI(){
        var type = $('input[name="account_type"]:checked').val();
        var isFaculty = (String(type).toLowerCase() === 'faculty');
        var $dept = $('#department_id');
        var $idNum = $('#id_number');

        if(isFaculty){
            $dept.val('').trigger('change');
            $dept.prop('disabled', true).removeAttr('required');
            $dept.select2('destroy');
            $dept.prop('disabled', true);
            $dept.select2({width:'100%'});
        }else{
            $dept.prop('disabled', false).attr('required', 'required');
            $dept.select2('destroy');
            $dept.select2({width:'100%'});
        }
    }

    // Initialize and bind radio change
    updateAccountTypeUI();
    $('input[name="account_type"]').on('change', updateAccountTypeUI);

    // Registration Form Submit with file upload
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
        var fd = new FormData(_this[0]);
        $.ajax({
            url:_base_url_+"classes/Users.php?f=save_student",
            method:'POST',
            data: fd,
            processData: false,
            contentType: false,
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
                    if(resp.auto_login === true){
                        $.ajax({
                            url:_base_url_+"classes/Login.php?f=student_login",
                            method:'POST',
                            data:{ email: $('#email').val(), password: $('#password').val() },
                            dataType:'json',
                            success:function(lr){
                                if(lr && lr.status === 'success'){
                                    showSignupLoader("Welcome! Redirecting to your profile...", "./?page=profile");
                                }else{
                                    showSignupLoader("Registration successful. Redirecting to login...", "./login.php");
                                }
                            },
                            error:function(){
                                showSignupLoader("Registration successful. Redirecting to login...", "./login.php");
                            }
                        });
                    }else{
                        showSignupLoader("Registration successful. Redirecting to login...", "./login.php")
                    }
                }else if(resp.status == 'blocked'){
                    showGuestPopup(resp.msg || 'Registration is not allowed. You may continue as Guest to view-only.');
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

    function showGuestPopup(message){
        var overlay = $('<div>').css({position:'fixed',top:0,left:0,width:'100%',height:'100%',background:'rgba(0,0,0,0.6)',zIndex:2000});
        var modal = $('<div>').css({position:'fixed',top:'50%',left:'50%',transform:'translate(-50%,-50%)',background:'#fff',padding:'20px',borderRadius:'8px',maxWidth:'420px',width:'90%',zIndex:2001});
        var title = $('<h4>').text('View-Only Access');
        var msg = $('<p>').text(message);
        var btns = $('<div>').css({display:'flex',gap:'10px',justifyContent:'flex-end'});
        var guest = $('<a>').addClass('btn btn-primary').text('Continue as Guest').attr('href','./');
        var close = $('<button>').addClass('btn btn-secondary').text('Close').on('click', function(){ overlay.remove(); });
        btns.append(close).append(guest);
        modal.append(title).append(msg).append(btns);
        overlay.append(modal);
        $('body').append(overlay);
    }

    // Removed live camera/file capture logic per request
  })
</script>
</body>
</html>
