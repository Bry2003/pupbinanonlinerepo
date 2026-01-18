<style>
  .user-img{
        position: absolute;
        height: 27px;
        width: 27px;
        object-fit: cover;
        left: -7%;
        top: -12%;
  }
  .btn-rounded{
        border-radius: 50px;
  }
</style>
<!-- Navbar -->
      <style>
        /* Advanced Header Animations & Design */
        :root{ --brand-logo-size: 150px; }
        #top-Nav{
          top: 0;
          position: fixed;
          width: 100%;
          z-index: 1036;
          background: linear-gradient(180deg, var(--pup-maroon) 0%, #5a0000 100%) !important;
          backdrop-filter: blur(15px);
          -webkit-backdrop-filter: blur(15px);
          box-shadow: 0 8px 32px rgba(0,0,0,0.25);
          transition: background 0.3s ease;
          padding: 0.5rem 0 !important;
          height: 200px;
          position: relative;
        }
        #top-Nav::after{content:"";position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg, rgba(255,215,0,0.1), var(--pup-gold), rgba(255,215,0,0.1))}
        .text-sm .layout-navbar-fixed .wrapper .main-header ~ .content-wrapper, .layout-navbar-fixed .wrapper .main-header.text-sm ~ .content-wrapper {margin-top: calc(12.5rem) !important;padding-top: calc(1rem) !important;}
        
        /* Nav Link Animations */
        #top-Nav .nav-link {
            position: relative;
            color: #fff !important;
            font-family: 'Clarendon BT','Clarendon','Georgia','Times New Roman',serif;
            font-weight: 900;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 1px 0 rgba(0,0,0,0.35), 0 2px 4px rgba(0,0,0,0.35);
            padding: 0.45rem 1.1rem !important;
            transition: color 0.3s;
        }
        #top-Nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: 0;
            left: 50%;
            background-color: var(--pup-gold);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            transform: translateX(-50%);
            border-radius: 2px;
        }
        #top-Nav .nav-link:hover::after,
        #top-Nav .nav-link.active::after {
            width: 70%;
        }
        #top-Nav .nav-link:hover {
            color: var(--pup-gold) !important;
        }

        /* Brand Logo Spin */
        .brand-image {
            transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            height: var(--brand-logo-size) !important;
            width: var(--brand-logo-size) !important;
            margin-right: 10px;
            border: 2px solid rgba(128,0,0,0.1);
        }
        .navbar-brand { display: flex; align-items: center; }
        .brand-text-group{display:flex;flex-direction:column;line-height:1.1}
        .brand-text{font-size:1.9rem;font-weight:800;color:#fff;letter-spacing:1px;text-transform:uppercase;font-family:'Clarendon BT','Clarendon','Georgia','Times New Roman',serif}
        .brand-subtext{font-size:1.3rem;font-weight:800;color:var(--pup-accent);font-style:italic}
        .navbar-brand:hover .brand-image {
            transform: rotate(360deg) scale(1.1);
        }

        /* Search styles removed */
        @media (min-width:768px){
          #top-Nav .container, #top-Nav .container-fluid{display:grid;grid-template-columns:auto 1fr;grid-template-rows:auto auto;align-items:center;gap:.25rem .75rem;padding-left:0;padding-right:0;max-width:100%}
          .navbar-brand{grid-column:1;grid-row:1;justify-self:start}
          #navbarCollapse{grid-column:1 / 3;grid-row:2;align-self:end}
          #navbarCollapse .navbar-nav{justify-content:flex-start;padding-top:.2rem;padding-bottom:.25rem;margin-bottom:0;padding-left:calc(var(--brand-logo-size))}
        }
          #top-Nav .container > .navbar-nav.navbar-no-expand, #top-Nav .container-fluid > .navbar-nav.navbar-no-expand{grid-column:2;grid-row:1;justify-self:end}
        }
        
        /* Advanced Login Button Styles for White Navbar */
        .nav-btn-animated {
          position: relative;
          display: inline-flex !important;
          align-items: center;
          padding: 8px 18px;
          border-radius: 30px;
          background: #5a0000;
          overflow: hidden;
          transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
          border: 2px solid var(--pup-gold);
          text-decoration: none !important;
          margin-left: 10px;
          height: 42px;
          box-shadow: 0 0 0 2px rgba(255,215,0,0.25);
          color: #fff !important;
        }

        .nav-btn-animated:hover {
          background: var(--pup-gold);
          transform: translateY(-2px);
          box-shadow: 0 0 0 3px rgba(255,215,0,0.35);
          border-color: var(--pup-gold);
        }

        .nav-btn-animated .icon-wrapper {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 24px;
          height: 24px;
          margin-right: 10px;
          transition: transform 0.4s ease;
          color: #fff;
          background: transparent;
          line-height: 1;
        }
        .nav-btn-animated .icon-wrapper i{font-size:16px;line-height:1}

        .nav-btn-animated:hover .icon-wrapper {
          transform: rotate(15deg) scale(1.2);
          color: var(--pup-gold);
        }

        .nav-btn-animated .text-wrapper {
          font-weight: 600;
          font-size: 0.9rem;
          text-transform: uppercase;
          letter-spacing: 0.5px;
          transition: color 0.3s ease;
          color: #fff;
        }

        .nav-btn-animated:hover .text-wrapper {
          color: var(--pup-maroon);
        }
        
        /* Ensure spacing between right-side buttons */
        #top-Nav .navbar-no-expand > .d-none.d-md-flex{gap:12px}

        /* Shine Effect */
        .nav-btn-animated::before {
          content: '';
          position: absolute;
          top: 0;
          left: -100%;
          width: 100%;
          height: 100%;
          background: linear-gradient(
            120deg,
            transparent,
            rgba(255, 255, 255, 0.3),
            transparent
          );
          transition: all 0.6s;
        }

        .nav-btn-animated:hover::before {
          left: 100%;
        }

        /* Welcome Pill Design */
        .welcome-pill {
          background: rgba(255, 255, 255, 0.05);
          border-radius: 50px;
          padding: 4px 15px 4px 4px;
          border: 1px solid rgba(243, 178, 51, 0.3);
          transition: all 0.3s ease;
          display: inline-flex;
          align-items: center;
          margin-right: 15px;
        }
        .welcome-pill:hover {
          background: rgba(255, 255, 255, 0.1);
          border-color: var(--pup-gold);
          box-shadow: 0 0 10px rgba(243, 178, 51, 0.2);
        }
        .user-avatar-header {
          width: 38px !important;
          height: 38px !important;
          object-fit: cover;
          border-radius: 50%;
          border: 2px solid var(--pup-gold) !important;
          margin-right: 12px !important;
          box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .welcome-text {
          font-family: 'Clarendon BT','Clarendon','Georgia','Times New Roman',serif;
          font-weight: 700 !important;
          color: #fff !important;
          font-size: 1.05rem !important;
          margin-right: 12px !important;
          letter-spacing: 0.5px;
          text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
          line-height: 1.1;
        }
        .logout-btn {
          color: rgba(255,255,255,0.8) !important;
          font-size: 1.2rem;
          transition: all 0.2s;
          display: flex;
          align-items: center;
          justify-content: center;
          width: 30px;
          height: 30px;
          border-radius: 50%;
        }
        .logout-btn:hover {
          color: var(--pup-gold) !important;
          background: rgba(255,255,255,0.1);
          transform: scale(1.1);
        }
      </style>
      <!-- Contact chip fixed at bottom-left -->
      <div class="contact-chip animate__animated animate__fadeInLeft">
        <a href="tel:<?= preg_replace('/\D+/', '', $_settings->info('contact')) ?>" class="chip-link">
          <i class="fa fa-phone"></i>
          <span><?= $_settings->info('contact') ?></span>
        </a>
      </div>
      <nav class="main-header navbar navbar-expand navbar-light border-0 navbar-light text-sm animate__animated animate__fadeInDown" style="animation-delay: 0.1s;" id='top-Nav'>
        
        <div class="container-fluid px-0">
          <a href="./" class="navbar-brand">
            <img src="<?php echo validate_image($_settings->info('logo'))?>" alt="Site Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <div class="brand-text-group">
              <div class="brand-text"><?= strtoupper($_settings->info('name')) ?></div>
              <div class="brand-subtext"><?= $_settings->info('campus_name') ?: 'Biñan Campus' ?></div>
            </div>
          </a>
          <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <!-- Mobile-only consolidated menu button moved to right side -->

          <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav d-none d-md-flex">
              <li class="nav-item">
                <a href="./" class="nav-link <?= isset($page) && $page =='home' ? "active" : "" ?>">Home</a>
              </li>
              
              <!-- Projects Dropdown -->
              <li class="nav-item dropdown">
                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle  <?= isset($page) && ($page =='projects' || $page =='projects_per_department') ? "active" : "" ?>">Projects</a>
                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow" style="left: 0px; right: inherit;">
                  <li><a href="./?page=projects" class="dropdown-item">All Projects</a></li>
                  <li class="dropdown-divider"></li>
                  <li class="dropdown-header">By Department</li>
                  <?php 
                    $departments = $conn->query("SELECT * FROM department_list where status = 1 order by `name` asc");
                    if($departments && $departments->num_rows){
                      while($row = $departments->fetch_assoc()):
                  ?>
                  <li>
                    <a href="./?page=projects_per_department&id=<?= $row['id'] ?>" class="dropdown-item"><?= ucwords($row['name']) ?></a>
                  </li>
                  <?php 
                      endwhile; 
                    } else { 
                  ?>
                  <li><span class="dropdown-item text-muted">No departments</span></li>
                  <?php } ?>
                </ul>
              </li>

              <!-- Programs link removed -->
              <?php if($_settings->userdata('id') > 0): ?>
              <li class="nav-item">
                <a href="./?page=profile" class="nav-link <?= isset($page) && $page =='profile' ? "active" : "" ?>">Profile</a>
              </li>
              <li class="nav-item">
                <a href="./?page=submit-archive" class="nav-link <?= isset($page) && $page =='submit-archive' ? "active" : "" ?>">Submit Thesis/Capstone</a>
              </li>
              <?php endif; ?>
            </ul>

            
          </div>
          <!-- Right navbar links -->
          <div class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto align-items-center">
            <?php if($_settings->userdata('id') > 0): ?>
              <span class="d-none d-md-inline-flex align-items-center mx-2 welcome-pill">
                <img src="<?= validate_image($_settings->userdata('avatar')) ?>" alt="User Avatar" class="user-avatar-header elevation-2">
                <span class="welcome-text">Welcome, <?= !empty($_settings->userdata('fullname')) ? ucwords($_settings->userdata('fullname')) : (trim(($_settings->userdata('firstname') ?? '').' '.($_settings->userdata('lastname') ?? '')) ? ucwords(trim(($_settings->userdata('firstname') ?? '').' '.($_settings->userdata('lastname') ?? ''))) : (!empty($_settings->userdata('username')) ? $_settings->userdata('username') : $_settings->userdata('email'))) ?></span>
                <?php $logout_f = ($_settings->userdata('login_type') == 1) ? 'logout' : 'student_logout'; ?>
                <a href="<?= base_url.'classes/Login.php?f='.$logout_f ?>" class="logout-btn hover-scale" title="Logout"><i class="fa fa-power-off"></i></a>
              </span>
            <?php else: ?>
              <div class="d-none d-md-flex align-items-center mr-2">
                <a href="./login.php" class="nav-btn-animated">
                  <span class="icon-wrapper"><i class="fas fa-sign-in-alt"></i></span>
                  <span class="text-wrapper">User Login</span>
                </a>
                <a href="./admin" class="nav-btn-animated">
                  <span class="icon-wrapper"><i class="fas fa-user-shield"></i></span>
                  <span class="text-wrapper">Admin Login</span>
                </a>
              </div>
            <?php endif; ?>
                <button class="btn btn-sm btn-maroon btn-mobile-menu d-md-none mr-0" id="mobileDrawerToggle" type="button">Menu</button>
          </div>
        </div>
      </nav>
      <!-- /.navbar -->
<!-- Mobile slide-out sidebar -->
<div id="mobile-drawer" class="mobile-drawer d-md-none">
  <div class="drawer-header">
    <span class="drawer-title">Browse</span>
    <button id="drawerClose" class="btn btn-sm btn-light">Close</button>
  </div>
  <a href="./?page=projects" class="drawer-link">Projects</a>
  <a href="#" class="drawer-link drawer-toggle">Program</a>
  <div class="submenu">
    <?php 
      $departments = $conn->query("SELECT * FROM department_list where status = 1 order by `name` asc");
      if($departments && $departments->num_rows){
        while($row = $departments->fetch_assoc()):
    ?>
      <a class="drawer-link" href="./?page=projects_per_department&id=<?= $row['id'] ?>"><?= ucwords($row['name']) ?></a>
    <?php 
        endwhile;
      } else { 
    ?>
      <span class="drawer-link text-muted">No departments</span>
    <?php } ?>
  </div>
  <?php if($_settings->userdata('id') > 0): ?>
    <a href="./?page=profile" class="drawer-link">Profile</a>
    <a href="./?page=submit-archive" class="drawer-link">Submit Thesis/Capstone</a>
    <?php $logout_f = ($_settings->userdata('login_type') == 1) ? 'logout' : 'student_logout'; ?>
    <a href="<?= base_url.'classes/Login.php?f='.$logout_f ?>" class="drawer-link">Logout</a>
  <?php endif; ?>
</div>
<div id="mobile-drawer-overlay" class="mobile-drawer-overlay d-md-none"></div>
      <script>
        $(function(){

          // Mobile drawer toggles
          $('#mobileDrawerToggle').on('click', function(){
            $('#mobile-drawer').addClass('open');
            $('#mobile-drawer-overlay').addClass('show');
          });
          $('#drawerClose, #mobile-drawer-overlay').on('click', function(){
            $('#mobile-drawer').removeClass('open');
            $('#mobile-drawer-overlay').removeClass('show');
          });
          $('.drawer-toggle').on('click', function(e){
            e.preventDefault();
            $(this).next('.submenu').toggleClass('show');
          });
          
        })
      </script>
