<!-- aiz-main-wrapper -->
<div class="aiz-main-wrapper d-flex flex-column justify-content-center" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); overflow: hidden; position: relative;">
    
    <!-- Animated Background Elements -->
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
    </div>

    <section class="py-5 position-relative">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-5 col-md-7 col-sm-9">
                    
                    <!-- Glassmorphism Login Card -->
                    <div class="glass-card">
                        
                        <!-- Site Icon -->
                        <div class="text-center mb-4">
                            <div class="site-icon-wrapper">
                                <img src="{{ uploaded_asset(get_setting('site_icon')) }}" alt="{{ translate('Site Icon')}}" class="site-icon">
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="text-center mb-4">
                            <h1 class="login-title">{{ translate('Login') }}</h1>
                            <p class="login-subtitle">{{ translate('Welcome back! Please login to your account') }}</p>
                        </div>

                        <!-- Login Form -->
                        <form class="glass-form" id="login-form" role="form" action="{{ route('login') }}" method="POST">
                            @csrf
                            
                            <!-- Email Field -->
                            <div class="form-group-glass">
                                <div class="input-wrapper">
                                    <input type="email" 
                                           class="glass-input{{ $errors->has('email') ? ' is-invalid' : '' }}" 
                                           value="{{ old('email') }}" 
                                           name="email" 
                                           id="email" 
                                           placeholder="{{ translate('Email') }}"
                                           autocomplete="off"
                                           required>
                                    <i class="input-icon fas fa-envelope"></i>
                                </div>
                                @if ($errors->has('email'))
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $errors->first('email') }}
                                    </div>
                                @endif
                            </div>
                                
                            <!-- Password Field -->
                            <div class="form-group-glass">
                                <div class="input-wrapper">
                                    <input type="password" 
                                           class="glass-input{{ $errors->has('password') ? ' is-invalid' : '' }}" 
                                           placeholder="{{ translate('Password') }}" 
                                           name="password" 
                                           id="password"
                                           required>
                                    <i class="input-icon fas fa-lock"></i>
                                    <i class="password-toggle fas fa-eye" onclick="togglePassword()"></i>
                                </div>
                                @if ($errors->has('password'))
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $errors->first('password') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="form-options">
                                <label class="glass-checkbox">
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    <span class="checkbox-text">{{ translate('Remember Me') }}</span>
                                </label>
                                
                                <a href="{{ route('password.request') }}" class="forgot-link">
                                    {{ translate('Forgot Password?') }}
                                </a>
                            </div>

                            <!-- Recaptcha -->
                            @if(get_setting('google_recaptcha') == 1 && get_setting('recaptcha_admin_login') == 1)
                                <div class="recaptcha-wrapper">
                                    {!! NoCaptcha::display() !!}
                                </div>
                                @if ($errors->has('g-recaptcha-response'))
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $errors->first('g-recaptcha-response') }}
                                    </div>
                                @endif
                            @endif

                            <!-- Submit Button -->
                            <button type="submit" class="glass-submit-btn">
                                <span class="btn-text">{{ translate('Log In') }}</span>
                                <i class="btn-icon fas fa-arrow-right"></i>
                            </button>
                        </form>

                        <!-- Demo Mode -->
                        @if (env("DEMO_MODE") == "On")
                            <div class="demo-credentials">
                                <div class="demo-header">
                                    <i class="fas fa-info-circle"></i>
                                    <span>{{ translate('Demo Credentials') }}</span>
                                </div>
                                <div class="demo-item">
                                    <span class="demo-email">admin@example.com</span>
                                    <span class="demo-password">123456</span>
                                    <button class="demo-copy-btn" onclick="autoFillAdmin()">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Back Link -->
                        <div class="back-link-wrapper">
                            <a href="{{ url()->previous() }}" class="back-link">
                                <i class="fas fa-arrow-left"></i>
                                {{ translate('Back to Previous Page') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Main Container */
.aiz-main-wrapper {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
}

/* Floating Background Shapes */
.floating-shapes {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    animation: float 6s ease-in-out infinite;
}

.shape-1 {
    width: 120px;
    height: 120px;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.shape-2 {
    width: 80px;
    height: 80px;
    top: 70%;
    right: 15%;
    animation-delay: 2s;
}

.shape-3 {
    width: 60px;
    height: 60px;
    top: 20%;
    right: 25%;
    animation-delay: 4s;
}

.shape-4 {
    width: 100px;
    height: 100px;
    bottom: 20%;
    left: 15%;
    animation-delay: 1s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(10deg); }
}

/* Glassmorphism Card */
.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 40px 30px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
    max-width: 420px;
    margin: 0 auto;
}

.glass-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.15);
}

/* Site Icon */
.site-icon-wrapper {
    width: 70px;
    height: 70px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
}

.site-icon {
    width: 45px;
    height: 45px;
    object-fit: contain;
}

/* Typography */
.login-title {
    color: white;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
    text-align: center;
}

.login-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 16px;
    font-weight: 400;
    margin-bottom: 0;
    text-align: center;
}

/* Form Styling */
.glass-form {
    width: 100%;
}

.form-group-glass {
    margin-bottom: 25px;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.glass-input {
    width: 100%;
    height: 55px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 0 50px 0 50px;
    font-size: 16px;
    color: white;
    outline: none;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.glass-input::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.glass-input:focus {
    border-color: rgba(255, 255, 255, 0.4);
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
}

.input-icon {
    position: absolute;
    left: 18px;
    color: rgba(255, 255, 255, 0.6);
    font-size: 16px;
    z-index: 3;
}

.password-toggle {
    position: absolute;
    right: 18px;
    color: rgba(255, 255, 255, 0.6);
    font-size: 16px;
    cursor: pointer;
    z-index: 3;
    transition: all 0.3s ease;
}

.password-toggle:hover {
    color: white;
}

/* Form Options */
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.glass-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
}

.glass-checkbox input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    margin-right: 10px;
    position: relative;
    transition: all 0.3s ease;
}

.glass-checkbox input[type="checkbox"]:checked + .checkmark {
    background: rgba(255, 255, 255, 0.2);
    border-color: white;
}

.glass-checkbox input[type="checkbox"]:checked + .checkmark:after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.checkbox-text {
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
}

.forgot-link {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.forgot-link:hover {
    color: white;
    text-decoration: underline;
}

/* Submit Button */
.glass-submit-btn {
    width: 100%;
    height: 55px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 15px;
    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 25px;
}

.glass-submit-btn:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.2));
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.btn-icon {
    transition: all 0.3s ease;
}

.glass-submit-btn:hover .btn-icon {
    transform: translateX(5px);
}

/* Error Messages */
.error-message {
    color: #ff6b6b;
    font-size: 14px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Demo Credentials */
.demo-credentials {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    backdrop-filter: blur(10px);
}

.demo-header {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 15px;
}

.demo-item {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.demo-email, .demo-password {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 14px;
    font-family: monospace;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.demo-copy-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: white;
    padding: 8px 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.demo-copy-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* Back Link */
.back-link-wrapper {
    text-align: center;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.back-link:hover {
    color: white;
    transform: translateX(-3px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .glass-card {
        margin: 20px;
        padding: 30px 25px;
    }
    
    .login-title {
        font-size: 28px;
    }
    
    .form-options {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .demo-item {
        flex-direction: column;
        align-items: stretch;
    }
    
    .demo-email, .demo-password {
        text-align: center;
    }
}

@media (max-width: 576px) {
    .glass-card {
        margin: 15px;
        padding: 25px 20px;
    }
    
    .shape {
        display: none;
    }
}

/* Animation for page load */
.glass-card {
    animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
// Password toggle functionality
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.password-toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Auto-fill demo credentials
function autoFillAdmin() {
    document.getElementById('email').value = 'admin@example.com';
    document.getElementById('password').value = '123456';
}

// Add focus effects
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.glass-input');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});
</script>

