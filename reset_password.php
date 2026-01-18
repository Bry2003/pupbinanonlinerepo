<?php
require_once './config.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$error = '';
$success = '';
$show_form = false;

if ($token === '') {
  $error = 'Invalid password reset link.';
} else {
  $token_safe = $conn->real_escape_string($token);
  $qry = $conn->query("SELECT * FROM student_password_resets WHERE token = '{$token_safe}' AND used = 0 LIMIT 1");
  if ($conn->error) {
    $error = 'An error occurred. Please try again later.';
  } elseif (!$qry || $qry->num_rows === 0) {
    $error = 'This password reset link is invalid or has already been used.';
  } else {
    $reset = $qry->fetch_assoc();
    if (strtotime($reset['expires_at']) < time()) {
      $error = 'This password reset link has expired.';
    } else {
      $show_form = true;
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token_post = isset($_POST['token']) ? trim($_POST['token']) : '';
  $password = isset($_POST['password']) ? $_POST['password'] : '';
  $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

  if ($token_post === '') {
    $error = 'Invalid request.';
  } elseif ($password === '' || $confirm === '') {
    $error = 'Please enter and confirm your new password.';
  } elseif ($password !== $confirm) {
    $error = 'Passwords do not match.';
  } elseif (strlen($password) < 6) {
    $error = 'Password must be at least 6 characters.';
  } else {
    $token_safe = $conn->real_escape_string($token_post);
    $qry = $conn->query("SELECT * FROM student_password_resets WHERE token = '{$token_safe}' AND used = 0 LIMIT 1");
    if ($conn->error) {
      $error = 'An error occurred. Please try again later.';
    } elseif (!$qry || $qry->num_rows === 0) {
      $error = 'This password reset link is invalid or has already been used.';
    } else {
      $reset = $qry->fetch_assoc();
      if (strtotime($reset['expires_at']) < time()) {
        $error = 'This password reset link has expired.';
      } else {
        $student_id = (int)$reset['student_id'];
        $new_pass = md5($password);
        $student_id_safe = $conn->real_escape_string($student_id);
        $update = $conn->query("UPDATE student_list SET password = '{$new_pass}' WHERE id = '{$student_id_safe}'");
        if ($conn->error || !$update) {
          $error = 'Unable to update password. Please try again later.';
        } else {
          $conn->query("UPDATE student_password_resets SET used = 1, date_used = NOW() WHERE id = '{$reset['id']}'");
          $success = 'Your password has been updated. You can now log in.';
          $show_form = false;
        }
      }
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password | <?php echo $_settings->info('name') ?></title>
  <link rel="icon" href="<?php echo validate_image($_settings->info('logo')) ?>" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.css">
  <link rel="stylesheet" href="dist/css/custom.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="javascript:void(0)" class="h1"><b>PUPBC</b> Reset Password</a>
    </div>
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <?php if ($show_form): ?>
        <p class="login-box-msg">Enter your new password.</p>
        <form action="" method="post">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="password" placeholder="New password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block">Update Password</button>
            </div>
          </div>
        </form>
      <?php endif; ?>

      <p class="mt-3 mb-0">
        <a href="login.php" class="text-center">Back to Login</a>
      </p>
    </div>
  </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>

