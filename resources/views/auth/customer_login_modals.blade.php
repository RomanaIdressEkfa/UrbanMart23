<div class="modal fade" id="customerAuthModal" tabindex="-1" role="dialog" aria-labelledby="customerAuthModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content auth-modal-content">
            <!-- Mohammad Hassan: Enhanced CSS for more attractive design -->
            <style>
                .auth-modal-content {
                    border: none;
                    border-radius: 20px;
                    box-shadow: 0 15px 50px rgba(61, 82, 160, 0.2);
                    overflow: hidden;
                    background: white;
                    margin: 2rem 0;
                    /* উপরে নিচে margin */
                    position: relative;
                }

                /* Attractive border around modal */
                .auth-modal-content::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: linear-gradient(135deg, #3D52A0, #7091E6, #8697C4);
                    border-radius: 20px;
                    padding: 3px;
                    z-index: -1;
                }

                .auth-modal-content::after {
                    content: '';
                    position: absolute;
                    top: 3px;
                    left: 3px;
                    right: 3px;
                    bottom: 3px;
                    background: white;
                    border-radius: 17px;
                    z-index: -1;
                }

                .auth-modal-header {
                    background: linear-gradient(135deg, #3D52A0 0%, #7091E6 100%);
                    color: white;
                    padding: 2rem 2rem 1.5rem;
                    border: none;
                    position: relative;
                    margin: 0;
                }

                /* Decorative element */
                .auth-modal-header::before {
                    content: '';
                    position: absolute;
                    bottom: -10px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 60px;
                    height: 4px;
                    background: linear-gradient(90deg, #ADBBDA, #EDE8F5);
                    border-radius: 2px;
                }

                .auth-modal-title {
                    font-size: 1.75rem;
                    font-weight: 700;
                    margin: 0;
                    text-align: center;
                    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }

                .auth-modal-close {
                    background: rgba(255, 255, 255, 0.2);
                    border: none;
                    color: white;
                    font-size: 1.5rem;
                    opacity: 0.9;
                    transition: all 0.3s ease;
                    position: absolute;
                    right: 1.5rem;
                    top: 1.5rem;
                    width: 35px;
                    height: 35px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .auth-modal-close:hover {
                    opacity: 1;
                    color: white;
                    background: rgba(255, 255, 255, 0.3);
                    transform: rotate(90deg);
                }

                .auth-modal-body {
                    padding: 2.5rem 2rem 2rem;
                    background: white;
                    margin: 0;
                    position: relative;
                }

                /* Subtle background pattern */
                .auth-modal-body::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 40px;
                    background: linear-gradient(180deg, #EDE8F5 0%, transparent 100%);
                    opacity: 0.3;
                }

                .auth-form-group {
                    margin-bottom: 1.75rem;
                    position: relative;
                }

                .auth-form-control {
                    width: 100%;
                    padding: 1rem 1.25rem;
                    border: 2px solid #ADBBDA;
                    border-radius: 12px;
                    font-size: 1rem;
                    transition: all 0.3s ease;
                    background: white;
                    box-shadow: 0 2px 8px rgba(173, 187, 218, 0.1);
                }

                .auth-form-control:focus {
                    border-color: #3D52A0;
                    box-shadow: 0 0 0 4px rgba(61, 82, 160, 0.15), 0 4px 12px rgba(61, 82, 160, 0.1);
                    outline: none;
                    transform: translateY(-1px);
                }

                .auth-form-control::placeholder {
                    color: #8697C4;
                    font-weight: 400;
                }

                .auth-btn-primary {
                    width: 100%;
                    padding: 1rem 1.5rem;
                    background: linear-gradient(135deg, #3D52A0 0%, #7091E6 100%);
                    border: none;
                    border-radius: 12px;
                    color: white;
                    font-size: 1.1rem;
                    font-weight: 700;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                    box-shadow: 0 4px 15px rgba(61, 82, 160, 0.3);
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }

                .auth-btn-primary::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                    transition: left 0.5s;
                }

                .auth-btn-primary:hover::before {
                    left: 100%;
                }

                .auth-btn-primary:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 8px 30px rgba(61, 82, 160, 0.4);
                }

                .auth-btn-primary:active {
                    transform: translateY(-1px);
                }

                .auth-btn-secondary {
                    width: 100%;
                    padding: 1rem 1.5rem;
                    background: white;
                    border: 2px solid #8697C4;
                    border-radius: 12px;
                    color: #3D52A0;
                    font-size: 1rem;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    text-decoration: none;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    box-shadow: 0 2px 10px rgba(134, 151, 196, 0.15);
                }

                .auth-btn-secondary:hover {
                    background: #EDE8F5;
                    border-color: #3D52A0;
                    color: #3D52A0;
                    text-decoration: none;
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(61, 82, 160, 0.2);
                }

                .separator {
                    display: flex;
                    align-items: center;
                    text-align: center;
                    margin: 2rem 0;
                    color: #8697C4;
                    font-size: 0.9rem;
                    font-weight: 600;
                    position: relative;
                }

                .separator::before,
                .separator::after {
                    content: '';
                    flex: 1;
                    border-bottom: 2px solid #ADBBDA;
                    opacity: 0.3;
                }

                .separator span {
                    padding: 0 1.5rem;
                    background: white;
                    position: relative;
                }

                .separator span::before {
                    content: '';
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: calc(100% + 20px);
                    height: 30px;
                    background: radial-gradient(ellipse, rgba(237, 232, 245, 0.5) 0%, transparent 70%);
                    z-index: -1;
                }

                .google-icon {
                    background-color: white;
                    border-radius: 6px;
                    padding: 3px;
                    height: 26px;
                    width: 26px;
                    flex-shrink: 0;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                }

                .auth-info-text {
                    color: #8697C4;
                    font-size: 0.95rem;
                    text-align: center;
                    margin-bottom: 1.75rem;
                    line-height: 1.5;
                    padding: 1rem;
                    background: rgba(237, 232, 245, 0.3);
                    border-radius: 10px;
                    border-left: 4px solid #ADBBDA;
                }

                .auth-verification-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 1.75rem;
                    padding: 1.25rem;
                    background: linear-gradient(135deg, #EDE8F5 0%, rgba(237, 232, 245, 0.5) 100%);
                    border-radius: 12px;
                    border: 1px solid rgba(173, 187, 218, 0.3);
                }

                .auth-email-display {
                    color: #3D52A0;
                    font-weight: 700;
                    font-size: 1rem;
                }

                .auth-change-link {
                    color: #7091E6;
                    text-decoration: none;
                    font-weight: 600;
                    font-size: 0.9rem;
                    transition: all 0.3s ease;
                    padding: 0.5rem 1rem;
                    border-radius: 20px;
                    background: rgba(112, 145, 230, 0.1);
                }

                .auth-change-link:hover {
                    color: #3D52A0;
                    text-decoration: none;
                    background: rgba(61, 82, 160, 0.1);
                }

                .auth-verification-actions {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 1.5rem;
                    flex-wrap: wrap;
                    margin-top: 1.5rem;
                }

                .auth-resend-section {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    flex: 1;
                }

                .auth-resend-btn {
                    background: none;
                    border: none;
                    color: #7091E6;
                    font-weight: 600;
                    text-decoration: underline;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    padding: 0.5rem;
                    border-radius: 6px;
                }

                .auth-resend-btn:hover:not(:disabled) {
                    color: #3D52A0;
                    background: rgba(112, 145, 230, 0.1);
                }

                .auth-resend-btn:disabled {
                    color: #ADBBDA;
                    cursor: not-allowed;
                    text-decoration: none;
                    background: transparent;
                }

                .auth-timer {
                    color: #8697C4;
                    font-size: 0.9rem;
                    font-weight: 600;
                    background: rgba(134, 151, 196, 0.1);
                    padding: 0.25rem 0.75rem;
                    border-radius: 15px;
                }

                .auth-verify-btn {
                    flex-shrink: 0;
                    min-width: 160px;
                }

                /* Enhanced Responsive Design */
                @media (max-width: 768px) {
                    .modal-dialog {
                        margin: 1.5rem;
                        max-width: calc(100% - 3rem);
                    }

                    .auth-modal-content {
                        margin: 1.5rem 0;
                    }

                    .auth-modal-header {
                        padding: 1.75rem 1.5rem 1.25rem;
                    }

                    .auth-modal-title {
                        font-size: 1.5rem;
                    }

                    .auth-modal-body {
                        padding: 2rem 1.5rem 1.75rem;
                    }

                    .auth-verification-actions {
                        flex-direction: column;
                        align-items: stretch;
                        gap: 1.25rem;
                    }

                    .auth-resend-section {
                        justify-content: center;
                    }

                    .auth-verify-btn {
                        min-width: auto;
                        width: 100%;
                    }
                }

                @media (max-width: 480px) {
                    .modal-dialog {
                        margin: 1rem;
                        max-width: calc(100% - 2rem);
                    }

                    .auth-modal-content {
                        margin: 1rem 0;
                    }

                    .auth-modal-header {
                        padding: 1.5rem 1.25rem 1rem;
                    }

                    .auth-modal-title {
                        font-size: 1.375rem;
                    }

                    .auth-modal-body {
                        padding: 1.75rem 1.25rem 1.5rem;
                    }

                    .auth-form-control {
                        padding: 0.875rem 1rem;
                        font-size: 0.95rem;
                    }

                    .auth-btn-primary,
                    .auth-btn-secondary {
                        padding: 0.875rem 1.25rem;
                        font-size: 1rem;
                    }

                    .auth-verification-header {
                        padding: 1rem;
                        flex-direction: column;
                        align-items: stretch;
                        gap: 1rem;
                        text-align: center;
                    }

                    .separator {
                        margin: 1.5rem 0;
                    }
                }

                @media (max-width: 360px) {
                    .auth-modal-content {
                        margin: 0.5rem 0;
                    }

                    .auth-modal-body {
                        padding: 1.5rem 1rem 1.25rem;
                    }

                    .auth-form-group {
                        margin-bottom: 1.5rem;
                    }
                }
            </style>

            <div class="modal-header auth-modal-header">
                <h5 class="modal-title auth-modal-title" id="customerAuthModalLabel">{{ translate('Customer Login') }}
                </h5>
                <button type="button" class="close auth-modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body auth-modal-body">
                <!-- Mohammad Hassan: Email step (send verification code) -->
                <div id="customerEmailStep" class="active">
                    <form class="form-default" role="form" onsubmit="handleCustomerEmailSubmit(event)">
                        <input type="hidden" name="user_type" value="customer">

                        <div class="auth-form-group">
                            <input type="email" class="form-control auth-form-control"
                                placeholder="{{ translate('Enter your email address') }}" name="email"
                                id="customerEmail" autocomplete="off" required>
                        </div>

                        <div class="auth-info-text">
                            {{ translate('We will send a 6-digit verification code to your email.') }}
                        </div>

                        <div class="auth-form-group">
                            <button type="submit"
                                class="btn auth-btn-primary">{{ translate('Send Verification Code') }}</button>
                        </div>

                        <!-- Mohammad Hassan: Google Login Button -->
                        <div class="separator">
                            <span>{{ translate('OR') }}</span>
                        </div>

                        <div class="auth-form-group">
                            <a href="{{ route('social.login', ['provider' => 'google']) }}"
                                class="btn auth-btn-secondary">
                                <svg class="google-icon" width="18" height="18" viewBox="0 0 48 48">
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
                    </form>
                </div>

                <!-- Mohammad Hassan: Verification step (enter code and login) -->
                <div id="customerVerificationStep" style="display: none;">
                    <form class="form-default" role="form" onsubmit="handleCustomerVerification(event)">
                        <div class="auth-verification-header">
                            <div>
                                <div style="color: #8697C4; font-size: 0.875rem; margin-bottom: 0.25rem;">
                                    {{ translate('Email') }}</div>
                                <div class="auth-email-display" id="customerEmailDisplay"></div>
                            </div>
                            <a href="javascript:void(0)" class="auth-change-link"
                                onclick="goBackToCustomerEmail()">{{ translate('Change') }}</a>
                        </div>

                        <div class="auth-form-group">
                            <input type="text" class="form-control auth-form-control"
                                placeholder="{{ translate('Enter 6-digit code') }}" id="customerVerificationCode"
                                maxlength="6" autocomplete="one-time-code" required>
                        </div>

                        <div class="auth-verification-actions">
                            <div class="auth-resend-section">
                                <button type="button" id="customerResendBtn" class="auth-resend-btn"
                                    onclick="resendCustomerVerificationCode()"
                                    disabled>{{ translate('Resend Code') }}</button>
                                <span id="customerResendTimer" class="auth-timer"></span>
                            </div>
                            <button type="submit"
                                class="btn auth-btn-primary auth-verify-btn">{{ translate('Verify & Login') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Mohammad Hassan - All existing JavaScript functionality remains unchanged
    function openCustomerLogin() {
        document.getElementById('customerEmailStep').style.display = 'block';
        document.getElementById('customerVerificationStep').style.display = 'none';
        const emailInput = document.getElementById('customerEmail');
        const codeInput = document.getElementById('customerVerificationCode');
        if (emailInput) emailInput.value = '';
        if (codeInput) codeInput.value = '';
        resetCustomerResendState();
        $('#customerAuthModal').modal('show');
    }

    function handleCustomerEmailSubmit(event) {
        event.preventDefault();
        const email = document.getElementById('customerEmail').value.trim();
        if (!email) return;

        const submitBtn = event.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = '{{ translate('Sending...') }}';
        submitBtn.disabled = true;

        fetch('/api/v2/auth/user-email-submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'System-Key': '{{ config('app.system_key') }}',
                },
                body: JSON.stringify({
                    email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.result) {
                    document.getElementById('customerEmailStep').style.display = 'none';
                    document.getElementById('customerVerificationStep').style.display = 'block';
                    document.getElementById('customerEmailDisplay').textContent = email;
                    startCustomerResendCountdown();
                } else {
                    const message = Array.isArray(data.message) ? data.message[0] : data.message;
                    alert(message || '{{ translate('Error sending verification code') }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ translate('Network error. Please try again.') }}');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
    }

    function handleCustomerVerification(event) {
        event.preventDefault();
        const email = document.getElementById('customerEmail').value.trim();
        const code = document.getElementById('customerVerificationCode').value.trim();
        if (!email || !code) return;

        const submitBtn = event.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = '{{ translate('Verifying...') }}';
        submitBtn.disabled = true;

        fetch('/api/v2/auth/user-verify-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'System-Key': '{{ config('app.system_key') }}',
                },
                body: JSON.stringify({
                    email: email,
                    code: code
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.result) {
                    if (data.access_token) {
                        localStorage.setItem('auth_token', data.access_token);
                    }
                    if (data.user) {
                        localStorage.setItem('user', JSON.stringify(data.user));
                    }
                    alert('{{ translate('Email verified successfully! You are now logged in.') }}');
                    $('#customerAuthModal').modal('hide');
                    window.location.reload();
                } else {
                    const message = Array.isArray(data.message) ? data.message[0] : data.message;
                    alert(message || '{{ translate('Invalid verification code') }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ translate('Network error. Please try again.') }}');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
    }

    function resendCustomerVerificationCode() {
        const btn = document.getElementById('customerResendBtn');
        if (btn && btn.disabled) return;
        const email = document.getElementById('customerEmail').value.trim();
        if (!email) return;

        btn.disabled = true;

        fetch('/api/v2/auth/user-resend-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'System-Key': '{{ config('app.system_key') }}',
                },
                body: JSON.stringify({
                    email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.result) {
                    alert('{{ translate('Verification code resent successfully!') }}');
                    startCustomerResendCountdown();
                } else {
                    const message = Array.isArray(data.message) ? data.message[0] : data.message;
                    alert(message || '{{ translate('Error resending code') }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ translate('Network error. Please try again.') }}');
            });
    }

    function goBackToCustomerEmail() {
        document.getElementById('customerVerificationStep').style.display = 'none';
        document.getElementById('customerEmailStep').style.display = 'block';
        resetCustomerResendState();
    }

    let customerResendInterval = null;
    const CUSTOMER_RESEND_SECONDS = 60;

    function startCustomerResendCountdown(seconds = CUSTOMER_RESEND_SECONDS) {
        const btn = document.getElementById('customerResendBtn');
        const timerEl = document.getElementById('customerResendTimer');
        if (!btn || !timerEl) return;

        btn.disabled = true;
        let remaining = seconds;
        timerEl.textContent = `${remaining}s`;

        clearInterval(customerResendInterval);
        customerResendInterval = setInterval(() => {
            remaining -= 1;
            timerEl.textContent = `${remaining}s`;
            if (remaining <= 0) {
                clearInterval(customerResendInterval);
                customerResendInterval = null;
                btn.disabled = false;
                timerEl.textContent = '';
            }
        }, 1000);
    }

    function resetCustomerResendState() {
        const btn = document.getElementById('customerResendBtn');
        const timerEl = document.getElementById('customerResendTimer');
        if (btn) btn.disabled = true;
        if (timerEl) timerEl.textContent = '';
        if (customerResendInterval) {
            clearInterval(customerResendInterval);
            customerResendInterval = null;
        }
    }
</script>

