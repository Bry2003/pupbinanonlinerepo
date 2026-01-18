<?php require_once('./config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<?php include base_app.'inc/header.php'; ?>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
  <?php include base_app.'inc/navigation.php'; ?>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container">
        <h1 class="m-0 text-dark">FAQs</h1>
        <p class="text-muted">Common questions about using the system</p>
      </div>
    </div>
    <section class="content">
      <div class="container">
        <div class="card card-outline card-primary">
          <div class="card-body">
            <?php
              // Render FAQs from database when available, fallback to static content
              $has_tbl = $conn->query("SHOW TABLES LIKE 'faqs'");
              if($has_tbl && $has_tbl->num_rows > 0){
                $res = $conn->query("SELECT question, answer FROM faqs WHERE status = 1 ORDER BY COALESCE(sort_order, 999999), id ASC");
                if($res && $res->num_rows > 0){
                  echo '<dl>';
                  while($row = $res->fetch_assoc()){
                    echo '<dt>'.htmlspecialchars($row['question']).'</dt>';
                    echo '<dd>'.($row['answer']).'</dd>';
                  }
                  echo '</dl>';
                } else {
                  echo '<p class="text-muted">No published FAQs yet. Please check back later.</p>';
                }
              } else {
            ?>
            <dl>
              <dt>How do I log in?</dt>
              <dd>Click the “USER” button, enter your credentials, and submit. If you forgot your password, use “Forgot Password”.</dd>
              <dt>How to register a new account?</dt>
              <dd>On the selection page, click “Register”, provide required information, capture your photo, and submit.</dd>
              <dt>Where do I upload archives/projects?</dt>
              <dd>Navigate to “Archives” or “Projects” from the top navigation and use the upload/submit buttons.</dd>
              <dt>How to change the theme?</dt>
              <dd>Admins can go to Admin → System Info → Theme Settings to adjust colors and fonts.</dd>
              <dt>How to contact support?</dt>
              <dd>Use the Help Bot in the lower-right to ask questions or start a guided tour. For further help, contact your administrator.</dd>
            </dl>
            <?php } ?>
          </div>
        </div>
      </div>
    </section>
  </div>
  <?php include base_app.'inc/footer.php'; ?>
</div>
<script src="<?php echo base_url ?>plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url ?>dist/js/adminlte.min.js"></script>
</body>
</html>
