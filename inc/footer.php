<script>
  // Simple Scroll Reveal Script
  document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px"
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate__animated', 'animate__fadeInUp');
          entry.target.style.opacity = 1;
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Select elements to animate
    const animateElements = document.querySelectorAll('.card, .info-box, .callout, .list-group-item, h2, h3, .project-card');
    animateElements.forEach(el => {
      // Don't override existing animations
      if (!el.classList.contains('animate__animated')) {
        el.style.opacity = 0; // Hide initially
        el.classList.add('animate__fast'); // Faster animation
        observer.observe(el);
      }
    });
  });

  $(document).ready(function(){
    if($('.list-group').length > 0){
      $('.list-group').each(function(){
        if(String($(this).text()).trim() == ""){
          $(this).html("")
        }
      })
    }
    
     window.viewer_modal = function($src = ''){
      start_loader()
      var t = $src.split('.')
      t = t[1]
      if(t =='mp4'){
        var view = $("<video src='"+$src+"' controls autoplay></video>")
      }else{
        var view = $("<img src='"+$src+"' />")
      }
      $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
      $('#viewer_modal .modal-content').append(view)
      $('#viewer_modal').modal({
              show:true,
              backdrop:'static',
              keyboard:false,
              focus:true
            })
            end_loader()  

  }
    window.uni_modal = function($title = '' , $url='',$size=""){
        start_loader()
        $.ajax({
            url:$url,
            error:err=>{
                console.log()
                alert("An error occured")
            },
            success:function(resp){
                if(resp){
                    $('#uni_modal .modal-title').html($title)
                    $('#uni_modal .modal-body').html(resp)
                    if($size != ''){
                        $('#uni_modal .modal-dialog').addClass($size+'  modal-dialog-centered')
                    }else{
                        $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
                    }
                    $('#uni_modal').modal({
                      show:true,
                      backdrop:'static',
                      keyboard:false,
                      focus:true
                    })
                    end_loader()
                }
            }
        })
    }
    window._conf = function($msg='',$func='',$params = []){
       $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
       $('#confirm_modal .modal-body').html($msg)
       $('#confirm_modal').modal('show')
    }
  })
</script>
<footer class="main-footer text-sm bg-maroon text-white pt-5 pb-3">
  <div class="container">
    <div class="row">
      <!-- Brand Section -->
      <div class="col-12 text-center mb-4">
        <div class="d-flex align-items-center justify-content-center mb-3">
          <img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="PUP Logo" style="width: 70px; height: 70px; margin-right: 20px;">
          <div class="text-left">
            <h6 class="text-uppercase mb-0 text-white" style="font-size: 1.1rem; letter-spacing: 1.5px; font-weight: 600;">Polytechnic University of the Philippines</h6>
            <h4 class="font-weight-bold mb-0 text-white" style="font-size: 2.2rem; letter-spacing: 2px;">Biñan Campus</h4>
          </div>
        </div>
        <p class="font-italic mb-4 text-white" style="font-size: 1.25rem; opacity: 1; letter-spacing: 0.5px;"><?php echo $_settings->info('motto') ?: 'Serving the Nation Through Quality Public Education' ?></p>
        
        <div class="contact-info text-white" style="font-size: 1.1rem;">
          <p class="mb-1 d-inline-block mx-4"><i class="fas fa-map-marker-alt mr-2 text-warning"></i> <?php echo $_settings->info('school_address') ?: 'Brgy. Zapote, Biñan City, Laguna 4024' ?></p>
          <p class="mb-1 d-inline-block mx-4"><i class="fas fa-phone mr-2 text-warning"></i> <?php echo $_settings->info('contact_number') ?: '(049) 511-9577' ?></p>
          <p class="mb-0 d-inline-block mx-4"><i class="fas fa-envelope mr-2 text-warning"></i> <?php echo $_settings->info('email_address') ?: 'binan@pup.edu.ph' ?></p>
        </div>
      </div>
    </div>

    <div class="row mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.2) !important;">
      <div class="col-12 text-center">
        <p class="mb-0 text-white" style="opacity: 0.9; font-size: 1rem;">&copy; <?php echo date('Y') ?> PUP Biñan Campus. All rights reserved.</p>
      </div>
    </div>
  </div>
</footer>
    </div>
    <!-- ./wrapper -->
<div id="libraries">
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="<?php echo base_url ?>plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo base_url ?>plugins/sparklines/sparkline.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- JQVMap -->
    <script src="<?php echo base_url ?>plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="<?php echo base_url ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo base_url ?>plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?php echo base_url ?>plugins/moment/moment.min.js"></script>
    <script src="<?php echo base_url ?>plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="<?php echo base_url ?>plugins/summernote/summernote-bs4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- overlayScrollbars -->
    <!-- <script src="<?php echo base_url ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script> -->
    <!-- AdminLTE App -->
    <script src="<?php echo base_url ?>dist/js/adminlte.js"></script>
  </div>   
    <div class="daterangepicker ltr show-ranges opensright">
      <div class="ranges">
        <ul>
          <li data-range-key="Today">Today</li>
          <li data-range-key="Yesterday">Yesterday</li>
          <li data-range-key="Last 7 Days">Last 7 Days</li>
          <li data-range-key="Last 30 Days">Last 30 Days</li>
          <li data-range-key="This Month">This Month</li>
          <li data-range-key="Last Month">Last Month</li>
          <li data-range-key="Custom Range">Custom Range</li>
        </ul>
      </div>
      <div class="drp-calendar left">
        <div class="calendar-table"></div>
        <div class="calendar-time" style="display: none;"></div>
      </div>
      <div class="drp-calendar right">
        <div class="calendar-table"></div>
        <div class="calendar-time" style="display: none;"></div>
      </div>
      <div class="drp-buttons"><span class="drp-selected"></span><button class="cancelBtn btn btn-sm btn-default" type="button">Cancel</button><button class="applyBtn btn btn-sm btn-primary" disabled="disabled" type="button">Apply</button> </div>
    </div>
    <div class="jqvmap-label" style="display: none; left: 1093.83px; top: 394.361px;">Idaho</div>
<script>
  $(function(){
    $('.wrapper>.content-wrapper').css("min-height",$(window).height() - $('#top-Nav').height() - $('#login-nav').height() - $("footer.main-footer").height())
  })
</script>