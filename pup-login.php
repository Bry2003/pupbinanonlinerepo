<?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en" style="height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_settings->info('name') ?></title>
    <link rel="icon" href="<?php echo validate_image($_settings->info('logo')) ?>" />
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.css">
    <link rel="stylesheet" href="dist/css/custom.css">
    <link rel="stylesheet" href="dist/css/pup-theme.css">
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="dist/js/pre_loader.js"></script>
</head>
<body style="height: 100%;">
    <script>
        start_loader()
    </script>

    <div class="pup-login-container">
        <div class="pup-login-left" style="background-image: url('uploads/cover-163884028.png'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
            <div style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; width: 80%; max-width: 500px; text-align: center;">
                <img src="<?= validate_image($_settings->info('logo')) ?>" alt="PUP Logo" class="pup-logo">
                <h1 class="pup-login-title">Hi, PUPian!</h1>
                <p style="font-size: 1.2rem; font-weight: 500; color: var(--pup-text-dark);">Please click or tap your destination.</p>
            </div>
            
            <div style="width: 100%; max-width: 400px; margin-top: 20px; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px;">
                <a href="login.php" class="pup-role-btn pup-student-btn">USER</a>
                <a href="admin/login.php" class="pup-role-btn pup-faculty-btn">Admin</a>
                <a href="register.php" class="pup-role-btn pup-register-btn">Register</a>
                <a href="./" class="pup-role-btn" style="background-color: #28a745; color: white;">Back to Homepage</a>
                
                <div class="pup-terms" style="color: var(--pup-text-dark); font-weight: 500;">
                    By using this service, you understood and agree to the PUP Online Services 
                    <a href="#" style="color: var(--pup-maroon); font-weight: bold;">Terms of Use</a> and <a href="#" style="color: var(--pup-maroon); font-weight: bold;">Privacy Statement</a>
                </div>
            </div>
        </div>
        
        <div class="pup-login-right">
            <div class="pup-welcome-content">
                <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); font-weight: 700;">Welcome to PUP Biñan</h2>
                <p style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 1.5rem; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">The Polytechnic University of the Philippines Biñan Campus is committed to providing quality education and fostering academic excellence.</p>
                
                <h3 style="font-size: 1.8rem; margin: 1.5rem 0; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); font-weight: 600;">About Us</h3>
                <p style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 1.5rem; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">PUP Biñan Campus was established to serve the educational needs of students in Biñan City and nearby municipalities. We offer various undergraduate programs designed to prepare students for successful careers.</p>
                
                <p style="font-size: 1.1rem; line-height: 1.6; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">Our mission is to provide highly technical, integrated, and relevant quality education to develop competent and morally upright citizens who are productive members of society.</p>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- Pre-loader -->
    <script src="dist/js/pre_loader.js"></script>
    
    <script>
        $(document).ready(function(){
            end_loader();
        })
    </script>
</body>
</html>