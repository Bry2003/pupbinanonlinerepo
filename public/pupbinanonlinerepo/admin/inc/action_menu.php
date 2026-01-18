<?php
require_once('../../config.php');
$context = isset($_GET['context']) ? $_GET['context'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
?>
<style>
  #uni_modal .modal-footer { display: none !important; }
  .action-list { list-style: none; padding: 0; margin: 0; }
  .action-list li { margin-bottom: .5rem; }
  .action-btn { width: 100%; text-align: left; }
  .action-icon { width: 20px; display: inline-block; }
</style>
<div class="container-fluid">
  <div class="mb-2">Choose an action for this item:</div>
  <ul class="action-list">
    <li><button id="action_view" class="btn btn-sm btn-default action-btn"><span class="action-icon fa fa-eye text-dark"></span> View</button></li>
    <?php
    $edit_label = 'Edit';
    $edit_icon = 'fa-edit';
    $edit_class = 'btn-primary';
    if($context === 'archives'){
        $type = $_settings->userdata('type');
        if($type == 2){ // Adviser
            $edit_label = 'Verify';
            $edit_icon = 'fa-check';
            $edit_class = 'btn-success';
        } else { // Admin
            $edit_label = 'Update Status';
            $edit_icon = 'fa-cog';
            $edit_class = 'btn-info';
        }
    }
    ?>
    <li><button id="action_edit" class="btn btn-sm <?= $edit_class ?> action-btn"><span class="action-icon fa <?= $edit_icon ?>"></span> <?= $edit_label ?></button></li>
    <li id="action_view_archives_container" style="display:none"><button id="action_view_archives" class="btn btn-sm btn-info action-btn"><span class="action-icon fa fa-archive"></span> View Archives</button></li>
    <?php if(($context === 'user' || $context === 'students') && $status != 1): ?>
    <li><button id="action_verify" class="btn btn-sm btn-warning action-btn"><span class="action-icon fa fa-check"></span> Verify</button></li>
    <?php endif; ?>
    <li><button id="action_delete" class="btn btn-sm btn-danger action-btn"><span class="action-icon fa fa-trash"></span> Delete</button></li>
  </ul>
  <div class="text-right">
    <button class="btn btn-flat btn-sm btn-dark" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
  </div>
</div>
<script>
  (function(){
    const ctx = <?php echo json_encode($context); ?>;
    const id = <?php echo json_encode($id); ?>;
    const status = <?php echo json_encode($status); ?>;
    const base_url = <?php echo json_encode(base_url); ?>;

    function closeThen(fn){
      $('#uni_modal').modal('hide');
      setTimeout(fn, 300);
    }

    $('#action_view').on('click', function(){
      closeThen(function(){
        switch(ctx){
          case 'curriculum':
            uni_modal('Program Details', 'curriculum/view_curriculum.php?id=' + id);
            break;
          case 'departments':
            uni_modal('Department Details', 'departments/view_department.php?id=' + id);
            break;
          case 'archives':
            window.open(base_url + '/?page=view_archive&id=' + id, '_blank');
            break;
          case 'students':
            uni_modal('Student Details', 'students/view_details.php?id=' + id, 'mid-large');
            break;
          case 'user':
            // If a dedicated view exists, you can point it here
            uni_modal('Account Details', 'user/manage_user.php?id=' + id);
            break;
          case 'id_registry':
            uni_modal('ID Registry', 'id_registry/manage_id.php?id=' + id);
            break;
          default:
            alert_toast('View not configured for this item.', 'warning');
        }
      });
    });

    $('#action_edit').on('click', function(){
      closeThen(function(){
        switch(ctx){
          case 'curriculum':
            uni_modal('Program Details', 'curriculum/manage_curriculum.php?id=' + id);
            break;
          case 'departments':
            uni_modal('Department Details', 'departments/manage_department.php?id=' + id);
            break;
          case 'archives':
            uni_modal('Update Details', 'archives/update_status.php?id=' + id + '&status=' + status);
            break;
          case 'students':
            alert_toast('Edit not available for students.', 'warning');
            break;
          case 'user':
            uni_modal('Account Details', 'user/manage_user.php?id=' + id);
            break;
          case 'id_registry':
            uni_modal('ID Registry', 'id_registry/manage_id.php?id=' + id);
            break;
          default:
            alert_toast('Edit not configured for this item.', 'warning');
        }
      });
    });

    if(ctx === 'curriculum'){
      $('#action_view_archives_container').show();
    }
    $('#action_view_archives').on('click', function(){
      closeThen(function(){
        if(ctx === 'curriculum'){
          location.href = base_url + 'admin/?page=archives&program_id=' + id;
        }
      });
    });

    $('#action_delete').on('click', function(){
      closeThen(function(){
        switch(ctx){
          case 'curriculum':
            _conf('Are you sure to delete this Program permanently?', 'delete_curriculum', [id]);
            break;
          case 'departments':
            _conf('Are you sure to delete this Department permanently?', 'delete_department', [id]);
            break;
          case 'archives':
            _conf('Are you sure to delete this project permanently?', 'delete_archive', [id]);
            break;
          case 'students':
            _conf('Are you sure to delete this student permanently?', 'delete_student', [id]);
            break;
          case 'user':
            _conf('Are you sure to delete this user permanently?', 'delete_user', [id]);
            break;
          case 'id_registry':
            _conf('Are you sure to delete this ID permanently?', 'delete_id_registry', [id]);
            break;
          default:
            alert_toast('Delete not configured for this item.', 'warning');
        }
      });
    });
    $('#action_verify').on('click', function(){
      closeThen(function(){
        if(ctx === 'user' || ctx === 'students'){
          _conf('Are you sure to verify this account?', 'verify_user', [id]);
        } else {
          alert_toast('Verify not configured for this item.', 'warning');
        }
      });
    });
  })();
</script>
    
