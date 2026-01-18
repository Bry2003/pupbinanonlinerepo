// Loading disabled globally: no-op functions
function start_loader() {}
function end_loader() {}

// Post-signup loader styled like the provided image
function showSignupLoader(message, redirectUrl, delayMs) {
  try {
    var existing = document.getElementById('signup-loader-overlay');
    if (!existing) {
      // Create overlay
      var overlay = document.createElement('div');
      overlay.id = 'signup-loader-overlay';
      overlay.style.position = 'fixed';
      overlay.style.inset = '0';
      overlay.style.background = '#000';
      overlay.style.display = 'flex';
      overlay.style.alignItems = 'center';
      overlay.style.justifyContent = 'center';
      overlay.style.zIndex = '9999';

      // Container
      var container = document.createElement('div');
      container.style.width = '80%';
      container.style.maxWidth = '560px';
      container.style.textAlign = 'center';

      // LOADING text
      var title = document.createElement('div');
      title.id = 'signup-loader-text';
      title.textContent = (message || 'LOADING...').toUpperCase();
      title.style.color = '#fff';
      title.style.fontSize = '36px';
      title.style.fontWeight = '800';
      title.style.letterSpacing = '4px';
      title.style.textShadow = '0 0 6px rgba(255,255,255,0.8), 0 0 12px rgba(255,255,255,0.4)';
      title.style.marginBottom = '20px';

      // Progress bar wrapper
      var barWrap = document.createElement('div');
      barWrap.style.position = 'relative';
      barWrap.style.height = '28px';
      barWrap.style.border = '4px solid #fff';
      barWrap.style.borderRadius = '6px';
      barWrap.style.background = '#111';
      barWrap.style.boxShadow = '0 0 8px rgba(255,255,255,0.2) inset';

      // Progress bar fill
      var barFill = document.createElement('div');
      barFill.id = 'signup-loader-progress';
      barFill.style.height = '100%';
      barFill.style.width = '0%';
      barFill.style.background = '#00e676';
      barFill.style.boxShadow = '0 0 12px rgba(0,230,118,0.9), 0 0 20px rgba(0,230,118,0.6)';
      barFill.style.transition = 'width 200ms ease-out';

      barWrap.appendChild(barFill);
      container.appendChild(title);
      container.appendChild(barWrap);
      overlay.appendChild(container);
      document.body.appendChild(overlay);
    } else {
      // Update message if overlay exists
      var msgNode = document.getElementById('signup-loader-text');
      if (msgNode) msgNode.textContent = (message || msgNode.textContent).toUpperCase();
    }

    // Animate progress to 100%
    var progress = 0;
    var targetDelay = typeof delayMs === 'number' ? delayMs : 1800; // slightly longer to make it visible
    var step = Math.max(10, Math.floor(targetDelay / 20));
    var increment = Math.max(3, Math.floor(100 / (targetDelay / step)));
    var progressEl = document.getElementById('signup-loader-progress');
    var timer = setInterval(function () {
      progress = Math.min(100, progress + increment);
      if (progressEl) progressEl.style.width = progress + '%';
      if (progress >= 100) {
        clearInterval(timer);
        if (redirectUrl) {
          setTimeout(function () { window.location.href = redirectUrl; }, 200);
        }
      }
    }, step);
  } catch (e) {
    // Fallback to direct redirect if overlay fails
    if (redirectUrl) window.location.href = redirectUrl;
  }
}
// function 
window.alert_toast = function($msg = 'TEST', $bg = 'success', $pos = '') {
    if (typeof Swal !== 'undefined') {
        var Toast = Swal.mixin({
            toast: true,
            position: $pos || 'top',
            showConfirmButton: false,
            timer: 3500
        });
        Toast.fire({
            icon: $bg,
            title: $msg
        })
    } else if (typeof toastr !== 'undefined') {
        toastr[$bg]($msg);
    } else {
        // Fallback to a simple custom toast if libraries fail
        var toast = document.createElement('div');
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.backgroundColor = $bg === 'success' ? '#28a745' : ($bg === 'error' || $bg === 'danger' ? '#dc3545' : '#17a2b8');
        toast.style.color = '#fff';
        toast.style.padding = '10px 20px';
        toast.style.borderRadius = '4px';
        toast.style.zIndex = '9999';
        toast.innerText = $msg;
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.remove();
        }, 3000);
    }
}

$(document).ready(function() {
    // Login
    $('#login-frm').submit(function(e) {
        e.preventDefault()
        start_loader()
        if ($('.err_msg').length > 0)
            $('.err_msg').remove()
        $.ajax({
            url: _base_url_ + 'classes/Login.php?f=login',
            method: 'POST',
            data: $(this).serialize(),
            error: err => {
                console.log(err)

            },
            success: function(resp) {
                if (resp) {
                    resp = JSON.parse(resp)
                    if (resp.status == 'success') {
                        location.replace(_base_url_ + 'admin');
                    } else if (resp.status == 'incorrect') {
                        var _frm = $('#login-frm')
                        var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Incorrect username or password</div>"
                        _frm.prepend(_msg)
                        _frm.find('input').addClass('is-invalid')
                        $('[name="username"]').focus()
                    } else if (resp.status == 'notverified') {
                        var _frm = $('#login-frm')
                        var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Your Account is not yet verified.</div>"
                        _frm.prepend(_msg)
                        _frm.find('input').addClass('is-invalid')
                        $('[name="username"]').focus()
                    }
                    end_loader()
                }
            }
        })
    })
    $('#clogin-frm').submit(function(e) {
        e.preventDefault()
        start_loader()
        if ($('.err_msg').length > 0)
            $('.err_msg').remove()
        $.ajax({
            url: _base_url_ + 'classes/Login.php?f=clogin',
            method: 'POST',
            data: $(this).serialize(),
            error: err => {
                console.log(err)
                alert_toast("An error occured", 'danger')
                end_loader()
            },
            success: function(resp) {
                if (resp) {
                    resp = JSON.parse(resp)
                    if (resp.status == 'success') {
                        location.replace(_base_url_);
                    } else if (resp.status == 'incorrect') {
                        var _frm = $('#clogin-frm')
                        var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Incorrect code or password</div>"
                        _frm.prepend(_msg)
                        _frm.find('input').addClass('is-invalid')
                        $('[name="username"]').focus()
                    }
                }
                end_loader()
            }
        })
    })

    //user login
    $('#slogin-frm').submit(function(e) {
            e.preventDefault()
            start_loader()
            if ($('.err_msg').length > 0)
                $('.err_msg').remove()
            $.ajax({
                url: _base_url_ + 'classes/Login.php?f=slogin',
                method: 'POST',
                data: $(this).serialize(),
                error: err => {
                    console.log(err)

                },
                success: function(resp) {
                    if (resp) {
                        resp = JSON.parse(resp)
                        if (resp.status == 'success') {
                            location.replace(_base_url_ + 'student');
                        } else if (resp.status == 'incorrect') {
                            var _frm = $('#slogin-frm')
                            var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Incorrect username or password</div>"
                            _frm.prepend(_msg)
                            _frm.find('input').addClass('is-invalid')
                            $('[name="username"]').focus()
                        }
                        end_loader()
                    }
                }
            })
        })
        // System Info
    $('#system-frm').submit(function(e) {
        e.preventDefault()
        start_loader()
        if ($('.err_msg').length > 0)
            $('.err_msg').remove()
        $.ajax({
            url: _base_url_ + 'classes/SystemSettings.php?f=update_settings',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                $('#msg').html('<div class="alert alert-danger err_msg">An Error occurred: ' + error + '</div>')
                end_loader()
            },
            success: function(resp) {
                console.log("Response:", resp);
                try {
                    // Handle boolean/number/JSON/string responses robustly
                    var ok = false;
                    if (resp === 1 || resp === '1' || resp === true) ok = true;
                    if (!ok) {
                        // Try JSON {status:"success"}
                        var j;
                        try { j = JSON.parse(resp); } catch (_) {}
                        if (j && (j.status === 'success' || j.ok === true)) ok = true;
                    }
                    if (ok) {
                        alert_toast('System settings updated', 'success');
                        location.reload();
                        return;
                    }
                    // Not OK: surface server-provided message if any
                    var msg = 'An Error occurred';
                    if (typeof resp === 'string' && resp.trim().length > 0) {
                        msg = resp;
                    } else if (j && j.message) {
                        msg = j.message;
                    }
                    $('#msg').html('<div class="alert alert-danger err_msg">' + msg + '</div>');
                } finally {
                    end_loader();
                }
            }
        })
    })
})