<?php require_once('./config.php'); ?>
 <!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<style>
  /* Global Page Background - PUP Themed */
  body {
    background-color: #f4f6f9;
    background-image: 
        radial-gradient(circle at 0% 0%, rgba(128, 0, 0, 0.08) 15%, transparent 16%), 
        radial-gradient(circle at 100% 0%, rgba(255, 215, 0, 0.08) 15%, transparent 16%), 
        radial-gradient(circle at 100% 100%, rgba(128, 0, 0, 0.08) 15%, transparent 16%), 
        radial-gradient(circle at 0% 100%, rgba(255, 215, 0, 0.08) 15%, transparent 16%);
    background-attachment: fixed;
  }
  
  /* Side Decoration Elements */
  body::before, body::after {
      content: '';
      position: fixed;
      top: 0;
      bottom: 0;
      width: 40px;
      z-index: 1030;
      background: linear-gradient(180deg, var(--pup-maroon) 0%, #4a0000 100%);
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      display: none; /* Hidden on mobile */
  }
  @media (min-width: 1400px) {
      body::before { left: 0; display: block; }
      body::after { right: 0; display: block; }
      body { padding: 0 40px; } /* Add padding so content doesn't hide behind bars */
  }

  #header{
    height:85vh;
    width:100%;
    position:relative;
    margin-top:4.5rem;
    overflow: hidden;
    border-radius: 0 0 50% 50% / 4%; /* Subtle curve at bottom */
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  }
  <?php $__h_cover = validate_image($_settings->info('cover')); $__h_is_video = preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $__h_cover); ?>
  #header:before{
    content:"";
    position:absolute;
    height:100%;
    width:100%;
    background-image:url(<?= $__h_is_video ? base_url.'dist/img/no-image-available.png' : $__h_cover ?>);
    background-size:cover;
    background-repeat:no-repeat;
    background-position: center center;
  }
  #header .header-bg-video{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%;
    object-fit:cover;
    z-index:1;
  }
  /* Dark Overlay for better text contrast */
  #header .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); 
    z-index: 2;
  }
  #header>div.content-holder{
    position:absolute;
    height:100%;
    width:100%;
    z-index:3;
  }

  /* Typography & Animations */
  .site-title {
    font-size: 4rem;
    font-weight: 800;
    color: var(--pup-gold);
    text-transform: uppercase;
    letter-spacing: 2px;
    text-shadow: 2px 2px 0 #3b3b3b, 4px 4px 0 #000;
    margin-bottom: 2.5rem;
    display: inline-block; /* Crucial for centering properly with text-align: center on parent */
    position: relative;
  }
  
  /* The typing container */
  .typing-text {
    overflow: hidden;
    white-space: nowrap;
    border-right: .15em solid var(--pup-maroon, #800000);
    /* font-family: 'Courier New', Courier, monospace; Removed to allow Clarendon BT inheritance */
    animation: 
      typing 4s steps(<?= strlen($_settings->info('name')) ?>, end) forwards,
      blink-caret .75s step-end infinite;
    display: inline-block;
    width: 0;
    vertical-align: bottom;
  }
  
  /* The typing effect */
  @keyframes typing {
    from { width: 0 }
    to { width: calc(<?= strlen($_settings->info('name')) ?> * 1ch + <?= strlen($_settings->info('name')) ?> * 2px); }
  }
  
  /* The typewriter cursor effect */
  @keyframes blink-caret {
    from, to { border-color: transparent }
    50% { border-color: var(--pup-maroon, #800000); }
  }

  /* Parallax Effect */
  #header:before {
      background-attachment: fixed;
  }

  #enrollment {
      font-size: 1.4rem;
      padding: 15px 50px;
      border-radius: 50px;
      transition: all 0.4s ease;
      background: linear-gradient(135deg, var(--pup-maroon, #800000), #b90000);
      border: 2px solid rgba(255,255,255,0.2);
      color: #fff;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      text-transform: uppercase;
      letter-spacing: 1px;
  }
  #enrollment:hover {
      transform: translateY(-5px) scale(1.05);
      box-shadow: 0 10px 25px rgba(128, 0, 0, 0.5);
      background: linear-gradient(135deg, #b90000, var(--pup-maroon, #800000));
      color: #fff;
  }

  #top-Nav a.nav-link.active {
      color: var(--pup-accent);
      font-weight: 900;
      position: relative;
  }
  #top-Nav a.nav-link.active:before {
    content: "";
    position: absolute;
    border-bottom: 2px solid var(--pup-accent);
    width: 33.33%;
    left: 33.33%;
    bottom: 0;
  }
  
  /* Feature Cards */
  .feature-card {
      background: #fff;
      border-radius: 15px;
      transition: all 0.3s ease;
      height: 100%;
      border: 1px solid rgba(0,0,0,0.05);
  }
  .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
      border-color: transparent;
  }
  .icon-wrapper {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 90px;
      height: 90px;
      border-radius: 50%;
      background: #fff;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
      margin-bottom: 1.5rem;
  }
  .feature-card:hover .icon-wrapper {
      background: var(--pup-maroon, #800000);
      transform: scale(1.1) rotate(5deg);
  }
  .feature-card:hover .icon-wrapper i {
      color: #fff !important;
      transition: color 0.3s ease;
  }
</style>
<?php require_once('inc/header.php') ?>
  <body class="layout-top-nav layout-fixed layout-navbar-fixed" style="height: auto;">
    <div class="wrapper">
     <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home';  ?>
     <?php require_once('inc/topBarNav.php') ?>
     <?php if($_settings->chk_flashdata('success')): ?>
      <script>
        $(document).ready(function(){
          alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
        });
      </script>
      <?php endif;?>    
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper pt-5" style="">
        <?php if($page == "about_us"): ?>
          <?php $__cover_url = validate_image($_settings->info('cover')); $__is_video = preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $__cover_url); ?>
          <div id="header" class="shadow mb-4">
              <?php if($__is_video): ?>
                <video class="header-bg-video" autoplay muted loop playsinline>
                  <source src="<?= $__cover_url ?>" type="video/mp4">
                </video>
              <?php endif; ?>
              <div class="overlay"></div>
              <div class="d-flex justify-content-center h-100 w-100 align-items-center flex-column px-3 content-holder">
                  <h1 class="site-title"><span class="typing-text"><?php echo $_settings->info('name') ?></span></h1>
                  <p class="lead text-light text-center w-75 mb-4 animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 1.5rem; text-shadow: 1px 1px 3px rgba(0,0,0,0.8); font-weight: 300;">Archiving Excellence. Empowering Research. Preserving the Future.</p>
                  <a href="./?page=projects" class="btn btn-lg rounded-pill animate__animated animate__fadeInUp animate__delay-2s" id="enrollment" style="width: auto; padding-left: 3rem; padding-right: 3rem;"><b>Explore Projects</b></a>
              </div>
          </div>

          <!-- Features Section -->
          <div class="container py-5 bg-white shadow-lg animate__animated animate__fadeInUp" style="position: relative; z-index: 10; margin-top: -80px; border-radius: 15px; margin-bottom: 3rem;">
              <div class="row text-center">
                  <div class="col-md-4 mb-4 mb-md-0">
                      <div class="p-3 feature-card">
                          <div class="icon-wrapper mb-3">
                            <i class="fas fa-search fa-3x" style="color: var(--pup-maroon);"></i>
                          </div>
                          <h4 style="font-weight: 700; color: #444; margin-bottom: 1rem;">Search & Discover</h4>
                          <p class="text-muted">Easily access a vast repository of theses, capstone projects, and research papers from PUP Biñan.</p>
                      </div>
                  </div>
                  <div class="col-md-4 mb-4 mb-md-0">
                      <div class="p-3 feature-card">
                          <div class="icon-wrapper mb-3">
                            <i class="fas fa-shield-alt fa-3x" style="color: var(--pup-gold);"></i>
                          </div>
                          <h4 style="font-weight: 700; color: #444; margin-bottom: 1rem;">Secure Archive</h4>
                          <p class="text-muted">A dedicated platform ensuring the preservation and integrity of academic works for future generations.</p>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="p-3 feature-card">
                          <div class="icon-wrapper mb-3">
                            <i class="fas fa-graduation-cap fa-3x" style="color: var(--pup-maroon);"></i>
                          </div>
                          <h4 style="font-weight: 700; color: #444; margin-bottom: 1rem;">Academic Excellence</h4>
                          <p class="text-muted">Showcasing the intellectual output and innovative research of the PUP Biñan community.</p>
                      </div>
                  </div>
              </div>
          </div>
        <?php endif; ?>
        <!-- Main content -->
        <section class="content">
            <?php 
              if($page == "home"){
                  include 'home.php';
              }else{
            ?>
            <div class="container">
                <?php 
                  if(!file_exists($page.".php") && !is_dir($page)){
                      include '404.html';
                  }else{
                    if(is_dir($page))
                      include $page.'/index.php';
                    else
                      include $page.'.php';
                  }
                ?>
            </div>
            <?php } ?>
        </section>
        <!-- /.content -->
  <div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-arrow-right"></span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
      </div>
      <!-- /.content-wrapper -->
      <?php require_once('inc/footer.php') ?>
  </body>
</html>
