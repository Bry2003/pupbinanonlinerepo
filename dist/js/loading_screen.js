/**
 * Loading Screen Controller
 * Manages the loading screen progress and animations
 */
class LoadingScreen {
    constructor() {
        this.currentStep = 0;
        this.progress = 0;
        this.steps = [
            { id: 'step-1', message: 'Loading database...', duration: 800 },
            { id: 'step-2', message: 'Configuring system...', duration: 600 },
            { id: 'step-3', message: 'Authenticating user...', duration: 700 },
            { id: 'step-4', message: 'Ready!', duration: 500 }
        ];
        this.isComplete = false;
    }

    /**
     * Initialize the loading screen
     */
    init() {
        this.progressFill = document.getElementById('progress-fill');
        this.progressPercentage = document.getElementById('progress-percentage');
        this.loadingMessage = document.getElementById('loading-message');
        this.loadingScreen = document.getElementById('loading-screen');
        
        if (!this.loadingScreen) {
            console.warn('Loading screen element not found');
            return;
        }

        // Start the loading sequence
        this.startLoading();
    }

    /**
     * Start the loading sequence
     */
    startLoading() {
        this.updateProgress(0);
        this.processNextStep();
    }

    /**
     * Process the next loading step
     */
    processNextStep() {
        if (this.currentStep >= this.steps.length) {
            this.completeLoading();
            return;
        }

        const step = this.steps[this.currentStep];
        const stepElement = document.getElementById(step.id);
        
        // Mark previous step as completed
        if (this.currentStep > 0) {
            const prevStep = document.getElementById(this.steps[this.currentStep - 1].id);
            if (prevStep) {
                prevStep.classList.remove('active');
                prevStep.classList.add('completed');
            }
        }

        // Activate current step
        if (stepElement) {
            stepElement.classList.add('active');
        }

        // Update loading message
        if (this.loadingMessage) {
            this.loadingMessage.textContent = step.message;
        }

        // Calculate progress percentage
        const progressPercent = Math.round(((this.currentStep + 1) / this.steps.length) * 100);
        
        // Animate progress bar
        this.animateProgress(progressPercent, step.duration);

        // Move to next step after duration
        setTimeout(() => {
            this.currentStep++;
            this.processNextStep();
        }, step.duration);
    }

    /**
     * Animate the progress bar to a target percentage
     */
    animateProgress(targetPercent, duration) {
        const startPercent = this.progress;
        const difference = targetPercent - startPercent;
        const startTime = performance.now();

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function for smooth animation
            const easeOutCubic = 1 - Math.pow(1 - progress, 3);
            
            const currentPercent = Math.round(startPercent + (difference * easeOutCubic));
            this.updateProgress(currentPercent);

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    /**
     * Update the progress bar and percentage display
     */
    updateProgress(percent) {
        this.progress = percent;
        
        if (this.progressFill) {
            this.progressFill.style.width = percent + '%';
        }
        
        if (this.progressPercentage) {
            this.progressPercentage.textContent = percent + '%';
        }
    }

    /**
     * Complete the loading sequence
     */
    completeLoading() {
        // Mark final step as completed
        const finalStep = document.getElementById(this.steps[this.steps.length - 1].id);
        if (finalStep) {
            finalStep.classList.remove('active');
            finalStep.classList.add('completed');
        }

        // Update final message
        if (this.loadingMessage) {
            this.loadingMessage.textContent = 'Loading complete!';
        }

        this.isComplete = true;

        // Auto-hide after a short delay
        setTimeout(() => {
            this.hide();
        }, 800);
    }

    /**
     * Hide the loading screen
     */
    hide() {
        if (this.loadingScreen) {
            this.loadingScreen.classList.add('fade-out');
            
            // Remove from DOM after fade animation
            setTimeout(() => {
                if (this.loadingScreen && this.loadingScreen.parentNode) {
                    this.loadingScreen.parentNode.removeChild(this.loadingScreen);
                }
            }, 500);
        }
    }

    /**
     * Show the loading screen (if hidden)
     */
    show() {
        if (this.loadingScreen) {
            this.loadingScreen.classList.remove('fade-out');
            this.loadingScreen.style.display = 'flex';
        }
    }

    /**
     * Reset the loading screen to initial state
     */
    reset() {
        this.currentStep = 0;
        this.progress = 0;
        this.isComplete = false;
        
        // Reset all steps
        this.steps.forEach(step => {
            const stepElement = document.getElementById(step.id);
            if (stepElement) {
                stepElement.classList.remove('active', 'completed');
            }
        });

        this.updateProgress(0);
        
        if (this.loadingMessage) {
            this.loadingMessage.textContent = 'Initializing...';
        }
    }

    /**
     * Set custom loading steps
     */
    setSteps(newSteps) {
        this.steps = newSteps;
        this.reset();
    }

    /**
     * Skip to completion (for testing or fast loading)
     */
    skipToComplete() {
        this.currentStep = this.steps.length;
        this.updateProgress(100);
        this.completeLoading();
    }
}

// Global loading screen instance
let loadingScreen;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    loadingScreen = new LoadingScreen();
    
    // Auto-start if loading screen element exists
    if (document.getElementById('loading-screen')) {
        loadingScreen.init();
    }
});

// Expose to global scope for manual control
window.LoadingScreen = LoadingScreen;
window.loadingScreen = loadingScreen;