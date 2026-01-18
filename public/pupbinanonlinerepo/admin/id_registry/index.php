<?php
$tbl = $conn->query("SHOW TABLES LIKE 'id_registry'");
if(!$tbl || $tbl->num_rows == 0){
  $conn->query("CREATE TABLE `id_registry` (
    `id` int(30) NOT NULL AUTO_INCREMENT,
    `id_number` varchar(50) NOT NULL,
    `name` text NULL,
    `department_id` int(30) NULL,
    `status` tinyint(1) NOT NULL DEFAULT 1,
    `date_created` datetime NOT NULL DEFAULT current_timestamp(),
    `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_id_number` (`id_number`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}
?>
<div class="card card-outline card-primary">
  <div class="card-header">
    <h3 class="card-title">ID Registry</h3>
    <div class="card-tools">
      <a href="javascript:void(0)" id="create_new" class="btn btn-sm btn-primary"><span class="fas fa-plus"></span> Add ID</a>
      <a href="javascript:void(0)" id="import_ids" class="btn btn-sm btn-warning" style="font-weight:600"><span class="fas fa-file-upload"></span> Import IDs</a>
    </div>
  </div>
  <div class="card-body">
    <div class="container-fluid">
      <table class="table table-hover table-striped">
        <colgroup>
          <col width="5%">
          <col width="20%">
          <col width="25%">
          <col width="25%">
          <col width="10%">
          <col width="15%">
        </colgroup>
        <thead>
          <tr>
            <th>#</th>
            <th>Date Created</th>
            <th>ID Number</th>
            <th>Name</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $i = 1;
            $hasDeptCol = false;
            $colRes = $conn->query("SHOW COLUMNS FROM `id_registry` LIKE 'department_id'");
            if($colRes && $colRes->num_rows > 0) $hasDeptCol = true;
            $hasDeptTable = false;
            $tblRes = $conn->query("SHOW TABLES LIKE 'department_list'");
            if($tblRes && $tblRes->num_rows > 0) $hasDeptTable = true;
            $sql = "SELECT r.*".($hasDeptCol && $hasDeptTable ? ", d.name as department_name" : "")." FROM `id_registry` r ".($hasDeptCol && $hasDeptTable ? "LEFT JOIN `department_list` d ON r.department_id = d.id " : "")." ORDER BY r.id_number ASC";
            $qry = $conn->query($sql);
            if($qry && $qry->num_rows > 0):
            while($row = $qry->fetch_assoc()):
          ?>
            <tr>
              <td class="text-center"><?php echo $i++; ?></td>
              <td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
              <td><?php echo htmlspecialchars($row['id_number']) ?></td>
              <td class="truncate-1"><?php echo !empty($row['name']) ? htmlspecialchars($row['name']) : (isset($row['department_name']) ? htmlspecialchars($row['department_name']) : '') ?></td>
              <td class="text-center">
                <?php
                  switch($row['status']){
                    case '1':
                      echo "<span class='badge badge-success badge-pill'>Active</span>";
                      break;
                    case '0':
                      echo "<span class='badge badge-secondary badge-pill'>Inactive</span>";
                      break;
                  }
                ?>
              </td>
              <td align="center">
                <button type="button" class="btn btn-flat btn-default btn-sm action_menu_btn" data-id="<?php echo $row['id'] ?>">Action</button>
              </td>
            </tr>
          <?php endwhile; else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted">No IDs found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
  $(document).ready(function(){
    $('#create_new').click(function(){
      uni_modal("Add ID","id_registry/manage_id.php")
    })
    $('#import_ids').click(function(){
      uni_modal("Import IDs","id_registry/import.php")
    })
    $('.action_menu_btn').click(function(){
      var id = $(this).data('id');
      uni_modal('Actions', 'inc/action_menu.php?context=id_registry&id=' + id);
    })
    $('.table td,.table th').addClass('py-1 px-2 align-middle')
    $('.table').dataTable({
      columnDefs: [
        { orderable: false, targets: 5 }
      ],
    });
  })
  function delete_id_registry($id){
    start_loader();
    $.ajax({
      url:_base_url_+"classes/Master.php?f=delete_id_registry",
      method:"POST",
      data:{id: $id},
      dataType:"json",
      error:err=>{
        console.log(err)
        alert_toast("An error occured.",'error');
        end_loader();
      },
      success:function(resp){
        if(typeof resp== 'object' && resp.status == 'success'){
          location.reload();
        }else{
          alert_toast("An error occured.",'error');
          end_loader();
        }
      }
    })
  }
</script>
