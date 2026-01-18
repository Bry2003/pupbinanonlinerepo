<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_settings->userdata('login_type') == 2){
    $qry = $conn->query("SELECT s.adviser_id FROM archive_list a INNER JOIN student_list s ON a.student_id = s.id WHERE a.id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        $res = $qry->fetch_assoc();
        if($res['adviser_id'] != $_settings->userdata('id')){
            echo "<div class='alert alert-danger'>You are not authorized to update this archive.</div>";
            exit;
        }
    }
}
?>
<div class="container-fluid">
    <form action="" id="update_status_form">
        <input type="hidden" name="id" value="<?= isset($_GET['id']) ? $_GET['id'] : "" ?>">
        <div class="form-group">
            <label for="status" class="control-label text-navy">Status</label>
            <select name="status" id="status" class="form-control form-control-border" required>
                <?php if($_settings->userdata('type') == 2): // Adviser ?>
                     <option value="0" <?= isset($_GET['status']) && $_GET['status'] == 0 ? "selected" : "" ?>>Pending</option>
                     <option value="2" <?= isset($_GET['status']) && $_GET['status'] == 2 ? "selected" : "" ?>>verify</option>
                <?php else: // Admin ?>
                     <option value="0" <?= isset($_GET['status']) && $_GET['status'] == 0 ? "selected" : "" ?>>Pending</option>
                     
                     <option value="1" <?= isset($_GET['status']) && $_GET['status'] == 1 ? "selected" : "" ?>>Published</option>
                <?php endif; ?>
            </select>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#update_status_form').submit(function(e){
            e.preventDefault()
            start_loader()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            var _this = $(this)
            $.ajax({
                url:_base_url_+"classes/Master.php?f=update_status",
                method:"POST",
                data:$(this).serialize(),
                dataType:"json",
                error:err=>{
                    console.log(err)
                    alert_toast("An error occured while saving the data,", "error")
                    end_loader()
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload()
                    }else if(!!resp.msg){
                        el.addClass("alert-danger")
                        el.text(resp.msg)
                        _this.prepend(el)
                    }else{
                        el.addClass("alert-danger")
                        el.text("An error occurred due to unknown reason.")
                        _this.prepend(el)
                    }
                    el.show('slow')
                    end_loader();
                }
            })
        })
    })
</script>