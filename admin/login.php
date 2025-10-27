<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition" style="height: 100%;">
  <?php require_once('../inc/loading_screen.php') ?>
  <script>
    start_loader()
  </script>
  <link rel="stylesheet" href="../dist/css/pup-theme.css">
  <style>
    html, body{
      height:100% !important;
      width:100% !important;
      margin: 0;
      padding: 0;
    }
  </style>
  <div class="pup-login-container">
    <div class="pup-login-left" style="background-image: url('../uploads/cover-163884028.png'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
      <div style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; width: 80%; max-width: 500px; text-align: center;">
        <img src="<?= validate_image($_settings->info('logo')) ?>" alt="PUP Logo" class="pup-logo">
        <h1 class="pup-login-title"><?php echo $_settings->info('name') ?> - Admin</h1>
      </div>
    </div>
    
    <div class="pup-login-right">
      <div class="pup-welcome-content">
        <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); font-weight: 700;">Admin Login</h2>
        <div class="pup-login-card">
          <div class="pup-login-header">
            <h3 style="font-size: 1.8rem; margin: 1rem 0; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); font-weight: 600;">Administrator Access</h3>
          </div>
          <form id="login-frm" action="" method="post">
            <div class="form-group">
              <input type="text" class="pup-form-control" autofocus name="username" placeholder="Username">
            </div>
            <div class="form-group">
              <input type="password" class="pup-form-control" name="password" placeholder="Password">
            </div>
            <div class="form-group d-flex justify-content-between align-items-center">
              <a href="../pup-login.php" class="pup-btn" style="background-color: #6c757d; border-color: #6c757d;"><i class="fas fa-arrow-left mr-2"></i>Back</a>
              <button type="submit" class="pup-btn">Sign In</button>
            </div>
          </form>
        </div>
        
        <div class="mt-4">
          <h2 style="font-size: 2.2rem; margin-bottom: 1.5rem; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); font-weight: 700;">Welcome to PUP Biñan Admin</h2>
          <p style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 1.5rem; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">This is the administrative portal for the Polytechnic University of the Philippines Biñan Campus.</p>
          
          <h3 style="font-size: 1.8rem; margin: 1.5rem 0; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); font-weight: 600;">Admin Portal</h3>
          <p style="font-size: 1.1rem; line-height: 1.6; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">This secure area is for authorized personnel only. Please log in with your administrator credentials.</p>
        </div>
      </div>
    </div>
  </div>

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function(){
    end_loader();
  })
</script>
</body>
</html>