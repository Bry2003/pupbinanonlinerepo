<?php require_once('./config.php'); ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f4f6f9;
    margin: 0;
    padding: 20px;
  }
  .demo-container {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
  }
  .demo-title {
    color: #800000;
    text-align: center;
    margin-bottom: 30px;
  }
  .demo-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 30px;
  }
  .demo-btn {
    background: #800000;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
  }
  .demo-btn:hover {
    background: #600000;
  }
  .demo-info {
    background: #e8f4fd;
    border: 1px solid #bee5eb;
    border-radius: 5px;
    padding: 20px;
    margin-bottom: 20px;
  }
  .demo-info h4 {
    color: #0c5460;
    margin-top: 0;
  }
</style>
<?php require_once('inc/header.php') ?>
<body class="layout-top-nav layout-fixed layout-navbar-fixed" style="height: auto;">
    <?php require_once('inc/loading_screen.php') ?>
    
    <div class="demo-container">
        <h1 class="demo-title">Loading Screen Demo</h1>
        
        <div class="demo-info">
            <h4>Loading Screen Features:</h4>
            <ul>
                <li>Animated progress bar with percentage display</li>
                <li>Step-by-step loading indicators</li>
                <li>Smooth animations and transitions</li>
                <li>PUP branding with logo and colors</li>
                <li>Responsive design for all devices</li>
                <li>Auto-hide when loading completes</li>
            </ul>
        </div>
        
        <div class="demo-buttons">
            <button class="demo-btn" onclick="showLoadingScreen()">Show Loading Screen</button>
            <button class="demo-btn" onclick="resetLoadingScreen()">Reset Loading Screen</button>
            <button class="demo-btn" onclick="skipToComplete()">Skip to Complete</button>
            <button class="demo-btn" onclick="customSteps()">Custom Steps Demo</button>
        </div>
        
        <div class="demo-info">
            <h4>Integration Instructions:</h4>
            <p>The loading screen has been integrated into the following pages:</p>
            <ul>
                <li><strong>Main Site:</strong> index.php, login.php</li>
                <li><strong>Admin Panel:</strong> admin/index.php, admin/login.php</li>
            </ul>
            <p>The loading screen automatically starts when these pages load and completes after the loading sequence.</p>
        </div>
        
        <div class="demo-info">
            <h4>JavaScript API:</h4>
            <ul>
                <li><code>loadingScreen.init()</code> - Initialize the loading screen</li>
                <li><code>loadingScreen.show()</code> - Show the loading screen</li>
                <li><code>loadingScreen.hide()</code> - Hide the loading screen</li>
                <li><code>loadingScreen.reset()</code> - Reset to initial state</li>
                <li><code>loadingScreen.skipToComplete()</code> - Skip to completion</li>
                <li><code>loadingScreen.setSteps(newSteps)</code> - Set custom loading steps</li>
            </ul>
        </div>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function showLoadingScreen() {
            if (window.loadingScreen) {
                loadingScreen.reset();
                loadingScreen.show();
                loadingScreen.init();
            }
        }
        
        function resetLoadingScreen() {
            if (window.loadingScreen) {
                loadingScreen.reset();
                loadingScreen.show();
            }
        }
        
        function skipToComplete() {
            if (window.loadingScreen) {
                loadingScreen.skipToComplete();
            }
        }
        
        function customSteps() {
            if (window.loadingScreen) {
                const customSteps = [
                    { id: 'step-1', message: 'Connecting to server...', duration: 1000 },
                    { id: 'step-2', message: 'Loading user data...', duration: 800 },
                    { id: 'step-3', message: 'Preparing dashboard...', duration: 600 },
                    { id: 'step-4', message: 'Almost ready!', duration: 400 }
                ];
                loadingScreen.setSteps(customSteps);
                loadingScreen.show();
                loadingScreen.init();
            }
        }
        
        // Auto-hide loading screen on page load for demo
        $(document).ready(function() {
            setTimeout(function() {
                if (window.loadingScreen) {
                    loadingScreen.hide();
                }
            }, 100);
        });
    </script>
</body>
</html>