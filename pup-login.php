<?php require_once('./config.php');
$cover = $_settings->info('cover');
$cover_url = $cover ? validate_image($cover) : base_url.'dist/img/no-image-available.png';
$is_video = preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $cover_url);
?>
<!DOCTYPE html>
<html lang="en" style="height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | <?php echo $_settings->info('name') ?></title>
    <link rel="icon" href="<?php echo validate_image($_settings->info('logo')) ?>" />
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.css">
    <link rel="stylesheet" href="dist/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="plugins/jquery/jquery.min.js"></script>

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
        
        .login-header p {
            color: #666;
            margin-top: 10px;
            font-size: 1rem;
        }

        .btn-select {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 60px;
            margin-bottom: 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            transition: all 0.3s;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .btn-student {
            background: var(--pup-maroon);
            color: #fff;
        }
        
        .btn-student:hover {
            background: #5a0000;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-admin {
            background: #333;
            color: #fff;
        }
        
        .btn-admin:hover {
            background: #000;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: #fff;
            transform: translateY(-2px);
        }
        
        .btn-home {
            background: #f4f6f9;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn-home:hover {
            background: #e9ecef;
            color: #000;
            transform: translateY(-2px);
        }

        .pup-terms {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #888;
            line-height: 1.5;
        }
        
        .pup-terms a {
            color: var(--pup-maroon);
            font-weight: 600;
            text-decoration: none;
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
                ACCESS TO KNOWLEDGE, PATHWAY TO WISDOM.<br>
                Explore the academic treasures of our campus.
            </div>
        </div>
        
        <!-- Right Side: Selection Buttons -->
        <div class="card-right">
            <div class="login-header">
                <h2>Select Portal</h2>
                <p>Please choose your destination</p>
            </div>
            
            <a href="login.php" class="btn-select btn-student">
                <i class="fas fa-user-graduate mr-2"></i> User Login
            </a>
            
            <a href="admin/login.php" class="btn-select btn-admin">
                <i class="fas fa-chalkboard-teacher mr-2"></i> Admin / Faculty
            </a>
            
            <a href="./" class="btn-select btn-home">
                <i class="fas fa-home mr-2"></i> Back to Homepage
            </a>
            
            <div class="pup-terms">
                By using this service, you agree to the 
                <a href="#">Terms of Use</a> and <a href="#">Privacy Statement</a>.
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

</body>
</html>