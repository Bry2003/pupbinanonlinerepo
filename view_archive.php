<?php 
// Determine if the viewer is a guest (not logged in)
$is_guest = true;
if(isset($_SESSION['userdata']) && isset($_SESSION['userdata']['id']) && $_SESSION['userdata']['id'] > 0){
    $is_guest = false;
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
    /* Block only print/download areas on the embedded viewer toolbar */
    .toolbar-blocker-left,
    .toolbar-blocker-right {
        position: absolute;
        top: 0;
        height: 64px; /* approximate toolbar height inside PDF viewers */
        z-index: 1100;
        background: transparent;
        pointer-events: auto; /* capture clicks so underlying toolbar cannot be used */
    }
    .toolbar-blocker-left { left: 0; width: 180px; }
    .toolbar-blocker-right { right: 0; width: 200px; }
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
    /* Overlay to block text selection within the document area */
    .selection-blocker {
        position: absolute;
        top: 64px; /* sit below the viewer toolbar */
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1080;
        background: transparent;
        pointer-events: none; /* allow scroll/zoom to pass through */
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        cursor: default;
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
        if (e.keyCode == 44 || e.key === 'PrintScreen') {
            e.preventDefault();
            alert("Screenshots are not allowed for this document.");
        }
        
        // Disable Ctrl+P (Print)
        if ((e.ctrlKey || e.metaKey) && (e.keyCode == 80 || e.key.toLowerCase() === 'p')) {
            e.preventDefault();
            alert("Printing is not allowed for this document.");
        }
        
        // Disable Windows Snipping Tool shortcut: Win+Shift+S
        if (e.shiftKey && (e.metaKey || e.key === 'Meta') && e.key.toLowerCase() === 's') {
            e.preventDefault();
        }
        
        // Disable Ctrl+Shift+I (Developer Tools)
        if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
            e.preventDefault();
        }

        // Block copy/cut/paste via keyboard shortcuts
        var key = (e.key || '').toLowerCase();
        if ((e.ctrlKey || e.metaKey) && (key === 'c' || e.keyCode === 67)) { // Ctrl+C
            e.preventDefault();
            alert("Copying is disabled for this document.");
        }
        if ((e.ctrlKey || e.metaKey) && (key === 'x' || e.keyCode === 88)) { // Ctrl+X
            e.preventDefault();
            alert("Cutting is disabled for this document.");
        }
        if ((e.ctrlKey || e.metaKey) && (key === 'v' || e.keyCode === 86)) { // Ctrl+V
            e.preventDefault();
            alert("Pasting is disabled on this page.");
        }
    });

    // Block clipboard events at the page level
    document.addEventListener('copy', function(e){
        if(e.clipboardData){
            try { e.clipboardData.setData('text/plain', ''); } catch(_) {}
        }
        e.preventDefault();
        alert("Copying is disabled for this document.");
    });
    document.addEventListener('cut', function(e){
        e.preventDefault();
        alert("Cutting is disabled for this document.");
    });
    document.addEventListener('paste', function(e){
        e.preventDefault();
        alert("Pasting is disabled on this page.");
    });

    // Extra protection: when print is initiated, hide the document container
    var mql = window.matchMedia('print');
    mql.addListener(function(mq) {
        var container = document.querySelector('.document-container');
        if(!container) return;
        if (mq.matches) {
            container.style.visibility = 'hidden';
        } else {
            container.style.visibility = 'visible';
        }
    });
    window.onbeforeprint = function(){
        var container = document.querySelector('.document-container');
        if(container) container.style.visibility = 'hidden';
    }
    window.onafterprint = function(){
        var container = document.querySelector('.document-container');
        if(container) container.style.visibility = 'visible';
    }
    // Removed scroll interception to allow native viewer scrolling
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
                            <button type="button" data-id="<?= isset($id) ? $id : "" ?>" class="btn btn-flat btn-danger bg-danger btn-sm delete-data"><i class="fa fa-trash"></i> Delete</button>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <center>
                        <img src="<?= validate_image(isset($banner_path) ? $banner_path : "") ?>" alt="Banner Image" id="banner-img" class="img-fluid border bg-gradient-dark">
                    </center>
                    <fieldset>
                        <?php if(!$is_guest): ?>
                        <legend class="text-navy">Project Year:</legend>
                        <div class="pl-4"><large><?= isset($year) ? $year : "----" ?></large></div>
                        <?php endif; ?>
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
                            <?php if($is_guest): ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> You are viewing as a guest. The abstract, cover banner, and members are visible. Please <a href="./login.php" class="text-navy"><b>log in</b></a> to view the full document and additional details.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fa fa-info-circle"></i> Note: Screenshots and printing of this document are disabled for copyright protection.
                                </div>
                                <div class="document-container">
                                    <!-- Transparent blockers targeting toolbar edges (print/menu on left, download on right) -->
                                    <div class="toolbar-blocker-left" title="Actions disabled"></div>
                                    <div class="toolbar-blocker-right" title="Actions disabled"></div>
                                    <div class="watermark-overlay">
                                        <?php for($i = 0; $i < 10; $i++): ?>
                                            <div class="watermark" style="top: <?= $i * 10 ?>%">PUP BIÑAN ONLINE REPOSITORY - CONFIDENTIAL</div>
                                        <?php endfor; ?>
                                    </div>
                                    <!-- Block selection over the document area while keeping toolbar usable -->
                                    <div class="selection-blocker" title="Text selection disabled"></div>
                                    <!-- Capture shield: temporarily shown to obscure content during capture events -->
                                    <div class="capture-shield" aria-hidden="true"></div>
                                    <iframe src="<?= validate_image(isset($document_path) ? $document_path : "") ?>" frameborder="0" id="document_field" class="text-center w-100 disable-screenshot" style="pointer-events: auto;">Loading Document ...</iframe>
                                </div>
                            <?php endif; ?>
                        </div>
                    </fieldset>
                    <?php 
                    $files = $conn->query("SELECT * FROM archive_files where archive_id = '{$id}'");
                    if(!$is_guest && $files->num_rows > 0): 
                    ?>
                    <fieldset>
                        <legend class="text-navy">Supporting Documents:</legend>
                        <div class="pl-4 row">
                            <?php while($row = $files->fetch_assoc()): ?>
                                <div class="col-md-3 mb-2">
                                    <a href="<?= validate_image($row['file_path']) ?>" target="_blank" class="btn btn-block btn-outline-primary btn-sm">
                                        <i class="fa fa-file"></i> <?= $row['original_name'] ?>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </fieldset>
                    <?php endif; ?>
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
