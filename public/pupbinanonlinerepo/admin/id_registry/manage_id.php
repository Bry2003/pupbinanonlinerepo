<?php
require_once('../../config.php');
if(isset($_GET['id'])){
  $qry = $conn->query("SELECT * FROM `id_registry` WHERE id = '{$_GET['id']}'");
  if($qry && $qry->num_rows > 0){
    $res = $qry->fetch_array();
    foreach($res as $k=>$v){ if(!is_numeric($k)) $$k = $v; }
  }
}
?>
<div class="container-fluid">
  <form action="" id="id-registry-form">
    <input type="hidden" name="id" value="<?php echo isset($id)? $id : '' ?>">
    <div class="form-group">
      <label for="id_number" class="control-label">ID Number</label>
      <input type="text" name="id_number" id="id_number" class="form-control form-control-border" placeholder="ID Number" value="<?php echo isset($id_number)? htmlspecialchars($id_number) : '' ?>" required>
    </div>
    <div class="form-group">
      <label for="name" class="control-label">Name</label>
      <input type="text" name="name" id="name" class="form-control form-control-border" placeholder="Full Name" value="<?php echo isset($name)? htmlspecialchars($name) : '' ?>" required>
    </div>
    
    <div class="form-group">
      <label class="control-label">Status</label>
      <select name="status" id="status" class="form-control form-control-border" required>
        <option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>
  </form>
</div>
<script>
  $(function(){
    $('#uni_modal #submit').click(function(){
      $('#id-registry-form').submit();
    });
    $('#id-registry-form').submit(function(e){
      e.preventDefault();
      var _this = $(this);
      start_loader();
      $.ajax({
        url: _base_url_+"classes/Master.php?f=save_id_registry",
        method: 'POST',
        data: _this.serialize(),
        dataType: 'json',
        error: err=>{
          console.log(err);
          alert_toast('An error occured.', 'error');
          end_loader();
        },
        success: function(resp){
          if(typeof resp === 'object' && resp.status === 'success'){
            location.reload();
          }else{
            var msg = resp.msg || 'An error occured.';
            alert_toast(msg, 'error');
            end_loader();
          }
        }
      });
    });
  });
</script>
