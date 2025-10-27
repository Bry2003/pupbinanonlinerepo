<?php 
// Check if user is logged in, if not show popup and redirect to login page
if(!isset($_SESSION['userdata']) || !isset($_SESSION['userdata']['id']) || $_SESSION['userdata']['id'] <= 0){
    echo "<script>
        // Load the login popup script
        var script = document.createElement('script');
        script.src = './assets/js/login_popup.js';
        document.head.appendChild(script);
        
        // Show the popup after script loads
        script.onload = function() {
            showLoginRequiredPopup();
        };
        
        // Redirect after 3 seconds if user doesn't interact with popup
        setTimeout(function() {
            window.location.href = './login.php';
        }, 3000);
    </script>";
    exit;
}

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT a.* FROM `archive_list` a where a.id = '{$_GET['id']}'");
    if($qry->num_rows){
        foreach($qry->fetch_array() as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }
    $submitted = "N/A";
    if(isset($student_id)){
        $student = $conn->query("SELECT * FROM student_list where id = '{$student_id}'");
        if($student->num_rows > 0){
            $res = $student->fetch_array();
            $submitted = $res['email'];
        }
    }
}
?>
<style>
    #document_field{
        min-height:80vh;
        position: relative;
    }
    /* Disable user selection and context menu for document viewer */
    .disable-screenshot {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        /* Allow scrolling but prevent other interactions */
        pointer-events: auto;
    }
    /* Document container with watermark */
    .document-container {
        position: relative;
        width: 100%;
        height: 80vh;
    }
    /* Watermark overlay */
    .watermark-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none; /* Ensure watermark doesn't block interaction */
        z-index: 1000;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }
    .watermark {
        position: absolute;
        font-size: 3rem;
        color: rgba(0, 0, 0, 0.1);
        transform: rotate(-45deg);
        white-space: nowrap;
        pointer-events: none;
        user-select: none;
        z-index: 1000;
    }
</style>
<script>
    // Disable right-click context menu
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
    
    // Disable keyboard shortcuts for screenshots and printing
    document.addEventListener('keydown', function(e) {
        // Disable print screen (PrtSc)
        if (e.keyCode == 44) {
            e.preventDefault();
            alert("Screenshots are not allowed for this document.");
        }
        
        // Disable Ctrl+P (Print)
        if (e.ctrlKey && e.keyCode == 80) {
            e.preventDefault();
            alert("Printing is not allowed for this document.");
        }
        
        // Disable Ctrl+Shift+I (Developer Tools)
        if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
            e.preventDefault();
        }
    });
</script>
<div class="content py-4">
    <div class="col-12">
        <div class="card card-outline card-primary shadow rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    Archive - <?= isset($archive_code) ? $archive_code : "" ?>
                </h3>
            </div>
            <div class="card-body rounded-0">
                <div class="container-fluid">
                    <h2><b><?= isset($title) ? $title : "" ?></b></h2>
                    <small class="text-muted">Submitted by <b class="text-info"><?= $submitted ?></b> on  <?= date("F d, Y h:i A",strtotime($date_created)) ?></small>
                    <?php if(isset($student_id) && $_settings->userdata('login_type') == "2" && $student_id == $_settings->userdata('id')): ?>
                        <div class="form-group">
                            <a href="./?page=submit-archive&id=<?= isset($id) ? $id : "" ?>" class="btn btn-flat btn-default bg-navy btn-sm"><i class="fa fa-edit"></i> Edit</a>
                            <button type="button" data-id = "<?= isset($id) ? $id : "" ?>" class="btn btn-flat btn-danger btn-sm delete-data"><i class="fa fa-trash"></i> Delete</button>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <center>
                        <img src="<?= validate_image(isset($banner_path) ? $banner_path : "") ?>" alt="Banner Image" id="banner-img" class="img-fluid border bg-gradient-dark">
                    </center>
                    <fieldset>
                        <legend class="text-navy">Project Year:</legend>
                        <div class="pl-4"><large><?= isset($year) ? $year : "----" ?></large></div>
                    </fieldset>
                    <fieldset>
                        <legend class="text-navy">Abstract:</legend>
                        <div class="pl-4"><large><?= isset($abstract) ? html_entity_decode($abstract) : "" ?></large></div>
                    </fieldset>
                    <fieldset>
                        <legend class="text-navy">Members:</legend>
                        <div class="pl-4"><large><?= isset($members) ? html_entity_decode($members) : "" ?></large></div>
                    </fieldset>
                    <fieldset>
                        <legend class="text-navy">Project Document:</legend>
                        <div class="pl-4">
                            <div class="alert alert-warning">
                                <i class="fa fa-info-circle"></i> Note: Screenshots and printing of this document are disabled for copyright protection.
                            </div>
                            <div class="document-container">
                                <div class="watermark-overlay">
                                    <?php for($i = 0; $i < 10; $i++): ?>
                                        <div class="watermark" style="top: <?= $i * 10 ?>%;">PUP BIÑAN ONLINE REPOSITORY - CONFIDENTIAL</div>
                                    <?php endfor; ?>
                                </div>
                                <iframe src="<?= isset($document_path) ? base_url.$document_path : "" ?>" frameborder="0" id="document_field" class="text-center w-100 disable-screenshot" style="pointer-events: auto;">Loading Document ...</iframe>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.delete-data').click(function(){
            _conf("Are you sure to delete <b>Archive-<?= isset($archive_code) ? $archive_code : "" ?></b>","delete_archive")
        })
    })
    function delete_archive(){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_archive",
			method:"POST",
			data:{id: "<?= isset($id) ? $id : "" ?>"},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.replace("./");
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>