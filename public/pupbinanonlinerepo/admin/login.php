<?php require_once('../config.php');
$cover = $_settings->info('cover');
$cover_url = $cover ? validate_image($cover) : base_url.'dist/img/no-image-available.png';
$is_video = preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $cover_url);
?>
<!DOCTYPE html>
<html lang="en" style="height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?php echo $_settings->info('name') ?></title>
    <link rel="icon" href="<?php echo validate_image($_settings->info('logo')) ?>" />
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.css">
    <link rel="stylesheet" href="../dist/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="../plugins/jquery/jquery.min.js"></script>

    <style>
        :root{
            --pup-maroon: <?php echo $_settings->info('theme_maroon') ?: '#800000' ?>;
            --pup-gold: <?php echo $_settings->info('theme_gold') ?: '#FFD700' ?>;
        }
        body { 
            font-family: 'Clarendon BT', 'Clarendon', 'Georgia', 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            background: #fff;
        }
        
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
            position: relative;
        }

        .bg-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }

        .central-card {
            display: flex;
            background: #fff;
            width: 90%;
            max-width: 900px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            z-index: 1;
            min-height: 500px;
        }

        .card-left {
            flex: 0 0 45%;
            background: #f4f6f9;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            text-align: center;
            border-right: 1px solid #eee;
            position: relative;
        }

        .brand-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin-bottom: 1.5rem;
        }

        .info-title {
            color: var(--pup-maroon);
            font-weight: 800;
            font-size: 1.6rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .info-subtitle {
            color: #555;
            font-size: 1rem;
            line-height: 1.6;
        }

        .card-right {
            flex: 1;
            background: #fff;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            text-align: center;
        }

        .login-header {
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: var(--pup-maroon);
            font-weight: 800;
            margin: 0;
            font-size: 1.8rem;
            text-transform: uppercase;
            display: inline-block;
            border-bottom: 3px solid var(--pup-gold);
            padding-bottom: 5px;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-control {
            height: 50px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            padding-left: 15px;
        }

        .form-control:focus {
            border-color: var(--pup-maroon);
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
            background: #fff;
        }

        .btn-login {
            background: var(--pup-maroon);
            color: #fff;
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            margin-top: 1rem;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #5a0000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.95rem;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: var(--pup-maroon);
        }

        @media (max-width: 992px) {
            .central-card {
                flex-direction: column;
                max-width: 450px;
                min-height: auto;
                margin: 20px;
            }
            .card-left {
                flex: 0 0 auto;
                padding: 2rem;
                border-right: none;
                border-bottom: 1px solid #eee;
            }
            .brand-logo { width: 80px; height: 80px; margin-bottom: 1rem; }
            .info-title { font-size: 1.3rem; }
            .card-right { padding: 2rem; }
        }
    </style>
</head>
<body>

<div class="login-container">
    <?php if($is_video): ?>
        <video autoplay muted loop class="bg-video">
            <source src="<?php echo $cover_url ?>" type="video/mp4">
        </video>
    <?php else: ?>
        <img src="<?php echo $cover_url ?>" alt="Cover Image" class="bg-video">
    <?php endif; ?>
    <div class="bg-overlay"></div>

    <div class="central-card animate__animated animate__fadeInUp">
        <!-- Left Side: Branding -->
        <div class="card-left">
            <img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="PUP Logo" class="brand-logo">
            <div class="info-title">PUP Biñan Online Repository System</div>
            <div class="info-subtitle">
                ADMINISTRATOR & FACULTY PORTAL<br>
                Manage system resources and academic archives.
            </div>
        </div>
        
        <!-- Right Side: Login Form -->
        <div class="card-right">
            <div class="login-header">
                <h2>Admin Login</h2>
            </div>
            
            <form id="login-frm" action="" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="Username" required autofocus>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-login">Sign In</button>
                <a href="../pup-login.php" class="back-link">Back to Selection</a>
            </form>
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
    const _base_url_ = '<?php echo base_url ?>';
</script>

<script>
  $(document).ready(function(){
    $('#login-frm').submit(function(e){
        e.preventDefault()
        var _this = $(this)
        var btn = _this.find('button[type="submit"]')
        var original_text = btn.text()
        
        btn.attr('disabled', true).text('Signing in...')
        
        if($('.alert-danger').length > 0)
            $('.alert-danger').remove();
            
        $.ajax({
            url:_base_url_+'classes/Login.php?f=login',
            method:'POST',
            data:$(this).serialize(),
            error:err=>{
                console.log(err)
                btn.removeAttr('disabled').text(original_text)
            },
            success:function(resp){
                if(resp){
                    resp = JSON.parse(resp)
                    if(resp.status == 'success'){
                        location.replace('../admin')
                    }else if(resp.status == 'incorrect'){
                        var _el = $('<div>')
                        _el.addClass('alert alert-danger')
                        _el.text("Incorrect username or password.")
                        _el.hide()
                        _this.prepend(_el)
                        _el.show('slow')
                        btn.removeAttr('disabled').text(original_text)
                    }else if(resp.status == 'notverified'){
                        var _el = $('<div>')
                        _el.addClass('alert alert-danger')
                        _el.text("Account not verified.")
                        _el.hide()
                        _this.prepend(_el)
                        _el.show('slow')
                        btn.removeAttr('disabled').text(original_text)
                    }else{
                         var _el = $('<div>')
                        _el.addClass('alert alert-danger')
                        _el.text("An error occurred.")
                        _el.hide()
                        _this.prepend(_el)
                        _el.show('slow')
                        btn.removeAttr('disabled').text(original_text)
                    }
                }
            }
        })
    })
  })
</script>
</body>
</html>