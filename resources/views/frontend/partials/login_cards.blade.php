{{-- Mohammad Hassan --}}
{{-- Reusable Login Cards Component --}}

@if(auth()->check())
    {{-- Alternative content for logged-in users --}}
    <div class="user-dashboard-cards">
        <!-- Mohammad Hassan -->
        <a href="{{ route('dashboard') }}" class="dashboard-card">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
            </div>
            <h3 class="welcome-title">{{ translate('My Account') }}</h3>
            <p class="welcome-subtitle">{{ translate('Welcome back') }}, {{ auth()->user()->name }}!</p>
        </a>
        <!-- Mohammad Hassan -->
        <div class="dashboard-card quick-actions-card">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2v-6a2 2 0 00-2-2h-2" />
                </svg>
            </div>
            <h3 class="welcome-title">{{ translate('Quick Actions') }}</h3>
            <p class="welcome-subtitle">{{ translate('Manage orders and profile') }}</p>
            <div class="quick-links">
                <a href="{{ route('purchase_history.index') }}" class="quick-link-item">
                    <i class="las la-box"></i> {{ translate('Orders') }}
                </a>
                {{-- <a href="{{ route('wishlists.index') }}" class="quick-link-item">
                    <i class="las la-heart"></i> {{ translate('Wishlist') }}
                </a> --}}
                <a href="{{ route('profile') }}" class="quick-link-item">
                    <i class="las la-user-circle"></i> {{ translate('Profile') }}
                </a>
            </div>
        </div>
    </div>
@else
    {{-- Login cards for non-authenticated users --}}
    <div class="login-cards-container">
    {{-- Mohammad Hassan --}}
    <div class="login-card customer-login" onclick="{{ $userLoginFunction ?? 'openUserLogin' }}()">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20"
                style="width: 80% !important;" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                    clip-rule="evenodd" />
            </svg>
        </div>
        {{-- Mohammad Hassan --}}
        <h3>{{ translate('Customer Login') }}</h3>
        <p>{{ translate('Access your account, track orders, and manage your profile') }}</p>
    </div>
    <div class="login-card wholesaler-login" onclick="{{ $wholesalerLoginFunction ?? 'openWholesalerLogin' }}()">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none"
                viewBox="0 0 24 24" style="width: 80% !important;" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <h3>{{ translate('Wholesaler Login') }}</h3>
        <p>{{ translate('Get wholesale prices, bulk discounts, and business solutions') }}</p>
    </div>
</div>
@endif

{{-- Login Cards Styles --}}
<style>
    /* Mohammad Hassan */
    .user-dashboard-cards {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
        height: 100%;
        max-width: 100%;
    }

    .dashboard-card {
        flex: 1;
        border-radius: 15px;
        padding: 25px 20px;
        text-align: center;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 160px;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #28a745, #20c997);
        text-decoration: none;
        cursor: pointer;
    }

    /* Mohammad Hassan */
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    /* Mohammad Hassan */
    .welcome-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 15px 0 5px;
    }

    /* Mohammad Hassan */
    .welcome-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 10px;
    }

    /* Mohammad Hassan */
    .quick-actions-card {
        background: linear-gradient(135deg, #17a2b8, #0dcaf0);
    }

    /* Mohammad Hassan */
    .quick-links {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
        width: 100%;
    }

    /* Mohammad Hassan */
    .quick-link-item {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    /* Mohammad Hassan */
    .quick-link-item:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }

    .dashboard-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .welcome-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: white;
    }

    .welcome-subtitle {
        font-size: 0.95rem;
        opacity: 0.9;
        margin-bottom: 0;
        color: white;
    }

    .quick-actions-card {
        position: relative;
        background: linear-gradient(135deg, #17a2b8, #0056b3);
    }

    .quick-links {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-top: 15px;
        width: 100%;
    }

    .quick-link-item {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .quick-link-item:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        color: white;
    }

    /* Responsive adjustments */

    @media (max-width: 576px) {
        .welcome-title {
            font-size: 1.3rem;
        }

        .welcome-subtitle {
            font-size: 0.85rem;
        }

        .quick-link-item {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
    }

    @media (max-width: 480px) {
        .dashboard-card {
            padding: 20px 15px;
            min-height: 140px;
        }
    }

    /* High DPI and dark mode support */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .dashboard-card {
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.08);
        }
    }

    @media (prefers-color-scheme: dark) {
        .dashboard-card {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    }

    {{-- Login Cards Container --}}
    .login-cards-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
        height: 100%;
        max-width: 100%;
    }

    {{-- Individual Login Cards --}}
    .login-card {
        flex: 1;
        border-radius: 15px;
        padding: 25px 20px;
        text-align: center;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 160px;
        position: relative;
        overflow: hidden;
    }

    .login-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .login-card:active {
        transform: translateY(-2px) scale(1.01);
    }

    {{-- Card Background Gradients --}}
    {{-- Mohammad Hassan --}}
    .customer-login {
        background: linear-gradient(135deg, #7c71f8, #b116f9);
    }

    .wholesaler-login {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    {{-- Card Content Styles --}}
    .login-card h3 {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 8px;
        margin-top: 10px;
    }

    .login-card p {
        font-size: 14px;
        opacity: 0.9;
        max-width: 280px;
        line-height: 1.4;
        margin-bottom: 0;
    }

    .login-card .icon {
        margin-bottom: 15px;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.15);
        transition: all 0.3s ease;
    }

    .login-card:hover .icon {
        background-color: rgba(255, 255, 255, 0.25);
        transform: scale(1.1);
    }

    {{-- Responsive Design --}}
    @media (max-width: 1200px) {
        .login-cards-container {
            gap: 18px;
        }

        .login-card {
            padding: 22px 18px;
            min-height: 150px;
        }
    }

    @media (max-width: 992px) {
        .welcome-title {
            font-size: 24px;
        }

        .welcome-subtitle {
            font-size: 15px;
        }

        .login-cards-container {
            gap: 16px;
        }

        .login-card {
            padding: 20px 16px;
            min-height: 140px;
        }

        .login-card h3 {
            font-size: 20px;
        }

        .login-card p {
            font-size: 13px;
            max-width: 250px;
        }
    }

    @media (max-width: 768px) {
        .welcome-title {
            font-size: 22px;
        }

        .welcome-subtitle {
            font-size: 14px;
        }

        .login-cards-container {
            flex: auto;
            height: auto;
            gap: 15px;
        }

        .login-card {
            padding: 18px 15px;
            min-height: 130px;
        }

        .login-card h3 {
            font-size: 18px;
            margin-bottom: 6px;
        }

        .login-card p {
            font-size: 12px;
            max-width: 220px;
        }

        .login-card .icon {
            width: 45px;
            height: 45px;
            margin-bottom: 12px;
        }
    }

    @media (max-width: 576px) {
        .welcome-message {
            margin-bottom: 20px;
        }

        .welcome-title {
            font-size: 20px;
        }

        .welcome-subtitle {
            font-size: 13px;
        }

        .login-cards-container {
            gap: 12px;
        }

        .login-card {
            padding: 16px 12px;
            min-height: 120px;
            border-radius: 12px;
        }

        .login-card h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .login-card p {
            font-size: 11px;
            max-width: 200px;
            line-height: 1.3;
        }

        .login-card .icon {
            width: 40px;
            height: 40px;
            margin-bottom: 10px;
        }
    }

    @media (max-width: 480px) {
        .welcome-title {
            font-size: 18px;
        }

        .login-card {
            padding: 14px 10px;
            min-height: 110px;
        }

        .login-card h3 {
            font-size: 15px;
        }

        .login-card p {
            font-size: 10px;
            max-width: 180px;
        }

        .login-card .icon {
            width: 35px;
            height: 35px;
        }
    }

    {{-- High DPI Display Support --}}
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .login-card {
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .login-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
    }

    {{-- Dark Mode Support (if needed) --}}
    @media (prefers-color-scheme: dark) {
        .welcome-title {
            color: #ecf0f1;
        }

        .welcome-subtitle {
            color: #bdc3c7;
        }
    }

/* =================================================================== */
/* MOBILE VIEW: SIDE-BY-SIDE CARDS (আপনার অনুরোধ অনুযায়ী নতুন স্টাইল) */
/* =================================================================== */
@media (max-width: 768px) {
    .login-cards-container,
    .user-dashboard-cards {
        flex-direction: row;
        align-items: stretch;
        gap: 10px;
        margin-top: -7px;
    }

    .login-card,
    .dashboard-card {
        padding: 15px 10px;
        min-height: 152px;
    }

    .login-card h3,
    .welcome-title {
        font-size: 15px;
        margin-bottom: 6px;
    }

    .login-card p,
    .welcome-subtitle {
        font-size: 11px;
        line-height: 1.3;
    }
    
    .quick-link-item {
        font-size: 10px !important;
        padding: 4px 8px !important;
    }

    /* আইকনের আকার এখানে ঠিক করা হয়েছে */
    .login-card .icon,
    .dashboard-card .icon {
        width: 35px;          /* <<<<<<< সঠিক প্রস্থ */
        height: 35px;         /* <<<<<<< সঠিক উচ্চতা */
        margin-bottom: 0px;
    }
    .user-dashboard-cards .dashboard-card .icon {
        width: 350px;          /* <<<<<<< সঠিক প্রস্থ */
        height: 35px;         /* <<<<<<< সঠিক উচ্চতা */
        margin-bottom: 0px;
    }
}
</style>

