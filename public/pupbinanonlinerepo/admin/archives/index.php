<style>
    .img-avatar{
        width:45px;
        height:45px;
        object-fit:cover;
        object-position:center center;
        border-radius:100%;
    }
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Thesis Archives</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="container-fluid">
                <?php 
                    $program_id = isset($_GET['program_id']) ? $_GET['program_id'] : '';
                    $year = isset($_GET['year']) ? $_GET['year'] : '';
                    $programs = $conn->query("SELECT id, name FROM department_list WHERE status = 1 ORDER BY name ASC");
                    $prog_opts = $programs ? $programs->fetch_all(MYSQLI_ASSOC) : [];
                    $years = $conn->query("SELECT DISTINCT year FROM archive_list ORDER BY year DESC");
                    $year_opts = $years ? $years->fetch_all(MYSQLI_ASSOC) : [];
                ?>
                <div class="mb-2 d-flex align-items-center" style="gap:.5rem">
                    <label for="filter_program" class="mb-0">Program:</label>
                    <select id="filter_program" class="form-control form-control-sm" style="max-width:320px">
                        <option value="">All Programs</option>
                        <?php foreach($prog_opts as $po): ?>
                            <option value="<?= $po['id'] ?>" <?= $program_id == $po['id'] ? 'selected' : '' ?>><?= ucwords($po['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="filter_year" class="mb-0 ml-2">Year:</label>
                    <select id="filter_year" class="form-control form-control-sm" style="max-width:160px">
                        <option value="">All Years</option>
                        <?php foreach($year_opts as $yo): ?>
                            <option value="<?= $yo['year'] ?>" <?= $year == $yo['year'] ? 'selected' : '' ?>><?= $yo['year'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button id="export_excel" class="btn btn-sm btn-success ml-2">Download All (Excel)</button>
                </div>
				<table class="table table-hover table-striped">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="15%">
					<col width="20%">
					<col width="20%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Archive Code</th>
						<th>Project Title</th>
                        <th>Program</th>
                        <th>Adviser</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$i = 1;
                        $where = '';
                        if(!empty($program_id)){
                            $pid = $conn->real_escape_string($program_id);
                            $where = " WHERE COALESCE(NULLIF(a.curriculum_id,0), s.department_id) = '{$pid}'";
                        }
                        if(!empty($year)){
                            $yr = $conn->real_escape_string($year);
                            $where .= (empty($where) ? " WHERE " : " AND ") . " a.year = '{$yr}'";
                        }
                        // Join users to get adviser name
                        $sql = "SELECT a.*, d.name AS program_name, d.id AS program_id, sd.name AS student_program, sd.id AS student_program_id,
                                CONCAT(u.firstname, ' ', u.lastname) as adviser_name, s.adviser_id
                                FROM archive_list a
                                LEFT JOIN department_list d ON a.curriculum_id = d.id
                                LEFT JOIN student_list s ON a.student_id = s.id
                                LEFT JOIN department_list sd ON s.department_id = sd.id
                                LEFT JOIN users u ON s.adviser_id = u.id
                                ".$where." ORDER BY a.year DESC, a.title DESC";
                        $qry = $conn->query($sql);
						while($row = $qry->fetch_assoc()):
                            $is_my_student = true;
                            // Restriction: If logged in user is Adviser (2), check if this is their student
                            if($_settings->userdata('login_type') == 2){
                                if($row['adviser_id'] != $_settings->userdata('id')){
                                    $is_my_student = false;
                                }
                            }
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td class=""><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo ($row['archive_code']) ?></td>
							<td><?php echo ucwords($row['title']) ?></td>
                            <td>
                                <?php 
                                    $prog = !empty($row['program_name']) ? $row['program_name'] : (!empty($row['student_program']) ? $row['student_program'] : '');
                                    $link_id = !empty($row['program_id']) ? $row['program_id'] : (!empty($row['student_program_id']) ? $row['student_program_id'] : '');
                                ?>
                                <?php if(!empty($prog) && !empty($link_id)): ?>
                                    <a href="<?php echo base_url ?>admin/?page=archives&program_id=<?= $link_id ?>" class="text-navy"><?= ucwords($prog) ?></a>
                                <?php elseif(!empty($prog)): ?>
                                    <span class="text-muted"><?= ucwords($prog) ?></span>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo !empty($row['adviser_name']) ? ucwords($row['adviser_name']) : '<span class="text-muted font-italic">Unassigned</span>' ?></td>
							<td class="text-center">
								<?php
                                    switch($row['status']){
                                        case '1':
                                            echo "<span class='badge badge-success badge-pill'>Published</span>";
                                            break;
                                        case '0':
                                            echo "<span class='badge badge-secondary badge-pill'>Not Published</span>";
                                            break;
                                        case '2':
                                            echo "<span class='badge badge-primary badge-pill'>Adviser Verified</span>";
                                            break;
                                    }
                                ?>
							</td>
                            <td align="center">
                                <?php if($is_my_student || $_settings->userdata('login_type') == 1): ?>
                                <button type="button" class="btn btn-flat btn-default btn-sm action_menu_btn" data-id="<?php echo $row['id'] ?>" data-status="<?php echo $row['status'] ?>">
                                    Action
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-flat btn-default btn-sm" disabled title="Not your student">
                                    <i class="fa fa-lock"></i>
                                </button>
                                <?php endif; ?>
                            </td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
    $(document).ready(function(){
        function buildUrl(){
            const base = '<?php echo base_url ?>admin/?page=archives';
            const pid = $('#filter_program').val();
            const yr = $('#filter_year').val();
            let url = base;
            if(pid) url += '&program_id=' + encodeURIComponent(pid);
            if(yr) url += '&year=' + encodeURIComponent(yr);
            return url;
        }
        $('#filter_program').on('change', function(){
            location.href = buildUrl();
        });
        $('#filter_year').on('change', function(){
            location.href = buildUrl();
        });
        $('#export_excel').on('click', function(){
            const pid = $('#filter_program').val();
            const yr = $('#filter_year').val();
            let url = '<?php echo base_url ?>classes/Master.php?f=export_archives_xlsx';
            if(pid) url += '&program_id=' + encodeURIComponent(pid);
            if(yr) url += '&year=' + encodeURIComponent(yr);
            window.location = url;
        });
        $('.action_menu_btn').click(function(){
            var id = $(this).data('id');
            var status = $(this).data('status');
            uni_modal('Actions', 'inc/action_menu.php?context=archives&id=' + id + '&status=' + status);
        })
        $('.table td,.table th').addClass('py-1 px-2 align-middle')
        $('.table').dataTable({
            columnDefs: [
                { orderable: false, targets: 5 }
            ],
        });
	})
	function delete_archive($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_archive",
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
