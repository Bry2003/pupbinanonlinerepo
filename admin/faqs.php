<?php require_once('../config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<?php include base_app.'admin/inc/header.php'; ?>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include base_app.'admin/inc/navigation.php'; ?>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0 text-dark">Admin FAQs</h1>
        <p class="text-muted">Common admin tasks and tips</p>
      </div>
    </div>
    <section class="content">
      <div class="container-fluid">
        <div class="card card-outline card-primary">
          <div class="card-body">
            <dl>
              <dt>How to manage users?</dt>
              <dd>Open Admin → Users to add, edit, deactivate users, and reset passwords.</dd>
              <dt>How to adjust theme settings?</dt>
              <dd>Go to Admin → System Info → Theme Settings. Change colors and fonts, then save.</dd>
              <dt>How to manage departments and programs?</dt>
              <dd>Use Admin → Departments or Program to add and edit entries. Click Save to apply changes.</dd>
              <dt>How to view logs or archives?</dt>
              <dd>Navigate via the sidebar. Use filters in tables to find specific records.</dd>
              <dt>Need a walkthrough?</dt>
              <dd>Click the Help Bot “?” in the lower-right and choose Start Tour.</dd>
            </dl>
          </div>
        </div>
      </div>
    </section>
  </div>
  <?php include base_app.'admin/inc/footer.php'; ?>
</div>
<script src="<?php echo base_url ?>plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url ?>dist/js/adminlte.min.js"></script>
</body>
</html>
