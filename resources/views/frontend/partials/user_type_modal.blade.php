{{-- Mohammad Hassan --}}
<div class="modal fade" id="user_type_modal" tabindex="-1" role="dialog" aria-labelledby="userTypeModalLabel"
        aria-hidden="true">
    <div class="modal-dialog modal-dialog-zoom" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">{{ translate('Select Account Type') }}</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    <div class="text-center mb-4">
                        <p class="text-muted">{{ translate('Please select your account type to continue') }}</p>
                    </div>

                    {{-- Mohammad Hassan: Unified login interface --}}
                    <div class="unified-login-container">
                        <div class="login-option customer-option" onclick="triggerCustomerLogin()">
                            <div class="icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="option-details">
                                <h3>{{ translate('Customer Login') }}</h3>
                                <p>{{ translate('Access your account, track orders, and manage your profile') }}</p>
                            </div>
                        </div>

                        <div class="login-option wholesaler-option" onclick="triggerWholesalerLogin()">
                            <div class="icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="option-details">
                                <h3>{{ translate('Wholesaler Login') }}</h3>
                                <p>{{ translate('Get wholesale prices, bulk discounts, and business solutions') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Mohammad Hassan: CSS for unified login interface --}}
<style>
    /* Unified login container */
    .unified-login-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
        max-width: 100%;
    }

    /* Login option styles */
    .login-option {
        display: flex;
        align-items: center;
        padding: 20px;
        border-radius: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        color: white;
    }

    /* Customer option gradient */
    .customer-option {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    /* Wholesaler option gradient */
    .wholesaler-option {
        background: linear-gradient(135deg, #007bff, #6610f2);
    }

    /* Hover effect */
    .login-option:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }

    /* Icon container */
    .login-option .icon-container {
        flex-shrink: 0;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        margin-right: 15px;
    }

    /* SVG icon */
    .login-option .icon-container svg {
        width: 30px;
        height: 30px;
        color: white;
    }

    /* Option details */
    .login-option .option-details {
        flex-grow: 1;
    }

    /* Option title */
    .login-option .option-details h3 {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 5px;
        color: white;
    }

    /* Option description */
    .login-option .option-details p {
        font-size: 13px;
        margin: 0;
        opacity: 0.9;
        color: white;
    }

    /* Active state */
    .login-option:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
    // Mohammad Hassan
    function showUserTypeModal() {
        $('#user_type_modal').modal('show');
    }

    function showLoginOptions() {
        showUserTypeModal();
    }

    // Mohammad Hassan
    function triggerCustomerLogin() {
        $('#user_type_modal').modal('hide');
        setTimeout(function() {
            if (typeof openCustomerLogin === 'function') {
                openCustomerLogin();
            } else {
                $('#customerAuthModal').modal('show');
            }
        }, 300);
    }

    // Mohammad Hassan
    function triggerWholesalerLogin() {
        $('#user_type_modal').modal('hide');
        setTimeout(function() {
            $('#wholesalerAuthModal').modal('show');
        }, 300);
    }
</script>

