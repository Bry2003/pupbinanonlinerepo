<?php 
if(isset($_GET['id'])){

    $qry = $conn->query("SELECT * FROM curriculum_list where `status` = 1 and id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            if(!is_numeric($k)){
                $curriculum[$k] = $v;
            }
        }
    }else{
        echo "<script> alert('Unkown Course ID'); location.replace('./') </script>";
    }

}else{
    echo "<script> alert('Course ID is required'); location.replace('./') </script>";
}

?>
<div class="content py-2">
    <div class="col-12">
        <div class="card card-outline card-primary shadow rounded-0">
            <div class="card-body rounded-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2>Archive List of <?= isset($curriculum['name']) ? $curriculum['name'] : "" ?> </h2>
                        <p class="mb-0"><small><?= isset($curriculum['description']) ? $curriculum['description'] : "" ?></small></p>
                    </div>
                    <?php if(isset($_settings) && $_settings->userdata('id') > 0): ?>
                        <div>
                            <a class="btn btn-sm btn-navy" href="./?page=submit-archive&program_id=<?= htmlspecialchars($id) ?>">Submit Thesis/Capstone for this Program</a>
                        </div>
                    <?php endif; ?>
                </div>
                <hr class="bg-navy">
                <?php 
                $id = isset($_GET['id']) ? $_GET['id'] : '';
                $limit = 10;
                $page = isset($_GET['p'])? $_GET['p'] : 1; 
                $offset = 10 * ($page - 1);
                $paginate = " limit {$limit} offset {$offset}";
                $wherecid = " and COALESCE(NULLIF(a.curriculum_id,0), s.curriculum_id) = '{$id}' ";
                $students = $conn->query("SELECT * FROM `student_list` where id in (SELECT a.student_id FROM archive_list a LEFT JOIN student_list s ON a.student_id = s.id where a.`status` = 1 {$wherecid})");
                $student_arr = ($students && $students->num_rows) ? array_column($students->fetch_all(MYSQLI_ASSOC),'email','id') : [];
                $cnt_q = $conn->query("SELECT COUNT(*) AS c FROM archive_list a LEFT JOIN student_list s ON a.student_id = s.id where a.`status` = 1 {$wherecid}");
                $count_all = ($cnt_q && ($r = $cnt_q->fetch_assoc())) ? (int)$r['c'] : 0;
                $pages = max(1, ceil($count_all/$limit));
                $archives = $conn->query("SELECT a.* FROM archive_list a LEFT JOIN student_list s ON a.student_id = s.id where a.`status` = 1 {$wherecid} order by unix_timestamp(a.date_created) desc {$paginate}");    
                ?>
                <div class="list-group">
                    <?php if($archives && $archives->num_rows): ?>
                    <?php while($row = $archives->fetch_assoc()):
                        $row['abstract'] = strip_tags(html_entity_decode($row['abstract']));
                    ?>
                    <a href="./?page=view_archive&id=<?= $row['id'] ?>" class="text-decoration-none text-dark list-group-item list-group-item-action">
                        <div class="row">
                            <div class="col-lg-4 col-md-5 col-sm-12 text-center">
                                <img src="<?= validate_image($row['banner_path']) ?>" class="banner-img img-fluid bg-gradient-dark" alt="Banner Image">
                            </div>
                            <div class="col-lg-8 col-md-7 col-sm-12">
                                <h3 class="text-navy"><b><?php echo $row['title'] ?></b></h3>
                                <small class="text-muted">By <b class="text-info"><?= isset($student_arr[$row['student_id']]) ? $student_arr[$row['student_id']] : "N/A" ?></b></small>
                                <p class="truncate-5"><?= $row['abstract'] ?></p>
                            </div>
                        </div>
                    </a>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <div class="text-center text-muted py-4">No projects found for this course.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer clearfix rounded-0">
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-6"><span class="text-muted">Display Items: <?= ($archives && $archives->num_rows) ? $archives->num_rows : 0 ?></span></div>
                        <div class="col-md-6">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <li class="page-item"><a class="page-link" href="./?page=projects_per_curriculum&id=<?= $id ?>&p=<?= $page - 1 ?>" <?= $page == 1 ? 'disabled' : '' ?>>«</a></li>
                                <?php for($i = 1; $i<= $pages; $i++): ?>
                                <li class="page-item"><a class="page-link <?= $page == $i ? 'active' : '' ?>" href="./?page=projects_per_curriculum&id=<?= $id ?>&p=<?= $i ?>"><?= $i ?></a></li>
                                <?php endfor; ?>
                                <li class="page-item"><a class="page-link" href="./?page=projects_per_curriculum&id=<?= $id ?>&p=<?= $page + 1 ?>" <?= $page == $pages || $pages <= 1 ? 'disabled' : '' ?>>»</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
