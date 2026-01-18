/**
 * Login Popup Message
 * This script displays a popup message when a user tries to view project documents without logging in
 */

function showLoginRequiredPopup() {
    // Create modal backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    document.body.appendChild(backdrop);
    
    // Create modal HTML with PUP theme colors (maroon and gold)
    const modalHTML = `
    <div class="modal fade show" id="loginRequiredModal" tabindex="-1" role="dialog" aria-labelledby="loginRequiredModalLabel" style="display: block; padding-right: 17px;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--pup-maroon); color: var(--pup-text-light);">
                    <h5 class="modal-title" id="loginRequiredModalLabel" style="color: var(--pup-text-light);">Login Required</h5>
                    <button type="button" class="close" onclick="closeLoginPopup()" aria-label="Close" style="color: var(--pup-text-light);">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center">Login is required to view project documents.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" style="background-color: var(--pup-maroon); color: var(--pup-text-light);" onclick="redirectToLogin()">Login</button>
                    <button type="button" class="btn" style="background-color: var(--pup-secondary); color: var(--pup-text-light);" onclick="closeLoginPopup()">Cancel</button>
                </div>
            </div>
        </div>
    </div>`;
    
    // Add modal to body
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = modalHTML;
    document.body.appendChild(modalContainer);
    
    // Prevent body scrolling
    document.body.classList.add('modal-open');
}

function closeLoginPopup() {
    // Remove modal and backdrop
    const modal = document.getElementById('loginRequiredModal');
    if (modal) {
        modal.parentNode.remove();
    }
    
    // Remove backdrop
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
    
    // Re-enable body scrolling
    document.body.classList.remove('modal-open');
}

function redirectToLogin() {
    window.location.href = './login.php';
}
