<!-- Mohammad Hassan -->
<div class="modal fade" id="wholesalerAuthModal" tabindex="-1" role="dialog" aria-labelledby="wholesalerAuthModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content wholesaler-auth-modal-content">
            <!-- Mohammad Hassan: Enhanced CSS for responsive design -->
            <style>
                /* Modal backdrop enhancement */
                .modal-backdrop.fade.show {
                    background-color: rgba(61, 82, 160, 0.3);
                }

                .modal-lg {
                    max-width: 600px;
                }

                .wholesaler-auth-modal-content {
                    border: none;
                    border-radius: 20px;
                    box-shadow: 0 20px 60px rgba(61, 82, 160, 0.25);
                    overflow: hidden;
                    background: white;
                    margin: 1rem 0;
                }

                .wholesaler-auth-modal-header {
                    background: linear-gradient(135deg, #3D52A0 0%, #7091E6 100%);
                    color: white;
                    padding: 2rem 2.5rem 1.5rem 2.5rem;
                    border: none;
                    position: relative;
                }

                .wholesaler-auth-modal-header::after {
                    content: '';
                    position: absolute;
                    bottom: -10px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 60px;
                    height: 4px;
                    background: linear-gradient(90deg, #8697C4, #ADBBDA);
                    border-radius: 2px;
                }

                .wholesaler-auth-modal-title {
                    font-size: 1.75rem;
                    font-weight: 700;
                    margin: 0;
                    text-align: center;
                    letter-spacing: -0.02em;
                }

                .wholesaler-auth-modal-close {
                    background: rgba(255, 255, 255, 0.2);
                    border: none;
                    color: white;
                    font-size: 1.5rem;
                    opacity: 0.9;
                    transition: all 0.3s ease;
                    position: absolute;
                    right: 1.5rem;
                    top: 1.5rem;
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .wholesaler-auth-modal-close:hover {
                    opacity: 1;
                    background: rgba(255, 255, 255, 0.3);
                    color: white;
                    transform: scale(1.1);
                }

                .wholesaler-auth-modal-body {
                    padding: 2.5rem 2.5rem 2rem 2.5rem;
                    background: white;
                }

                /* Custom Tab Styling */
                .wholesaler-nav-tabs {
                    border: none;
                    margin-bottom: 2rem;
                    background: #F8FAFC;
                    border-radius: 12px;
                    padding: 4px;
                    position: relative;
                }

                .wholesaler-nav-tabs .nav-item {
                    flex: 1;
                }

                .wholesaler-nav-tabs .nav-link {
                    border: none;
                    border-radius: 8px;
                    color: #8697C4;
                    font-weight: 600;
                    padding: 0.875rem 1.5rem;
                    text-align: center;
                    transition: all 0.3s ease;
                    background: transparent;
                    position: relative;
                    z-index: 2;
                }

                .wholesaler-nav-tabs .nav-link.active {
                    background: linear-gradient(135deg, #3D52A0 0%, #7091E6 100%);
                    color: white;
                    box-shadow: 0 4px 15px rgba(61, 82, 160, 0.3);
                }

                .wholesaler-nav-tabs .nav-link:hover:not(.active) {
                    color: #3D52A0;
                    background: rgba(61, 82, 160, 0.08);
                }

                .wholesaler-form-group {
                    margin-bottom: 1.5rem;
                }

                .wholesaler-form-label {
                    color: #3D52A0;
                    font-weight: 600;
                    font-size: 0.9rem;
                    margin-bottom: 0.5rem;
                    display: block;
                }

                .wholesaler-form-control {
                    width: 100%;
                    padding: 0.875rem 1rem;
                    border: 2px solid #ADBBDA;
                    border-radius: 8px;
                    font-size: 0.95rem;
                    transition: all 0.3s ease;
                    background: #FAFBFC;
                    font-weight: 500;
                }

                .wholesaler-form-control:focus {
                    border-color: #3D52A0;
                    box-shadow: 0 0 0 3px rgba(61, 82, 160, 0.12);
                    outline: none;
                    background: white;
                    transform: translateY(-1px);
                }

                .wholesaler-form-control::placeholder {
                    color: #8697C4;
                    font-weight: 400;
                }

                .wholesaler-form-control.is-invalid {
                    border-color: #dc3545;
                    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.12);
                }

                .wholesaler-textarea {
                    resize: vertical;
                    min-height: 80px;
                }

                .wholesaler-btn-primary {
                    width: 100%;
                    padding: 0.875rem 1.5rem;
                    background: linear-gradient(135deg, #3D52A0 0%, #7091E6 100%);
                    border: none;
                    border-radius: 8px;
                    color: white;
                    font-size: 1rem;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                    text-transform: none;
                    letter-spacing: 0.02em;
                }

                .wholesaler-btn-primary:hover:not(:disabled) {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(61, 82, 160, 0.3);
                }

                .wholesaler-btn-primary:active {
                    transform: translateY(0);
                }

                .wholesaler-btn-primary:disabled {
                    opacity: 0.7;
                    cursor: not-allowed;
                    transform: none;
                }

                .wholesaler-btn-secondary {
                    width: 100%;
                    padding: 0.875rem 1.5rem;
                    background: white;
                    border: 2px solid #E1E8ED;
                    border-radius: 8px;
                    color: #3D52A0;
                    font-size: 0.95rem;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    text-decoration: none;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
                }

                .wholesaler-btn-secondary:hover {
                    background: #F8FAFC;
                    border-color: #3D52A0;
                    color: #3D52A0;
                    text-decoration: none;
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(61, 82, 160, 0.15);
                }

                .wholesaler-separator {
                    display: flex;
                    align-items: center;
                    text-align: center;
                    margin: 1.5rem 0;
                    color: #8697C4;
                    font-size: 0.9rem;
                    font-weight: 500;
                }

                .wholesaler-separator::before,
                .wholesaler-separator::after {
                    content: '';
                    flex: 1;
                    border-bottom: 1px solid #E1E8ED;
                }

                .wholesaler-separator span {
                    padding: 0 1.5rem;
                    background: white;
                }

                .wholesaler-google-icon {
                    background-color: white;
                    border-radius: 4px;
                    padding: 3px;
                    height: 24px;
                    width: 24px;
                    flex-shrink: 0;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                }

                .wholesaler-checkbox-container {
                    display: flex;
                    align-items: flex-start;
                    gap: 0.75rem;
                    margin-bottom: 1.5rem;
                }

                .wholesaler-checkbox {
                    margin-top: 2px;
                    accent-color: #3D52A0;
                }

                .wholesaler-checkbox-label {
                    color: #8697C4;
                    font-size: 0.9rem;
                    line-height: 1.4;
                    flex: 1;
                }

                .wholesaler-checkbox-label a {
                    color: #3D52A0;
                    text-decoration: none;
                    font-weight: 600;
                }

                .wholesaler-checkbox-label a:hover {
                    text-decoration: underline;
                }

                .wholesaler-auth-links {
                    text-align: center;
                    margin-top: 1.5rem;
                    padding-top: 1.5rem;
                    border-top: 1px solid #E1E8ED;
                }

                .wholesaler-auth-links small {
                    color: #8697C4;
                    font-size: 0.9rem;
                }

                .wholesaler-auth-links a {
                    color: #3D52A0;
                    text-decoration: none;
                    font-weight: 600;
                    transition: color 0.3s ease;
                }

                .wholesaler-auth-links a:hover {
                    color: #7091E6;
                    text-decoration: none;
                }

                .wholesaler-remember-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 1.5rem;
                    flex-wrap: wrap;
                    gap: 1rem;
                }

                .wholesaler-remember-checkbox {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .wholesaler-remember-checkbox input {
                    accent-color: #3D52A0;
                }

                .wholesaler-remember-checkbox label {
                    color: #8697C4;
                    font-size: 0.9rem;
                    margin: 0;
                    cursor: pointer;
                }

                .wholesaler-forgot-link {
                    color: #7091E6;
                    text-decoration: none;
                    font-size: 0.9rem;
                    font-weight: 500;
                    transition: color 0.3s ease;
                }

                .wholesaler-forgot-link:hover {
                    color: #3D52A0;
                    text-decoration: none;
                }

                /* Responsive Design */
                @media (max-width: 768px) {
                    .modal-dialog {
                        margin: 1rem;
                        max-width: calc(100% - 2rem);
                    }

                    .wholesaler-auth-modal-content {
                        margin: 0.5rem 0;
                        border-radius: 16px;
                    }

                    .wholesaler-auth-modal-header {
                        padding: 1.75rem 2rem 1.25rem 2rem;
                    }

                    .wholesaler-auth-modal-title {
                        font-size: 1.5rem;
                    }

                    .wholesaler-auth-modal-body {
                        padding: 2rem 2rem 1.5rem 2rem;
                    }

                    .wholesaler-nav-tabs {
                        margin-bottom: 1.5rem;
                    }

                    .wholesaler-nav-tabs .nav-link {
                        padding: 0.75rem 1rem;
                        font-size: 0.9rem;
                    }

                    .wholesaler-remember-row {
                        flex-direction: column;
                        align-items: stretch;
                        text-align: center;
                        gap: 0.75rem;
                    }
                }

                @media (max-width: 480px) {
                    .modal-dialog {
                        margin: 0.5rem;
                        max-width: calc(100% - 1rem);
                    }

                    .wholesaler-auth-modal-content {
                        border-radius: 14px;
                    }

                    .wholesaler-auth-modal-header {
                        padding: 1.5rem 1.5rem 1rem 1.5rem;
                    }

                    .wholesaler-auth-modal-title {
                        font-size: 1.35rem;
                    }

                    .wholesaler-auth-modal-body {
                        padding: 1.75rem 1.5rem 1.25rem 1.5rem;
                    }

                    .wholesaler-form-group {
                        margin-bottom: 1.25rem;
                    }

                    .wholesaler-form-control {
                        padding: 0.75rem 0.875rem;
                        font-size: 0.9rem;
                    }

                    .wholesaler-btn-primary,
                    .wholesaler-btn-secondary {
                        padding: 0.75rem 1.25rem;
                        font-size: 0.9rem;
                    }

                    .wholesaler-nav-tabs {
                        padding: 3px;
                    }

                    .wholesaler-nav-tabs .nav-link {
                        padding: 0.625rem 0.75rem;
                        font-size: 0.85rem;
                    }

                    .wholesaler-separator {
                        margin: 1.25rem 0;
                    }
                }

                @media (max-width: 360px) {
                    .wholesaler-auth-modal-body {
                        padding: 1.5rem 1.25rem 1rem 1.25rem;
                    }

                    .wholesaler-form-group {
                        margin-bottom: 1rem;
                    }

                    .wholesaler-separator span {
                        padding: 0 1rem;
                    }
                }

                /* Enhanced animations */
                @keyframes wholesalerModalSlideIn {
                    from {
                        opacity: 0;
                        transform: translate3d(0, -50px, 0) scale(0.95);
                    }

                    to {
                        opacity: 1;
                        transform: translate3d(0, 0, 0) scale(1);
                    }
                }

                .modal.fade .modal-dialog {
                    transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
                }

                .modal.show .modal-dialog {
                    animation: wholesalerModalSlideIn 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
                }
            </style>

            <div class="modal-header wholesaler-auth-modal-header">
                <h5 class="modal-title wholesaler-auth-modal-title" id="wholesalerAuthModalLabel">
                    {{ translate('Wholesaler Access') }}</h5>
                <button type="button" class="close wholesaler-auth-modal-close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body wholesaler-auth-modal-body">
                <!-- Mohammad Hassan: Tab navigation -->
                <ul class="nav nav-tabs wholesaler-nav-tabs" id="wholesalerAuthTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="wholesaler-login-tab" data-toggle="tab" href="#wholesaler-login"
                            role="tab">
                            {{ translate('Login') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="wholesaler-register-tab" data-toggle="tab" href="#wholesaler-register"
                            role="tab">
                            {{ translate('Register') }}
                        </a>
                    </li>
                </ul>

                <!-- Mohammad Hassan: Tab content -->
                <div class="tab-content" id="wholesalerAuthTabContent">
                    <!-- Login Tab -->
                    <div class="tab-pane fade show active" id="wholesaler-login" role="tabpanel">
                        <form class="form-default" role="form" action="{{ route('login') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_type" value="wholesaler">

                            <div class="wholesaler-form-group">
                                <input type="email" class="form-control wholesaler-form-control" value=""
                                    placeholder="{{ translate('Enter your email address') }}" name="email"
                                    id="wholesaler_email" autocomplete="off" required>
                            </div>

                            <div class="wholesaler-form-group">
                                <input type="password" class="form-control wholesaler-form-control"
                                    placeholder="{{ translate('Enter your password') }}" name="password"
                                    id="wholesaler_password" required>
                            </div>

                            <div class="wholesaler-remember-row">
                                <div class="wholesaler-remember-checkbox">
                                    <input type="checkbox" name="remember" id="wholesaler_remember">
                                    <label for="wholesaler_remember">{{ translate('Remember Me') }}</label>
                                </div>
                                <a href="{{ route('password.request') }}"
                                    class="wholesaler-forgot-link">{{ translate('Forgot password?') }}</a>
                            </div>

                            <div class="wholesaler-form-group">
                                <button type="submit"
                                    class="btn wholesaler-btn-primary">{{ translate('Login') }}</button>
                            </div>

                            <!-- Mohammad Hassan: Google Login Button -->
                            <div class="wholesaler-separator">
                                <span>{{ translate('OR') }}</span>
                            </div>

                            <div class="wholesaler-form-group">
                                <a href="{{ route('social.login', ['provider' => 'google']) }}"
                                    class="btn wholesaler-btn-secondary">
                                    <svg class="wholesaler-google-icon" width="18" height="18"
                                        viewBox="0 0 48 48">
                                        <path fill="#EA4335"
                                            d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                                        <path fill="#4285F4"
                                            d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                                        <path fill="#FBBC05"
                                            d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                                        <path fill="#34A853"
                                            d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                                    </svg>
                                    {{ translate('Continue with Google') }}
                                </a>
                            </div>

                            <div class="wholesaler-auth-links">
                                <small>{{ translate("Don't have a wholesaler account?") }}</small>
                                <br>
                                <a href="javascript:void(0)"
                                    onclick="$('#wholesaler-register-tab').tab('show')">{{ translate('Register Now') }}</a>
                            </div>
                        </form>
                    </div>

                    <!-- Register Tab -->
                    <div class="tab-pane fade" id="wholesaler-register" role="tabpanel">
                        <!-- Mohammad Hassan: Updated form to use API endpoint for wholesaler registration -->
                        <form class="form-default" role="form" id="wholesalerRegisterForm" method="POST">
                            @csrf
                            <input type="hidden" name="user_type" value="wholesaler">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="wholesaler-form-group">
                                        <label class="wholesaler-form-label">{{ translate('Business Name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control wholesaler-form-control"
                                            name="name" placeholder="{{ translate('Enter business name') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="wholesaler-form-group">
                                        <label
                                            class="wholesaler-form-label">{{ translate('Trade License Number') }}</label>
                                        <input type="text" class="form-control wholesaler-form-control"
                                            name="trade_license"
                                            placeholder="{{ translate('Enter trade license number') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="wholesaler-form-group">
                                        <label class="wholesaler-form-label">{{ translate('Email Address') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control wholesaler-form-control"
                                            name="email" placeholder="{{ translate('Enter email address') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="wholesaler-form-group">
                                        <label class="wholesaler-form-label">{{ translate('Phone Number') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="tel" class="form-control wholesaler-form-control"
                                            name="phone" placeholder="{{ translate('Enter phone number') }}"
                                            pattern="[0-9]{10,15}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="wholesaler-form-group">
                                        <label class="wholesaler-form-label">{{ translate('Business Address') }} <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control wholesaler-form-control wholesaler-textarea" name="address" rows="3"
                                            placeholder="{{ translate('Enter complete business address') }}" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="wholesaler-form-group">
                                        <label class="wholesaler-form-label">{{ translate('Facebook Link') }}</label>
                                        <input type="url" class="form-control wholesaler-form-control"
                                            name="facebook" placeholder="https://facebook.com/yourpage">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="wholesaler-form-group">
                                        <label class="wholesaler-form-label">{{ translate('Website Link') }}</label>
                                        <input type="url" class="form-control wholesaler-form-control"
                                            name="website" placeholder="https://yourwebsite.com">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="wholesaler-form-group">
                                        <label class="wholesaler-form-label">{{ translate('Password') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control wholesaler-form-control"
                                            name="password" placeholder="{{ translate('Enter password') }}"
                                            minlength="8" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="wholesaler-form-group">
                                        <label class="wholesaler-form-label">{{ translate('Confirm Password') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control wholesaler-form-control"
                                            name="password_confirmation"
                                            placeholder="{{ translate('Confirm password') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="wholesaler-checkbox-container">
                                <input type="checkbox" name="terms" id="wholesaler_terms"
                                    class="wholesaler-checkbox" required>
                                <label for="wholesaler_terms"
                                    class="wholesaler-checkbox-label">{{ translate('I agree to the') }} <a
                                        href="{{ route('terms') }}"
                                        target="_blank">{{ translate('Terms and Conditions') }}</a></label>
                            </div>

                            <div class="wholesaler-form-group">
                                <!-- Mohammad Hassan: Updated button to handle AJAX submission -->
                                <button type="button" class="btn wholesaler-btn-primary"
                                    onclick="submitWholesalerRegistration()">{{ translate('Create Account') }}</button>
                            </div>

                            <!-- Mohammad Hassan: Google Registration Button -->
                            <div class="wholesaler-separator">
                                <span>{{ translate('OR') }}</span>
                            </div>

                            <div class="wholesaler-form-group">
                                <a href="{{ route('social.login', ['provider' => 'google']) }}"
                                    class="btn wholesaler-btn-secondary">
                                    <svg class="wholesaler-google-icon" width="18" height="18"
                                        viewBox="0 0 48 48">
                                        <path fill="#EA4335"
                                            d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                                        <path fill="#4285F4"
                                            d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                                        <path fill="#FBBC05"
                                            d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                                        <path fill="#34A853"
                                            d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                                    </svg>
                                    {{ translate('Continue with Google') }}
                                </a>
                            </div>

                            <div class="wholesaler-auth-links">
                                <small>{{ translate('Already have an account?') }}</small>
                                <br>
                                <a href="javascript:void(0)"
                                    onclick="$('#wholesaler-login-tab').tab('show')">{{ translate('Login here') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Mohammad Hassan
    function openWholesalerLogin() {
        $('#wholesalerAuthModal').modal('show');
        $('#wholesaler-login-tab').tab('show');
    }

    // Mohammad Hassan
    function openWholesalerRegister() {
        $('#wholesalerAuthModal').modal('show');
        $('#wholesaler-register-tab').tab('show');
    }

    // Mohammad Hassan: AJAX function for wholesaler registration
    function submitWholesalerRegistration() {
        const form = document.getElementById('wholesalerRegisterForm');
        const formData = new FormData();

        // Map form fields to API expected field names
        formData.append('businessName', form.querySelector('[name="name"]').value);
        formData.append('email', form.querySelector('[name="email"]').value);
        formData.append('phone', form.querySelector('[name="phone"]').value);
        formData.append('address', form.querySelector('[name="address"]').value);
        formData.append('password', form.querySelector('[name="password"]').value);
        formData.append('confirmPassword', form.querySelector('[name="password_confirmation"]').value);
        formData.append('facebookLink', form.querySelector('[name="facebook"]').value || '');
        formData.append('websiteLink', form.querySelector('[name="website"]').value || '');
        formData.append('tradeLicense', form.querySelector('[name="trade_license"]').value || '');

        // Validate required fields
        const requiredFields = [{
                name: 'businessName',
                element: form.querySelector('[name="name"]')
            },
            {
                name: 'email',
                element: form.querySelector('[name="email"]')
            },
            {
                name: 'phone',
                element: form.querySelector('[name="phone"]')
            },
            {
                name: 'address',
                element: form.querySelector('[name="address"]')
            },
            {
                name: 'password',
                element: form.querySelector('[name="password"]')
            },
            {
                name: 'confirmPassword',
                element: form.querySelector('[name="password_confirmation"]')
            }
        ];

        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.element.value.trim()) {
                isValid = false;
                field.element.classList.add('is-invalid');
            } else {
                field.element.classList.remove('is-invalid');
            }
        });

        // Check if terms checkbox is checked
        const termsCheckbox = form.querySelector('[name="terms"]');
        if (!termsCheckbox.checked) {
            isValid = false;
            termsCheckbox.closest('.wholesaler-checkbox-container').style.borderColor = '#dc3545';
        } else {
            termsCheckbox.closest('.wholesaler-checkbox-container').style.borderColor = '';
        }

        // Check password confirmation
        const password = form.querySelector('[name="password"]').value;
        const passwordConfirmation = form.querySelector('[name="password_confirmation"]').value;
        if (password !== passwordConfirmation) {
            isValid = false;
            form.querySelector('[name="password_confirmation"]').classList.add('is-invalid');
            alert('{{ translate('Passwords do not match') }}');
            return;
        }

        if (!isValid) {
            alert('{{ translate('Please fill in all required fields') }}');
            return;
        }

        // Show loading state
        const submitBtn = document.querySelector(
            '#wholesalerRegisterForm button[onclick="submitWholesalerRegistration()"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '{{ translate('Creating Account...') }}';
        submitBtn.disabled = true;

        // Submit via AJAX to API endpoint
        fetch('/api/v2/auth/wholesaler-register', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Mohammad Hassan
                if (data.result) {
                    if (typeof AIZ !== 'undefined' && AIZ.plugins && AIZ.plugins.notify) {
                        AIZ.plugins.notify('success',
                            '{{ translate('Registration successful! Your account is pending approval.') }}');
                    } else {
                        alert('{{ translate('Registration successful! Your account is pending approval.') }}');
                    }
                    $('#wholesalerAuthModal').modal('hide');
                    form.reset();
                    // Redirect to login or refresh page
                    window.location.reload();
                } else {
                    let errorMessage = '';
                    if (typeof data.message === 'object') {
                        // Handle validation errors
                        errorMessage = '{{ translate('Please fix the following errors:') }}\n';
                        Object.keys(data.message).forEach(key => {
                            errorMessage += '• ' + data.message[key].join(', ') + '\n';
                        });
                    } else {
                        errorMessage = data.message || '{{ translate('Registration failed. Please try again.') }}';
                    }

                    if (typeof AIZ !== 'undefined' && AIZ.plugins && AIZ.plugins.notify) {
                        AIZ.plugins.notify('danger', errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                }
            })
            .catch(error => {
                // Mohammad Hassan
                console.error('Error:', error);
                const errorMessage = '{{ translate('An error occurred. Please try again.') }}';
                if (typeof AIZ !== 'undefined' && AIZ.plugins && AIZ.plugins.notify) {
                    AIZ.plugins.notify('danger', errorMessage);
                } else {
                    alert(errorMessage);
                }
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
    }
</script>

