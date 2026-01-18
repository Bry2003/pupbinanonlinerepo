<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * FROM `archive_list` where id = '{$_GET['id']}'");
    if($qry->num_rows){
        foreach($qry->fetch_array() as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }
    if(isset($student_id)){
        if($student_id != $_settings->userdata('id')){
            echo "<script> alert('You don\'t have an access to this page'); location.replace('./'); </script>";
        }
    }
}
?>
<style>
    .banner-img{
		object-fit:scale-down;
		object-position:center center;
        height:30vh;
        width:calc(100%);
	}
</style>
<style>
    #upload-overlay{
        position:fixed;
        inset:0;
        background:rgba(12,0,0,.78);
        backdrop-filter:blur(2px);
        display:none;
        align-items:center;
        justify-content:center;
        z-index:9999;
    }
    #upload-overlay .panel{
        width:90%;
        max-width:420px;
        text-align:center;
        color:#fff;
        font-family:'Times New Roman', Times, serif;
        background:linear-gradient(145deg,var(--pup-maroon,#7a0019) 0%,#540013 50%,#2b0006 100%);
        border-radius:18px;
        padding:22px 26px 24px;
        box-shadow:0 22px 45px rgba(0,0,0,.65);
        position:relative;
        overflow:hidden;
    }
    #upload-overlay .panel::before{
        content:"";
        position:absolute;
        inset:-40%;
        background:radial-gradient(circle at 0 0,rgba(255,215,0,.24),transparent 55%),radial-gradient(circle at 100% 100%,rgba(255,215,0,.18),transparent 55%);
        opacity:.85;
        pointer-events:none;
    }
    #upload-overlay .panel-inner{
        position:relative;
        z-index:1;
    }
    .upload-brand{
        display:flex;
        align-items:center;
        justify-content:center;
        gap:.85rem;
        margin-bottom:1.25rem;
    }
    .upload-logo-wrap{
        width:54px;
        height:54px;
        border-radius:50%;
        background:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        box-shadow:0 0 0 3px rgba(0,0,0,.18);
        overflow:hidden;
    }
    .upload-logo{
        width:44px;
        height:44px;
        object-fit:contain;
    }
    .upload-brand-text{
        text-align:left;
    }
    .upload-title{
        font-size:1rem;
        font-weight:700;
        letter-spacing:.04em;
        text-transform:uppercase;
    }
    .upload-subtitle{
        font-size:.88rem;
        opacity:.9;
    }
    .ring{
        width:150px;
        height:150px;
        margin:0 auto 18px;
        border-radius:50%;
        background:conic-gradient(var(--pup-gold,#f3b233) 0%, var(--pup-gold,#f3b233) 0%, rgba(255,255,255,.12) 0);
        box-shadow:0 0 18px rgba(243,178,51,.9),0 0 0 3px rgba(0,0,0,.35) inset;
        position:relative;
    }
    .ring::after{
        content:"";
        position:absolute;
        inset:14px;
        border-radius:50%;
        background:rgba(10,2,2,.95);
    }
    .percent-label{
        font-size:20px;
        letter-spacing:.12em;
        margin-top:6px;
    }
    .status-label{
        font-size:14px;
        opacity:.9;
        margin-top:4px;
    }
    @media (max-width:576px){
        #upload-overlay .panel{
            max-width:340px;
            padding:18px 18px 20px;
        }
        .upload-title{
            font-size:.9rem;
        }
        .upload-subtitle{
            font-size:.8rem;
        }
        .ring{
            width:132px;
            height:132px;
        }
    }
</style>
<div id="upload-overlay">
    <div class="panel">
        <div class="panel-inner">
            <div class="upload-brand">
                <div class="upload-logo-wrap">
                    <img src="<?= validate_image($_settings->info('logo')) ?>" alt="PUP Logo" class="upload-logo">
                </div>
                <div class="upload-brand-text">
                    <div class="upload-title">PUP Biñan Online Repository</div>
                    <div class="upload-subtitle">Please keep this tab open while we upload.</div>
                </div>
            </div>
            <div id="overlayRing" class="ring"></div>
            <div class="percent-label"><span id="overlayPct">0</span>%</div>
            <div id="overlayStatus" class="status-label">Uploading…</div>
        </div>
    </div>
</div>
<div class="content py-4">
    <div class="card card-outline card-primary shadow rounded-0">
        <div class="card-header rounded-0">
            <h5 class="card-title"><?= isset($id) ? "Update Archive-{$archive_code} Details" : "Submit Project" ?></h5>
        </div>
        <div class="card-body rounded-0">
            <div class="container-fluid">
                <form action="" id="archive-form">
                    <input type="hidden" name="id" value="<?= isset($id) ? $id : "" ?>">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="title" class="control-label text-navy">Project Title</label>
                                <input type="text" name="title" id="title" autofocus placeholder="Project Title" class="form-control form-control-border" value="<?= isset($title) ?$title : "" ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="year" class="control-label text-navy">Year</label>
                                <input type="number" name="year" id="year" class="form-control form-control-border" placeholder="YYYY" min="1900" max="<?= date("Y") ?>" value="<?= isset($year) ? $year : "" ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="abstract" class="control-label text-navy">Abstract</label>
                                <textarea rows="3" name="abstract" id="abstract" placeholder="abstract" class="form-control form-control-border summernote" required><?= isset($abstract) ? html_entity_decode($abstract) : "" ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="members" class="control-label text-navy">Project Members</label>
                                <textarea rows="3" name="members" id="members" placeholder="members" class="form-control form-control-border summernote-list-only" required><?= isset($members) ? html_entity_decode($members) : "" ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="img" class="control-label text-muted">Project Image/Banner Image</label>
                                <input type="file" id="img" name="img" class="form-control form-control-border" accept="image/png,image/jpeg" onchange="displayImg(this,$(this))" <?= !isset($id) ? "required" : "" ?>>
                            </div>

                            <div class="form-group text-center">
                                <img src="<?= validate_image(isset($banner_path) ? $banner_path : "") ?>" alt="My Avatar" id="cimg" class="img-fluid banner-img bg-gradient-dark border">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="pdf" class="control-label text-muted">Project Document (PDF File Only)</label>
                                <input type="file" id="pdf" name="pdf" class="form-control form-control-border" accept="application/pdf" <?= !isset($id) ? "required" : "" ?>>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="support_files" class="control-label text-muted">Supporting Documents/Images (Upload 5 or more)</label>
                                <input type="file" id="support_files" name="support_files[]" class="form-control form-control-border" multiple>
                                <small class="text-muted">You can select multiple files.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group text-center">
                                <button class="btn btn-default bg-navy btn-flat"> Update</button>
                                <a href="./?page=profile" class="btn btn-light border btn-flat"> Cancel</a>
                            </div>
                            <div class="mt-2">
                                <div id="upload-progress" class="progress" style="height:20px; display:none">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-navy" role="progressbar" style="width:0%"></div>
                                </div>
                                <div id="upload-progress-label" class="small text-muted" style="display:none">Uploading…</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }else{
            $('#cimg').attr('src', "<?= validate_image(isset($avatar) ? $avatar : "") ?>");
        }
	}
    $(function(){
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                [ 'style', [ 'style' ] ],
                [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
                [ 'fontname', [ 'fontname' ] ],
                [ 'fontsize', [ 'fontsize' ] ],
                [ 'color', [ 'color' ] ],
                [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
                [ 'table', [ 'table' ] ],
                ['insert', ['link', 'picture']],
                [ 'view', [ 'undo', 'redo', 'help' ] ]
            ]
        })
        $('.summernote-list-only').summernote({
            height: 200,
            toolbar: [
                [ 'font', [ 'bold', 'italic', 'clear'] ],
                [ 'fontname', [ 'fontname' ] ]
                [ 'color', [ 'color' ] ],
                [ 'para', [ 'ol', 'ul' ] ],
                [ 'view', [ 'undo', 'redo', 'help' ] ]
            ]
        })
        // Archive Form Submit
        $('#archive-form').submit(function(e){
            e.preventDefault()
            var _this = $(this)
                $(".pop-msg").remove()
            var el = $("<div>")
                el.addClass("alert pop-msg my-2")
                el.hide()
            $('#upload-overlay').css('display','flex');
            $('#overlayStatus').text('Preparing…');
            $('#overlayPct').text('0');
            $('#overlayRing').css('background','conic-gradient(var(--pup-gold,#f3b233) 0%, rgba(255,255,255,.12) 0)');
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_archive",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType:'json',
                xhr: function(){
                    var xhr = new window.XMLHttpRequest();
                    $('#upload-progress').show();
                    $('#upload-progress-label').show().text('Uploading…');
                    $('#upload-overlay').css('display','flex');
                    $('#overlayStatus').text('Uploading…');
                    $('#overlayPct').text('0');
                    $('#overlayRing').css('background','conic-gradient(var(--pup-gold,#f3b233) 0%, rgba(255,255,255,.12) 0)');
                    xhr.upload.addEventListener('progress', function(e){
                        if(e.lengthComputable){
                            var pct = Math.round((e.loaded / e.total) * 100);
                            $('#upload-progress .progress-bar').css('width', pct + '%');
                            $('#overlayPct').text(pct);
                            $('#overlayRing').css('background','conic-gradient(var(--pup-gold,#f3b233) '+pct+'%, rgba(255,255,255,.12) 0)');

                            if(pct >= 100){
                                $('#upload-progress-label').text('Uploaded Successfully');
                                $('#overlayStatus').text('Uploaded Successfully');
                            }else{
                                $('#upload-progress-label').text('Uploading… ' + pct + '%');
                                $('#overlayStatus').text('Uploading…');
                            }
                        }
                    });
                    xhr.addEventListener('load', function(){
                        $('#upload-progress .progress-bar').css('width', '100%');
                        $('#upload-progress-label').text('Uploaded Successfully');
                        $('#overlayStatus').text('Uploaded Successfully');
                        $('#overlayRing').css('background','conic-gradient(var(--pup-gold,#f3b233) 100%, rgba(255,255,255,.12) 0)');
                    });
                    return xhr;
                },
                error:err=>{
                    console.log(err)
                    el.text("An error occured while saving the data")
                    el.addClass("alert-danger")
                    _this.prepend(el)
                    el.show('slow')
                    end_loader()
                    $('#upload-progress-label').text('Failed').delay(1000).fadeOut();
                    $('#upload-progress').delay(1000).fadeOut(function(){ $('#upload-progress .progress-bar').css('width','0%'); });
                    $('#overlayStatus').text('Failed');
                    setTimeout(function(){
                        $('#upload-overlay').fadeOut();
                        $('#overlayPct').text('0');
                        $('#overlayRing').css('background','conic-gradient(var(--pup-gold,#f3b233) 0%, rgba(255,255,255,.12) 0)');
                    }, 1000);
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        $('#overlayStatus').text('Done');
                        $('#overlayPct').text('100');
                        $('#overlayRing').css('background','conic-gradient(var(--pup-gold,#f3b233) 100%, rgba(255,255,255,.12) 0)');
                        setTimeout(function(){
                            location.href= "./?page=view_archive&id="+resp.id
                        }, 250);
                    }else if(!!resp.msg){
                        el.text(resp.msg)
                        el.addClass("alert-danger")
                        _this.prepend(el)
                        el.show('show')
                    }else{
                        el.text("An error occured while saving the data")
                        el.addClass("alert-danger")
                        _this.prepend(el)
                        el.show('show')
                    }
                    end_loader();
                    $('html, body').animate({scrollTop: 0},'fast')
                    $('#upload-progress-label').text('Done').delay(500).fadeOut();
                    $('#upload-progress').delay(500).fadeOut(function(){ $('#upload-progress .progress-bar').css('width','0%'); });
                    setTimeout(function(){
                        $('#upload-overlay').fadeOut();
                        $('#overlayPct').text('0');
                        $('#overlayRing').css('background','conic-gradient(var(--pup-gold,#f3b233) 0%, rgba(255,255,255,.12) 0)');
                    }, 500);
                }
            })
        })
        $('#year').on('input change', function(){
            var max = parseInt($(this).attr('max'));
            var val = parseInt($(this).val());
            if(val > max){
                $(this).val(max);
            }
        });
    })
</script>
