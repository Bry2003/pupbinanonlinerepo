<?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition" style="height: 100%;">
  <?php require_once('inc/loading_screen.php') ?>
  <script>
    start_loader()
  </script>
  <link rel="stylesheet" href="dist/css/pup-theme.css">
  <style>
    html, body{
      height:100% !important;
      width:100% !important;
      margin: 0;
      padding: 0;
    }
  </style>
  <?php if($_settings->chk_flashdata('success')): ?>
      <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
      </script>
      <?php endif;?> 
<div class="pup-login-container">
    <div class="pup-login-left" style="background-image: url('uploads/cover-163884028.png'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
        <div style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; width: 80%; max-width: 500px; text-align: center;">
            <img src="<?= validate_image($_settings->info('logo')) ?>" alt="PUP Logo" class="pup-logo">
            <h1 class="pup-login-title"><?php echo $_settings->info('name') ?> - USER</h1>
        </div>
    </div>
      
     <div class="pup-login-right">
         <div class="pup-welcome-content">
             <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); font-weight: 700;">Login</h2>
             <div class="pup-login-card">
                 <div class="pup-login-header">
                     <h3 style="font-size: 1.8rem; margin: 1rem 0; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); font-weight: 600;">USER Login</h3>
                 </div>
                 <div class="text-right mb-3">
                     <a href="./" class="btn btn-sm btn-success"><i class="fa fa-home"></i> Back to Homepage</a>
                 </div>
                 <form action="" id="slogin-form">
                     <div class="form-group">
                         <input type="email" name="email" id="email" placeholder="Email" class="pup-form-control" required>
                     </div>
                     <div class="form-group">
                         <input type="password" name="password" id="password" placeholder="Password" class="pup-form-control" required>
                     </div>
                     <div class="form-group text-right">
                         <button class="pup-btn">Login</button>
                     </div>
                     <div class="text-center mt-3">
                         <a href="register.php" style="color: var(--pup-maroon); font-weight: bold;">Register</a> | <a href="pup-login.php" style="color: var(--pup-maroon); font-weight: bold;">Back to Selection</a>
                     </div>
                 </form>
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
  $(document).ready(function(){
    end_loader();
    // Registration Form Submit
    $('#slogin-form').submit(function(e){
        e.preventDefault()
        var _this = $(this)
            $(".pop-msg").remove()
            $('#password, #cpassword').removeClass("is-invalid")
        var el = $("<div>")
            el.addClass("alert pop-msg my-2")
            el.hide()
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Login.php?f=student_login",
            method:'POST',
            data:_this.serialize(),
            dataType:'json',
            error:err=>{
                console.log(err)
                el.text("An error occured while saving the data")
                el.addClass("alert-danger")
                _this.prepend(el)
                el.show('slow')
                end_loader();
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.href= "./"
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