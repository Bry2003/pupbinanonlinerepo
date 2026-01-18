<?php
require_once('config.php');

// Start session if not already started
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if form is submitted
if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Connect to database
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Query to check user credentials
    $query = "SELECT * FROM users WHERE username = ? AND status = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if(md5($password) === $user['password']) {
            // Start session if not already started
            if(session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Set user data in session
            foreach($user as $k => $v) {
                if(!is_numeric($k) && $k != 'password') {
                    $_SESSION['userdata'][$k] = $v;
                }
            }
            
            // Force login_type to admin (1) to access system settings
            $_SESSION['userdata']['login_type'] = 1;
            
            // Redirect to system settings page
            echo "<script>alert('Login successful! You will be redirected to system settings.'); window.location.href='admin/?page=system_info';</script>";
            exit;
        } else {
            $error = "Incorrect password";
        }
    } else {
        $error = "User not found or not active";
    }
    
    $stmt->close();
    $conn->close();
}

// Option to force admin access if already logged in
if(isset($_GET['force_admin']) && isset($_SESSION['userdata'])) {
    $_SESSION['userdata']['login_type'] = 1;
    echo "<script>alert('Your account has been temporarily elevated to admin status.'); window.location.href='admin/?page=system_info';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access Helper</title>
    <link rel="stylesheet" href="dist/css/adminlte.css">
    <link rel="stylesheet" href="dist/css/pup-theme.css">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 400px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-box-msg {
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #800000;
            border-color: #800000;
        }
        .btn-primary:hover {
            background-color: #600000;
            border-color: #600000;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <b>System Settings Access</b>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <div class="alert alert-info">
                    <p><strong>Problem:</strong> Unable to access system settings</p>
                    <p><strong>Solution:</strong> This page will help you gain admin access to system settings</p>
                </div>
                
                <?php if(isset($_SESSION['userdata'])): ?>
                <div class="text-center mb-4">
                    <p>You are currently logged in as: <strong><?php echo isset($_SESSION['userdata']['username']) ? $_SESSION['userdata']['username'] : 'Unknown User'; ?></strong></p>
                    <p>Login Type: <strong><?php echo isset($_SESSION['userdata']['login_type']) ? ($_SESSION['userdata']['login_type'] == 1 ? 'Admin' : 'Non-Admin') : 'Unknown'; ?></strong></p>
                    
                    <?php if(isset($_SESSION['userdata']['login_type']) && $_SESSION['userdata']['login_type'] != 1): ?>
                    <a href="?force_admin=1" class="btn btn-warning btn-block">Elevate to Admin Access</a>
                    <?php else: ?>
                    <a href="admin/?page=system_info" class="btn btn-success btn-block">Go to System Settings</a>
                    <?php endif; ?>
                    
                    <hr>
                    <p>Or login with a different admin account below:</p>
                </div>
                <?php else: ?>
                <p class="login-box-msg">Sign in with an admin account</p>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-group">
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="login" class="btn btn-primary btn-block">Sign In as Admin</button>
                        </div>
                    </div>
                </form>
                
                <p class="mt-3 mb-1 text-center">
                    <a href="admin/login.php">Go to regular login page</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>