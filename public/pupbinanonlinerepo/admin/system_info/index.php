<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: scale-down;
		border-radius: 100% 100%;
	}
	img#cimg2{
		height: 50vh;
		width: 100%;
		object-fit: contain;
		/* border-radius: 100% 100%; */
	}
	video#cvid2{
		height: 50vh;
		width: 100%;
		object-fit: contain;
	}
</style>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<h5 class="card-title">System Information</h5>
			<!-- <div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_department" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
			</div> -->
		</div>
		<div class="card-body">
			<form action="" id="system-frm">
			<div id="msg" class="form-group"></div>
			<div class="form-group">
				<label for="name" class="control-label">System Name</label>
				<input type="text" class="form-control form-control-sm" name="name" id="name" value="<?php echo $_settings->info('name') ?>">
			</div>
			<div class="form-group">
				<label for="short_name" class="control-label">System Short Name</label>
				<input type="text" class="form-control form-control-sm" name="short_name" id="short_name" value="<?php echo  $_settings->info('short_name') ?>">
			</div>
			<div class="form-group">
				<label for="motto" class="control-label">System Motto</label>
				<input type="text" class="form-control form-control-sm" name="motto" id="motto" value="<?php echo  $_settings->info('motto') ?>">
			</div>
			<fieldset>
				<legend>Contact Information</legend>
				<div class="form-group">
					<label for="school_address" class="control-label">School Address</label>
					<input type="text" class="form-control form-control-sm" name="school_address" id="school_address" value="<?php echo  $_settings->info('school_address') ?>">
				</div>
				<div class="form-group">
					<label for="contact_number" class="control-label">Contact Number</label>
					<input type="text" class="form-control form-control-sm" name="contact_number" id="contact_number" value="<?php echo  $_settings->info('contact_number') ?>">
				</div>
				<div class="form-group">
					<label for="email_address" class="control-label">Email Address</label>
					<input type="text" class="form-control form-control-sm" name="email_address" id="email_address" value="<?php echo  $_settings->info('email_address') ?>">
				</div>
			</fieldset>
			<div class="form-group">
				<label for="content[about_us]" class="control-label">Welcome Content</label>
				<textarea type="text" class="form-control form-control-sm summernote" name="content[welcome]" id="welcome"><?php echo  is_file(base_app.'welcome.html') ? file_get_contents(base_app.'welcome.html') : '' ?></textarea>
			</div>
			<div class="form-group">
				<label for="content[about_us]" class="control-label">About Us</label>
				<textarea type="text" class="form-control form-control-sm summernote" name="content[about_us]" id="about_us"><?php echo  is_file(base_app.'about_us.html') ? file_get_contents(base_app.'about_us.html') : '' ?></textarea>
			</div>
			<div class="form-group">
				<label for="" class="control-label">System Logo</label>
				<div class="custom-file">
	              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
	              <label class="custom-file-label" for="customFile">Choose file</label>
	            </div>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
			</div>
			<div class="form-group">
				<label for="" class="control-label">Cover</label>
				<div class="custom-file">
	              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="cover" accept="image/*,video/*" onchange="displayImg2(this,$(this))">
			      <label class="custom-file-label" for="customFile">Choose file</label>
		        </div>
			</div>
			<?php
			$__cover_url = validate_image($_settings->info('cover'));
			$__is_video = preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $__cover_url);
			?>
			<div class="form-group d-flex justify-content-center">
				<?php if($__is_video): ?>
					<video src="<?php echo $__cover_url ?>" id="cvid2" class="img-fluid img-thumbnail bg-gradient-dark border-dark" autoplay muted loop></video>
					<img src="<?php echo $__cover_url ?>" alt="" id="cimg2" class="img-fluid img-thumbnail bg-gradient-dark border-dark" style="display:none">
				<?php else: ?>
					<img src="<?php echo $__cover_url ?>" alt="" id="cimg2" class="img-fluid img-thumbnail bg-gradient-dark border-dark">
					<video src="" id="cvid2" class="img-fluid img-thumbnail bg-gradient-dark border-dark" autoplay muted loop style="display:none"></video>
				<?php endif; ?>
			</div>
			<fieldset>
				<legend>Theme Settings</legend>
				<div class="row">
					<div class="col-md-4">
						<label class="control-label">Primary (Maroon)</label>
						<input type="color" class="form-control form-control-sm" name="theme_maroon" value="<?php echo $_settings->info('theme_maroon') ?: '#800000' ?>">
					</div>
					<div class="col-md-4">
						<label class="control-label">Accent</label>
						<input type="color" class="form-control form-control-sm" name="theme_accent" value="<?php echo $_settings->info('theme_accent') ?: '#007bff' ?>">
					</div>
					<div class="col-md-4">
						<label class="control-label">Text (Dark)</label>
						<input type="color" class="form-control form-control-sm" name="theme_text_dark" value="<?php echo $_settings->info('theme_text_dark') ?: '#333333' ?>">
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-md-6">
						<label class="control-label">Secondary</label>
						<input type="color" class="form-control form-control-sm" name="theme_secondary" value="<?php echo $_settings->info('theme_secondary') ?: '#343a40' ?>">
					</div>
					<div class="col-md-6">
						<label class="control-label">Font Family</label>
						<input type="text" class="form-control form-control-sm" name="font_family" placeholder="e.g., Source Sans Pro" value="<?php echo $_settings->info('font_family') ?: 'Source Sans Pro' ?>">
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>Login Page Text</legend>
				<div class="form-group">
					<label for="login_title" class="control-label">Login Title</label>
					<input type="text" class="form-control form-control-sm" name="login_title" id="login_title" value="<?php echo $_settings->info('login_title') ?: 'Hi, PUPian!' ?>">
				</div>
				<div class="form-group">
					<label for="login_subtitle" class="control-label">Login Subtitle</label>
					<input type="text" class="form-control form-control-sm" name="login_subtitle" id="login_subtitle" value="<?php echo $_settings->info('login_subtitle') ?: 'Please click or tap your destination.' ?>">
				</div>
			</fieldset>
			<fieldset>
				<legend>School Information</legend>
				<div class="form-group">
					<label for="email" class="control-label">Email</label>
					<input type="email" class="form-control form-control-sm" name="email" id="email" value="<?php echo $_settings->info('email') ?>">
				</div>
				<div class="form-group">
					<label for="contact" class="control-label">Contact #</label>
					<input type="text" class="form-control form-control-sm" name="contact" id="contact" value="<?php echo $_settings->info('contact') ?>">
				</div>
				<div class="form-group">
					<label for="address" class="control-label">Address</label>
					<textarea rows="3" class="form-control form-control-sm" name="address" id="address" style="resize:none"><?php echo $_settings->info('address') ?></textarea>
				</div>
			</fieldset>
			</form>
		</div>
		<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
                    <button class="btn btn-sm btn-primary" type="submit" form="system-frm">Update</button>
                </div>
            </div>
        </div>

</div>
</div>
<!-- FAQs Manager -->
<div class="col-lg-12 mt-3">
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h5 class="card-title">FAQs Manager</h5>
            <p class="text-muted mb-0">Create, edit, and publish FAQs used by the Help Bot and the public FAQs page.</p>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form id="faq-form">
                        <input type="hidden" name="id" id="faq-id">
                        <div class="form-group">
                            <label class="control-label">Question</label>
                            <input type="text" class="form-control form-control-sm" name="question" id="faq-question" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Answer</label>
                            <textarea class="form-control form-control-sm summernote" name="answer" id="faq-answer" rows="6" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Tags (comma separated)</label>
                            <input type="text" class="form-control form-control-sm" name="tags" id="faq-tags" placeholder="login, register, archives">
                        </div>
                        <div class="form-row">
                            <div class="col-md-4">
                                <label class="control-label">Status</label>
                                <select class="form-control form-control-sm" name="status" id="faq-status">
                                    <option value="1">Published</option>
                                    <option value="0">Draft</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Order</label>
                                <input type="number" class="form-control form-control-sm" name="sort_order" id="faq-order" placeholder="optional">
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-sm btn-primary">Save FAQ</button>
                            <button type="button" id="faq-reset" class="btn btn-sm btn-secondary">Reset</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="faq-table">
                            <thead>
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Question</th>
                                    <th>Tags</th>
                                    <th style="width:100px">Status</th>
                                    <th style="width:120px">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
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
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	function displayImg2(input,_this) {
	    if (input.files && input.files[0]) {
	        var file = input.files[0];
	        var type = file.type || '';
	        _this.siblings('.custom-file-label').html(file.name);
	        if (type.indexOf('video/') === 0) {
	            var url = URL.createObjectURL(file);
	            $('#cvid2').attr('src', url).show();
	            $('#cimg2').hide();
	        } else {
	            var reader = new FileReader();
	            reader.onload = function (e) {
	                $('#cimg2').attr('src', e.target.result).show();
	                $('#cvid2').hide();
	            }
	            reader.readAsDataURL(file);
	        }
	    }
	}
	function displayImg3(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        	$('#cimg3').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$(document).ready(function(){
		function makeUploadButton(){
			var ui = $.summernote.ui;
			return ui.button({
				contents: '<i class="note-icon-picture"></i>',
				tooltip: 'Upload Image/Video',
				click: function(){
					var input = $('<input type="file" accept="image/*,video/*" multiple style="display:none"/>');
					$('body').append(input);
					input.on('change', function(){
						var files = this.files;
						if(!files || !files.length) { input.remove(); return; }
						doUpload(files, window.currentNote || $('#welcome'));
						input.remove();
					});
					input.trigger('click');
				}
			}).render();
		}
		function doUpload(files, note){
			Array.from(files).forEach(function(file){
				var fd = new FormData();
				fd.append('file', file);
				$.ajax({
					url: _base_url_ + 'classes/SystemSettings.php?f=upload_media',
					method: 'POST',
					data: fd,
					cache: false,
					contentType: false,
					processData: false,
					success: function(resp){
						try{ if(typeof resp === 'string') resp = JSON.parse(resp); }catch(e){}
						if(resp && resp.status === 'success' && resp.url){
							if(/^video\//.test(file.type)){
								var v = $('<video controls src="'+resp.url+'" style="max-width:100%"></video>')[0];
								note.summernote('insertNode', v);
							}else{
								note.summernote('insertImage', resp.url, file.name);
							}
						}else{
							alert_toast('Upload failed','danger');
						}
					},
					error: function(){ alert_toast('Network error','danger'); }
				});
			});
		}
		var currentNote = null;
		 $('.summernote').on('summernote.focus', function(){ currentNote = $(this); window.currentNote = currentNote; });
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
					['insert', ['link', 'picture', 'video', 'uploadMedia']],
		            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		        ]
				, buttons: { uploadMedia: makeUploadButton }
				, callbacks: {
					onImageUpload: function(files){ doUpload(files, $(this)); }
				}
		    })
			$('#system-frm').on('submit', function(e){
				e.preventDefault();
				var fd = new FormData(this);
				$.ajax({
					url: _base_url_ + 'classes/SystemSettings.php?f=update_settings',
					method: 'POST',
					data: fd,
					cache: false,
					contentType: false,
					processData: false,
					success: function(){
						alert_toast('System Info Successfully Updated.','success');
						setTimeout(function(){ location.reload(); }, 800);
					},
					error: function(){ alert_toast('Network error','danger'); }
				});
			});

    // FAQs Manager JS
    const $faqTbody = $('#faq-table tbody');
    const apiBase = _base_url_ + 'classes/FAQs.php?';
    function loadFaqs(){
        $.get(apiBase + 'f=list', function(resp){
            try{
                if(typeof resp === 'string') resp = JSON.parse(resp);
            }catch(e){}
            $faqTbody.empty();
            if(resp && resp.status === 'success' && Array.isArray(resp.data)){
                resp.data.forEach(function(row, idx){
                    const badge = row.status == 1 ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-secondary">Draft</span>';
                    const tr = $('<tr/>');
                    tr.append('<td>'+(row.sort_order ?? idx+1)+'</td>');
                    tr.append('<td>'+escapeHtml(row.question)+'</td>');
                    tr.append('<td>'+escapeHtml(row.tags || '')+'</td>');
                    tr.append('<td class="text-center">'+badge+'</td>');
                    const actions = $('<td class="text-right"/>');
                    const editBtn = $('<button class="btn btn-sm btn-primary mr-1">Edit</button>').on('click', function(){ fillForm(row); });
                    const delBtn = $('<button class="btn btn-sm btn-danger">Delete</button>').on('click', function(){ deleteFaq(row.id); });
                    actions.append(editBtn).append(delBtn);
                    tr.append(actions);
                    $faqTbody.append(tr);
                });
            }else{
                $faqTbody.append('<tr><td colspan="5" class="text-center text-muted">No FAQs found</td></tr>');
            }
        });
    }
    function escapeHtml(s){ return String(s||'').replace(/[&<>]/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;'})[c]; }); }
    function fillForm(row){
        $('#faq-id').val(row.id);
        $('#faq-question').val(row.question);
        $('#faq-answer').summernote('code', row.answer);
        $('#faq-tags').val(row.tags || '');
        $('#faq-status').val(row.status);
        $('#faq-order').val(row.sort_order || '');
    }
    function resetForm(){
        $('#faq-id').val('');
        $('#faq-question').val('');
        $('#faq-answer').summernote('code', '');
        $('#faq-tags').val('');
        $('#faq-status').val('1');
        $('#faq-order').val('');
    }
    function deleteFaq(id){
        if(!confirm('Delete this FAQ?')) return;
        $.post(apiBase + 'f=delete', {id:id}, function(resp){
            try{ if(typeof resp === 'string') resp = JSON.parse(resp); }catch(e){}
            if(resp && resp.status === 'success'){
                alert_toast('FAQ deleted','success');
                loadFaqs();
            }else{
                alert_toast('Failed to delete FAQ','danger');
            }
        });
    }
    $('#faq-form').on('submit', function(e){
        e.preventDefault();
        const fd = new FormData(this);
        $.ajax({
            url: apiBase + 'f=save',
            method: 'POST',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            success: function(resp){
                try{ if(typeof resp === 'string') resp = JSON.parse(resp); }catch(e){}
                if(resp && resp.status === 'success'){
                    alert_toast('FAQ saved','success');
                    resetForm();
                    loadFaqs();
                }else{
                    alert_toast((resp && resp.msg) ? resp.msg : 'Failed to save FAQ','danger');
                }
            },
            error: function(){ alert_toast('Network error','danger'); }
        })
    });
    $('#faq-reset').on('click', resetForm);
		loadFaqs();
		})
</script>
