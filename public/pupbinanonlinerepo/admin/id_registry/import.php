<?php
require_once('../../config.php');
?>
<div class="container-fluid">
  <form action="" id="id-import-form" enctype="multipart/form-data">
    <div class="form-group">
      <label class="control-label">Upload File</label>
      <input type="file" name="import_file" id="import_file" class="form-control form-control-border" accept=".csv, .xlsx, .docx, text/csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
      <small class="text-muted">Accepted: CSV, XLSX, DOCX. Only ID Number is required. Name and Status are optional.</small>
    </div>
    <div class="form-group">
      <label class="control-label">Download Templates</label>
      <div>
        <a href="<?php echo base_url ?>classes/Master.php?f=id_registry_template&type=csv" class="btn btn-sm btn-outline-primary">CSV Template</a>
        <a href="<?php echo base_url ?>classes/Master.php?f=id_registry_template&type=xlsx" class="btn btn-sm btn-outline-primary">XLSX Template</a>
        <a href="<?php echo base_url ?>classes/Master.php?f=id_registry_template&type=docx" class="btn btn-sm btn-outline-primary">DOCX Template</a>
      </div>
    </div>
  </form>
</div>
<script>
  $(function(){
    $('#uni_modal #submit').text('Import').off('click').on('click', function(){
      $('#id-import-form').submit();
    });
    $('#id-import-form').submit(function(e){
      e.preventDefault();
      var _this = $(this);
      start_loader();
      $.ajax({
        url: _base_url_+"classes/Master.php?f=import_id_registry",
        method: 'POST',
        data: new FormData(_this[0]),
        processData: false,
        contentType: false,
        dataType: 'json',
        error: err=>{
          console.log(err);
          alert_toast('An error occured.', 'error');
          end_loader();
        },
        success: function(resp){
          if(typeof resp === 'object' && resp.status === 'success'){
            var msg = 'Imported: '+(resp.imported||0)+', Duplicates: '+(resp.duplicates||0)+', Skipped: '+(resp.skipped||0);
            if(resp.uploaded_url){ msg += ' | S3: '+resp.uploaded_url; }
            alert_toast(msg, 'success');
            location.reload();
          }else{
            alert_toast(resp.msg || 'Import failed.', 'error');
            end_loader();
          }
        }
      });
    });
  });
</script>
