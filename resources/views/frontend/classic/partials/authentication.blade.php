    <!-- User Authentication Modal -->
<div id="userAuthModal" class="auth-modal">
    <div class="auth-modal-content">
        <span class="auth-close-button" onclick="closeUserModal()">&times;</span>
        <div class="auth-header">
            <h2 id="userModalTitle">User Access</h2>
            <p class="auth-subtitle">Enter your email to continue</p>
        </div>

        <!-- Email Input Step -->
        <div id="userEmailStep" class="auth-step active">
            <form onsubmit="handleUserEmailSubmit(event)">
                <div class="auth-form-group">
                    <label for="userEmail">Email Address:</label>
                    <input type="email" id="userEmail" name="userEmail" placeholder="Enter your email" required>
                </div>
                <button type="submit" class="auth-submit-btn primary">Continue</button>
            </form>
            
            <div class="auth-divider">
                <span>OR</span>
            </div>
            
            <button class="google-signin-btn" onclick="handleGoogleSignIn()">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/2048px-Google_%22G%22_logo.svg.png" alt="Google">
                Continue with Google
            </button>
        </div>

        <!-- Verification Step -->
        <div id="userVerificationStep" class="auth-step" style="display: none;">
            <div class="verification-info">
                <div class="verification-icon">✉️</div>
                <h3>Check Your Email</h3>
                <p>We've sent a verification code to <span id="userEmailDisplay"></span></p>
            </div>
            
            <form onsubmit="handleUserVerification(event)">
                <div class="auth-form-group">
                    <label for="verificationCode">Verification Code:</label>
                    <input type="text" id="verificationCode" name="verificationCode" placeholder="Enter 6-digit code" maxlength="6" required>
                </div>
                <button type="submit" class="auth-submit-btn primary">Verify & Continue</button>
            </form>
            
            <div class="auth-secondary-actions">
                <button class="link-btn" onclick="resendVerificationCode()">Resend Code</button>
                <button class="link-btn" onclick="goBackToEmail()">Change Email</button>
            </div>
        </div>
    </div>
</div>

<!-- Wholesaler Authentication Modal -->
<div id="wholesalerAuthModal" class="auth-modal">
    <div class="auth-modal-content">
        <span class="auth-close-button" onclick="closeWholesalerModal()">&times;</span>
        <h2 id="wholesalerModalTitle">Wholesaler Access</h2>

        <div class="auth-modal-tabs">
            <button class="auth-tab-button active" id="wholesalerLoginBtn" onclick="switchWholesalerTab('login')">Login</button>
            <button class="auth-tab-button" id="wholesalerRegisterBtn" onclick="switchWholesalerTab('register')">Register</button>
        </div>

        <!-- Wholesaler Login -->
        <div id="wholesalerLoginSection" class="auth-section">
            <form onsubmit="handleWholesalerLogin(event)">
                <div class="auth-form-group">
                    <label for="wholesalerLoginEmail">Email:</label>
                    <input type="email" id="wholesalerLoginEmail" name="email" required>
                </div>
                <div class="auth-form-group">
                    <label for="wholesalerLoginPassword">Password:</label>
                    <input type="password" id="wholesalerLoginPassword" name="password" required>
                </div>
                <div class="auth-forgot-password">
                    <a href="#" onclick="showForgotPassword()">Forgot Password?</a>
                </div>
                <button type="submit" class="auth-submit-btn primary">Login as Wholesaler</button>
            </form>
            <p class="auth-secondary-link">
                Don't have an account? 
                <a href="#" onclick="switchWholesalerTab('register')">Register here</a>
            </p>
        </div>

        <!-- Wholesaler Registration -->
        <div id="wholesalerRegisterSection" class="auth-section" style="display: none;">
            <form onsubmit="handleWholesalerRegistration(event)">
                <div class="form-grid">
                    <div class="auth-form-group">
                        <label for="businessName">Business Name: <span class="required">*</span></label>
                        <input type="text" id="businessName" name="businessName" required>
                    </div>
                    <div class="auth-form-group">
                        <label for="wholesalerPhone">Phone Number: <span class="required">*</span></label>
                        <input type="tel" id="wholesalerPhone" name="phone" pattern="[0-9]{10,15}" required>
                    </div>
                </div>
                
                <div class="auth-form-group">
                    <label for="wholesalerEmail">Email Address: <span class="required">*</span></label>
                    <input type="email" id="wholesalerEmail" name="email" required>
                </div>
                
                <div class="form-grid">
                    <div class="auth-form-group">
                        <label for="facebookLink">Facebook Link:</label>
                        <input type="url" id="facebookLink" name="facebookLink" placeholder="https://facebook.com/yourpage">
                    </div>
                    <div class="auth-form-group">
                        <label for="websiteLink">Website Link:</label>
                        <input type="url" id="websiteLink" name="websiteLink" placeholder="https://yourwebsite.com">
                    </div>
                </div>
                
                <div class="auth-form-group">
                    <label for="businessAddress">Business Address: <span class="required">*</span></label>
                    <textarea id="businessAddress" name="address" rows="3" required></textarea>
                </div>
                
                <div class="auth-form-group">
                    <label for="tradeLicense">Trade License Number:</label>
                    <input type="text" id="tradeLicense" name="tradeLicense">
                </div>
                
                <div class="form-grid">
                    <div class="auth-form-group">
                        <label for="wholesalerRegPassword">Password: <span class="required">*</span></label>
                        <input type="password" id="wholesalerRegPassword" name="password" minlength="8" required>
                    </div>
                    <div class="auth-form-group">
                        <label for="wholesalerConfirmPassword">Confirm Password: <span class="required">*</span></label>
                        <input type="password" id="wholesalerConfirmPassword" name="confirmPassword" required>
                    </div>
                </div>
                
                <div class="terms-checkbox">
                    <input type="checkbox" id="agreeTerms" required>
                    <label for="agreeTerms">I agree to the <a href="#" target="_blank">Terms & Conditions</a></label>
                </div>
                
                <button type="submit" class="auth-submit-btn primary">Register as Wholesaler</button>
            </form>
            <p class="auth-secondary-link">
                Already have an account? 
                <a href="#" onclick="switchWholesalerTab('login')">Login here</a>
            </p>
        </div>
    </div>
</div>


<style>
    .auth-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    align-items: center;
    justify-content: center;
}

.auth-modal-content {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.auth-close-button {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.5rem;
    cursor: pointer;
    color: #999;
    transition: color 0.2s;
}

.auth-close-button:hover {
    color: #333;
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-header h2 {
    margin: 0 0 0.5rem 0;
    color: #333;
    font-size: 1.5rem;
}

.auth-subtitle {
    color: #666;
    margin: 0;
    font-size: 0.9rem;
}

.auth-step {
    display: none;
}

.auth-step.active {
    display: block;
}

.auth-form-group {
    margin-bottom: 1.5rem;
}

.auth-form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 500;
}

.required {
    color: #e74c3c;
}

.auth-form-group input,
.auth-form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s;
}

.auth-form-group input:focus,
.auth-form-group textarea:focus {
    outline: none;
    border-color: #3498db;
}

.auth-submit-btn {
    width: 100%;
    padding: 0.875rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.auth-submit-btn.primary {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
}

.auth-submit-btn.primary:hover {
    background: linear-gradient(135deg, #2980b9, #21618c);
    transform: translateY(-1px);
}

.auth-divider {
    display: flex;
    align-items: center;
    margin: 1.5rem 0;
    color: #999;
}

.auth-divider::before,
.auth-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e1e5e9;
}

.auth-divider span {
    margin: 0 1rem;
    font-size: 0.875rem;
}

.google-signin-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 0.875rem;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    background: white;
    color: #333;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 500;
    transition: all 0.2s;
    text-decoration: none;
}

.google-signin-btn:hover {
    border-color: #4285f4;
    background: #f8f9fa;
}

.google-signin-btn img {
    width: 20px;
    height: 20px;
}

.verification-info {
    text-align: center;
    margin-bottom: 2rem;
}

.verification-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.verification-info h3 {
    margin: 0 0 0.5rem 0;
    color: #333;
}

.verification-info p {
    color: #666;
    margin: 0;
}

#userEmailDisplay {
    color: #3498db;
    font-weight: 600;
}

.auth-secondary-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
}

.link-btn {
    background: none;
    border: none;
    color: #3498db;
    cursor: pointer;
    text-decoration: underline;
    font-size: 0.875rem;
}

.auth-modal-tabs {
    display: flex;
    margin-bottom: 2rem;
    border-bottom: 2px solid #e1e5e9;
}

.auth-tab-button {
    flex: 1;
    padding: 1rem;
    border: none;
    background: none;
    cursor: pointer;
    font-size: 1rem;
    color: #666;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
}

.auth-tab-button.active {
    color: #3498db;
    border-bottom-color: #3498db;
}

.auth-section {
    display: none;
}

.auth-section.active {
    display: block;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.auth-forgot-password {
    text-align: right;
    margin-bottom: 1rem;
}

.auth-forgot-password a {
    color: #3498db;
    text-decoration: none;
    font-size: 0.875rem;
}

.terms-checkbox {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.terms-checkbox input[type="checkbox"] {
    width: auto;
    margin-top: 0.25rem;
}

.auth-secondary-link {
    text-align: center;
    margin-top: 1.5rem;
    color: #666;
    font-size: 0.875rem;
}

.auth-secondary-link a {
    color: #3498db;
    text-decoration: none;
}

@media (max-width: 768px) {
    .auth-modal-content {
        margin: 1rem;
        max-width: none;
        padding: 1.5rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .auth-secondary-actions {
        flex-direction: column;
        gap: 0.5rem;
        align-items: center;
    }
}

</style>

<script>
    // User Modal Functions
function openUserLogin() {
    document.getElementById('userAuthModal').style.display = 'flex';
    resetUserModal();
}

function closeUserModal() {
    document.getElementById('userAuthModal').style.display = 'none';
    resetUserModal();
}

function resetUserModal() {
    document.getElementById('userEmailStep').classList.add('active');
    document.getElementById('userVerificationStep').classList.remove('active');
    document.getElementById('userEmail').value = '';
    document.getElementById('verificationCode').value = '';
}

function handleUserEmailSubmit(event) {
    event.preventDefault();
    const email = document.getElementById('userEmail').value;
    
    const submitBtn = event.target.querySelector('button');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Sending...';
    submitBtn.disabled = true;
    
    fetch('/api/v2/auth/user-email-submit', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            // Mohammad Hassan
            'System-Key': '{{ config('app.system_key') }}',
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.result) {
            document.getElementById('userEmailStep').classList.remove('active');
            document.getElementById('userVerificationStep').classList.add('active');
            document.getElementById('userEmailDisplay').textContent = email;
        } else {
            const message = Array.isArray(data.message) ? data.message[0] : data.message;
            alert(message || 'Error sending verification code');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function handleUserVerification(event) {
    event.preventDefault();
    const email = document.getElementById('userEmail').value;
    const code = document.getElementById('verificationCode').value;
    
    const submitBtn = event.target.querySelector('button');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Verifying...';
    submitBtn.disabled = true;
    
    fetch('/api/v2/auth/user-verify-code', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            // Mohammad Hassan
            'System-Key': '{{ config('app.system_key') }}',
        },
        body: JSON.stringify({ email: email, code: code })
    })
    .then(response => response.json())
    .then(data => {
        if (data.result) {
            // Store token and user data
            localStorage.setItem('auth_token', data.access_token);
            localStorage.setItem('user', JSON.stringify(data.user));
            
            alert('Email verified successfully! You are now logged in.');
            closeUserModal();
            window.location.reload();
        } else {
            const message = Array.isArray(data.message) ? data.message[0] : data.message;
            alert(message || 'Invalid verification code');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}


// function handleGoogleSignIn() {
//     // TODO: Implement Google OAuth
//     alert('Google Sign-in integration coming soon!');
// }

function resendVerificationCode() {
    const email = document.getElementById('userEmail').value;
    
    fetch('/api/v2/auth/user-resend-code', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            // Mohammad Hassan
            'System-Key': '{{ config('app.system_key') }}',
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.result) {
            alert('Verification code resent successfully!');
        } else {
            const message = Array.isArray(data.message) ? data.message[0] : data.message;
            alert(message || 'Error resending code');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    });
}

function goBackToEmail() {
    document.getElementById('userVerificationStep').classList.remove('active');
    document.getElementById('userEmailStep').classList.add('active');
}

// Wholesaler Modal Functions
function openWholesalerLogin() {
    document.getElementById('wholesalerAuthModal').style.display = 'flex';
    switchWholesalerTab('login');
}

function closeWholesalerModal() {
    document.getElementById('wholesalerAuthModal').style.display = 'none';
}

function switchWholesalerTab(tab) {
    const loginBtn = document.getElementById('wholesalerLoginBtn');
    const registerBtn = document.getElementById('wholesalerRegisterBtn');
    const loginSection = document.getElementById('wholesalerLoginSection');
    const registerSection = document.getElementById('wholesalerRegisterSection');
    
    if (tab === 'login') {
        loginBtn.classList.add('active');
        registerBtn.classList.remove('active');
        loginSection.style.display = 'block';
        registerSection.style.display = 'none';
        document.getElementById('wholesalerModalTitle').textContent = 'Wholesaler Login';
    } else {
        registerBtn.classList.add('active');
        loginBtn.classList.remove('active');
        registerSection.style.display = 'block';
        loginSection.style.display = 'none';
        document.getElementById('wholesalerModalTitle').textContent = 'Wholesaler Registration';
    }
}

function handleWholesalerLogin(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    // TODO: Implement login API call
    console.log('Wholesaler login:', Object.fromEntries(formData));
    alert('Wholesaler login successful!');
    closeWholesalerModal();
}

function handleWholesalerRegistration(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData);
    
    if (data.password !== data.confirmPassword) {
        alert('Passwords do not match!');
        return;
    }
    
    // TODO: Implement registration API call
    console.log('Wholesaler registration:', data);
    alert('Registration submitted! Please wait for admin approval.');
    closeWholesalerModal();
}

// Close modal when clicking outside
window.onclick = function(event) {
    const userModal = document.getElementById('userAuthModal');
    const wholesalerModal = document.getElementById('wholesalerAuthModal');
    
    if (event.target === userModal) {
        closeUserModal();
    }
    if (event.target === wholesalerModal) {
        closeWholesalerModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeUserModal();
        closeWholesalerModal();
    }
});

</script>

