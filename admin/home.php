<div class="row mb-4 animate__animated animate__fadeInDown">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, var(--pup-maroon) 0%, #4a0000 100%); color: white; border-radius: 15px; overflow: hidden;">
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-md-8 position-relative" style="z-index: 2;">
                        <h1 class="font-weight-bold mb-2 text-white" style="font-family: 'Segoe UI', sans-serif;">Welcome Back, <?= (int)($_settings->userdata('type') ?? 0) === 1 ? 'Super Admin' : 'Admin' ?>!</h1>
                <p class="lead mb-0 text-white" style="opacity: 0.9;">Manage your <?php echo $_settings->info('name') ?> portal efficiently.</p>
                <p class="small mt-2 text-white"><i class="far fa-calendar-alt mr-2"></i> <?php echo date('l, F j, Y'); ?></p>
                    </div>
                    <div class="col-md-4 text-right d-none d-md-block position-relative" style="z-index: 2;">
                        <i class="fas fa-university fa-6x" style="opacity: 0.3; color: var(--pup-gold);"></i>
                    </div>
                </div>
                <!-- Abstract Shapes Background -->
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255, 215, 0, 0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -30px; left: 50px; width: 100px; height: 100px; background: rgba(255, 255, 255, 0.05); border-radius: 50%;"></div>
            </div>
        </div>
    </div>
</div>

<style>
  .info-box {
      background: #fff;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      border-radius: 15px;
      overflow: hidden;
      border: 1px solid rgba(0,0,0,0.05);
      box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important;
      position: relative;
  }
  .card.shadow-sm.border-0 {
      position: relative;
  }
  .card.shadow-sm.border-0::before {
      content: "";
      position: absolute;
      top: 0;
      left: -40%;
      width: 40%;
      height: 100%;
      background: linear-gradient(90deg, rgba(255,255,255,0.15), rgba(255,255,255,0));
      transform: skewX(-20deg);
      animation: headerSheen 6s linear infinite;
  }
  @keyframes headerSheen {
      0% { left: -40%; }
      60% { left: 120%; }
      100% { left: 120%; }
  }
  .info-box-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 64px;
      height: 64px;
      border-radius: 12px;
      box-shadow: inset 0 0 0 2px rgba(255,255,255,0.35);
      position: relative;
      overflow: hidden;
  }
  .info-box-icon::after {
      content: "";
      position: absolute;
      top: -20%;
      left: -20%;
      width: 60%;
      height: 60%;
      background: radial-gradient(circle, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0) 70%);
      animation: iconPulse 6s ease-in-out infinite;
  }
  @keyframes iconPulse {
      0% { transform: translate(0,0) scale(1); }
      50% { transform: translate(10px, -6px) scale(1.1); }
      100% { transform: translate(0,0) scale(1); }
  }
  .info-box::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, var(--pup-maroon), var(--pup-gold));
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.4s ease;
  }
  .info-box:hover::after {
      transform: scaleX(1);
  }
  .info-box:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
  }
  .info-box-content .info-box-number {
      font-weight: 800;
      letter-spacing: 0.5px;
  }
  .info-box:hover .info-box-number {
      transform: translateY(-2px);
      transition: transform 0.3s ease;
  }
</style>

<?php if($_settings->userdata('type') == 2): ?>
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-3 animate__animated animate__fadeInUp animate__delay-1s">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Total Students</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `student_list` where `status` = 1")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3 animate__animated animate__fadeInUp animate__delay-1s">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-archive"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Pending Archives</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `archive_list` where `status` = 0")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3 animate__animated animate__fadeInUp animate__delay-2s">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-check-circle"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Adviser Verified</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `archive_list` where `status` = 2")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3 animate__animated animate__fadeInUp animate__delay-2s">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Published Archives</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `archive_list` where `status` = 1")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
</div>
<?php else: ?>
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-3 animate__animated animate__fadeInUp animate__delay-1s">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-th-list"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Program List</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `department_list` where status = 1")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3 animate__animated animate__fadeInUp animate__delay-1s">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Verified Students</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `student_list` where `status` = 1")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Not Verified Students</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `student_list` where `status` = 0")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-spinner"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Waiting for Publish</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `archive_list` where `status` = 2")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-archive"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Published Archives</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `archive_list` where `status` = 1")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-archive"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Not Verified Archives</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `archive_list` where `status` = 0")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
</div>
<?php endif; ?>
