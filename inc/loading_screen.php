<!-- Loading Screen -->
<style>
  /* Fallback inline styles to guarantee proper overlay even if external CSS fails */
  #loading-screen.loading-screen{position:fixed;top:0;left:0;width:100vw;height:100vh;display:flex;justify-content:center;align-items:center;background:linear-gradient(135deg,#7a0c0c 0%,#a01515 50%,#7a0c0c 100%);z-index:9999;opacity:1;}
  #loading-screen .loading-container{color:#fff;text-align:center;max-width:420px;padding:1.5rem;}
  #loading-screen .loading-title{font-size:1.4rem;font-weight:600;margin:0;}
  #loading-screen .loading-animation{margin:1.5rem 0;}
  #loading-screen .spinner{width:46px;height:46px;border:4px solid rgba(255,255,255,0.3);border-top:4px solid #fff;border-radius:50%;margin:0 auto;animation:spin 1s linear infinite;}
  @keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}
  #loading-screen .progress-container{margin:1.5rem 0;}
  #loading-screen .progress-bar{width:100%;height:8px;background-color:rgba(255,255,255,0.2);border-radius:4px;overflow:hidden;margin-bottom:0.75rem;}
  #loading-screen .progress-fill{height:100%;background:linear-gradient(90deg,#fff 0%,#f0f0f0 50%,#fff 100%);border-radius:4px;width:0%;transition:width .3s ease;}
  #loading-screen .progress-text{display:flex;justify-content:space-between;font-size:.95rem;opacity:.95;}
  #loading-screen .loading-steps{margin-top:1rem;text-align:left;}
  #loading-screen .step{display:flex;align-items:center;gap:.75rem;opacity:.6;}
  #loading-screen .step.active{opacity:1}
  #loading-screen .step.completed{opacity:.8}
  #loading-screen .step i{font-size:1rem;color:#fff}
  #loading-screen .step span{font-size:.92rem;color:#fff}
</style>
<div id="loading-screen" class="loading-screen">
  <div class="loading-container">
    <!-- Logo/Branding -->
    <div class="loading-logo">
      <img src="uploads/pup-logo.png" alt="PUP Logo" class="logo-img" onerror="this.style.display='none'">
      <h2 class="loading-title">PUP Biñan Online Repository</h2>
    </div>
    
    <!-- Loading Animation -->
    <div class="loading-animation">
      <div class="spinner"></div>
    </div>
    
    <!-- Progress Bar -->
    <div class="progress-container">
      <div class="progress-bar">
        <div class="progress-fill" id="progress-fill"></div>
      </div>
      <div class="progress-text">
        <span id="progress-percentage">0%</span>
        <span id="loading-message">Initializing...</span>
      </div>
    </div>
    
    <!-- Loading Steps -->
    <div class="loading-steps">
      <div class="step" id="step-1">
        <i class="fas fa-database"></i>
        <span>Loading database...</span>
      </div>
      <div class="step" id="step-2">
        <i class="fas fa-cogs"></i>
        <span>Configuring system...</span>
      </div>
      <div class="step" id="step-3">
        <i class="fas fa-user-shield"></i>
        <span>Authenticating user...</span>
      </div>
      <div class="step" id="step-4">
        <i class="fas fa-check-circle"></i>
        <span>Ready!</span>
      </div>
    </div>
  </div>
</div>