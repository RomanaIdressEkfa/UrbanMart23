@extends('frontend.layouts.app')

@section('content')
    <!-- Modern Breadcrumb Section -->
    <section class="modern-breadcrumb-section">
        <div class="container">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb-content">
                    <h1 class="page-title">{{ translate('All Categories') }}</h1>
                    <p class="page-subtitle">{{ translate('Discover all product categories') }}</p>
                </div>
                <nav class="breadcrumb-nav">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">
                                <i class="las la-home"></i>
                                {{ translate('Home') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ translate('All Categories') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <!-- Modern Categories Grid -->
    <section class="modern-categories-section">
        <div class="container">
            <div class="categories-container">
                @foreach ($categories as $key => $category)
                    <div class="category-card-modern">
                        <!-- Category Header -->
                        <div class="category-header">
                            <div class="category-image-wrapper">
                                <img src="{{ uploaded_asset($category->banner) }}"
                                    alt="{{ $category->getTranslation('name') }}" class="category-image"
                                    onerror="this.src='{{ static_asset('assets/img/placeholder.jpg') }}'">
                            </div>
                            <div class="category-info">
                                <h2 class="category-title">
                                    <a href="{{ route('products.category', $category->slug) }}">
                                        {{ $category->getTranslation('name') }}
                                    </a>
                                </h2>
                                <span class="category-count">{{ $category->childrenCategories->count() }}
                                    {{ translate('subcategories') }}</span>
                            </div>
                            <div class="category-arrow">
                                <i class="las la-angle-right"></i>
                            </div>
                        </div>

                        <!-- Subcategories Grid -->
                        @if ($category->childrenCategories->count() > 0)
                            <div class="subcategories-grid">
                                @foreach ($category->childrenCategories as $key => $child_category)
                                    <div class="subcategory-item">
                                        <div class="subcategory-header">
                                            <h3 class="subcategory-title">
                                                <a href="{{ route('products.category', $child_category->slug) }}">
                                                    {{ $child_category->getTranslation('name') }}
                                                </a>
                                            </h3>
                                            @if ($child_category->childrenCategories->count() > 0)
                                                <span
                                                    class="subcategory-count">{{ $child_category->childrenCategories->count() }}</span>
                                            @endif
                                        </div>

                                        <!-- Sub-subcategories -->
                                        @if ($child_category->childrenCategories->count() > 0)
                                            <ul
                                                class="sub-subcategories-list @if ($child_category->childrenCategories->count() > 5) collapsed @endif">
                                                @foreach ($child_category->childrenCategories as $key => $second_level_category)
                                                    <li class="sub-subcategory-item">
                                                        <a
                                                            href="{{ route('products.category', $second_level_category->slug) }}">
                                                            {{ $second_level_category->getTranslation('name') }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            @if ($child_category->childrenCategories->count() > 5)
                                                <button class="toggle-subcategories-btn"
                                                    onclick="toggleSubcategories(this)">
                                                    <span class="btn-text">{{ translate('Show More') }}</span>
                                                    <i class="las la-angle-down btn-icon"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <style>
        /* Client Brand Colors */
        :root {
            --primary-color: #3D52A0;
            --primary-light: #7091E6;
            --primary-lighter: #8697C4;
            --primary-lightest: #ADBBDA;
            --background-light: #EDE8F5;
            --text-dark: #2C3E50;
            --text-medium: #6C7B8A;
            --border-color: #E8ECF0;
            --shadow-light: rgba(61, 82, 160, 0.08);
            --shadow-medium: rgba(61, 82, 160, 0.15);
            --shadow-heavy: rgba(61, 82, 160, 0.25);
        }

        /* Modern Breadcrumb Section */
        .modern-breadcrumb-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            padding: 2rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .modern-breadcrumb-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            transform: rotate(45deg);
        }

        .breadcrumb-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .breadcrumb-content {
            color: white;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .page-subtitle {
            font-size: 1rem;
            margin: 0;
            opacity: 0.9;
        }

        .breadcrumb-nav .breadcrumb {
            background: rgba(255, 255, 255, 0.15);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            margin: 0;
            backdrop-filter: blur(10px);
        }

        .breadcrumb-item {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .breadcrumb-item a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .breadcrumb-item a:hover {
            opacity: 0.8;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
        }

        /* Modern Categories Section */
        .modern-categories-section {
            padding: 2rem 0 4rem 0;
            background: #F8FAFC;
        }

        .categories-container {
            display: grid;
            gap: 2rem;
        }

        /* Category Card */
        .category-card-modern {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px var(--shadow-light);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--border-color);
        }

        .category-card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px var(--shadow-medium);
        }

        /* Category Header */
        .category-header {
            display: flex;
            align-items: center;
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, var(--background-light) 0%, #FFFFFF 100%);
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-header:hover {
            background: linear-gradient(135deg, var(--primary-lightest) 0%, var(--background-light) 100%);
        }

        .category-image-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            overflow: hidden;
            background: white;
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            flex-shrink: 0;
        }

        .category-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .category-header:hover .category-image {
            transform: scale(1.1);
        }

        .category-info {
            flex: 1;
        }

        .category-title {
            margin: 0 0 0.25rem 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .category-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .category-title a:hover {
            color: var(--primary-color);
            text-decoration: none;
        }

        .category-count {
            font-size: 0.875rem;
            color: var(--text-medium);
            font-weight: 500;
        }

        .category-arrow {
            color: var(--primary-lighter);
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .category-header:hover .category-arrow {
            color: var(--primary-color);
            transform: translateX(4px);
        }

        /* Subcategories Grid */
        .subcategories-grid {
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .subcategory-item {
            background: #FAFBFC;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .subcategory-item:hover {
            background: white;
            box-shadow: 0 4px 15px var(--shadow-light);
            transform: translateY(-2px);
        }

        .subcategory-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .subcategory-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .subcategory-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .subcategory-title a:hover {
            color: var(--primary-color);
            text-decoration: none;
        }

        .subcategory-count {
            background: var(--primary-color);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            min-width: 24px;
            text-align: center;
        }

        /* Sub-subcategories List */
        .sub-subcategories-list {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 200px;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .sub-subcategories-list.collapsed {
            max-height: 120px;
        }

        .sub-subcategory-item {
            margin-bottom: 0.5rem;
        }

        .sub-subcategory-item a {
            color: var(--text-medium);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            border-radius: 6px;
            transition: all 0.3s ease;
            position: relative;
        }

        .sub-subcategory-item a::before {
            content: '';
            width: 4px;
            height: 4px;
            background: var(--primary-lighter);
            border-radius: 50%;
            margin-right: 0.75rem;
            transition: all 0.3s ease;
        }

        .sub-subcategory-item a:hover {
            color: var(--primary-color);
            text-decoration: none;
            padding-left: 0.5rem;
        }

        .sub-subcategory-item a:hover::before {
            background: var(--primary-color);
            transform: scale(1.5);
        }

        /* Toggle Button */
        .toggle-subcategories-btn {
            background: none;
            border: 2px dashed var(--primary-lightest);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            justify-content: center;
            margin-top: 1rem;
        }

        .toggle-subcategories-btn:hover {
            background: var(--background-light);
            border-color: var(--primary-color);
            transform: translateY(-1px);
        }

        .btn-icon {
            transition: transform 0.3s ease;
        }

        .toggle-subcategories-btn.expanded .btn-icon {
            transform: rotate(180deg);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .breadcrumb-wrapper {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .subcategories-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 1.5rem;
                padding: 1.5rem;
            }

            .category-header {
                padding: 1.25rem 1.5rem;
            }

            .category-image-wrapper {
                width: 56px;
                height: 56px;
                margin-right: 1rem;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.5rem;
            }

            .page-subtitle {
                font-size: 0.9rem;
            }

            .modern-breadcrumb-section {
                padding: 1.5rem 0;
            }

            .subcategories-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
                padding: 1rem;
            }

            .category-header {
                padding: 1rem 1.25rem;
            }

            .category-image-wrapper {
                width: 48px;
                height: 48px;
            }

            .category-title {
                font-size: 1.125rem;
            }

            .subcategory-item {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            .modern-categories-section {
                padding: 1rem 0 2rem 0;
            }

            .categories-container {
                gap: 1rem;
            }

            .category-card-modern {
                border-radius: 12px;
                margin: 0 0.5rem;
            }

            .breadcrumb-nav .breadcrumb {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .category-header {
                padding: 0.875rem 1rem;
            }

            .subcategories-grid {
                padding: 0.75rem;
            }

            .subcategory-item {
                padding: 0.875rem;
            }

            .sub-subcategories-list.collapsed {
                max-height: 100px;
            }
        }
    </style>

    <script>
        function toggleSubcategories(button) {
            const list = button.previousElementSibling;
            const btnText = button.querySelector('.btn-text');
            const btnIcon = button.querySelector('.btn-icon');

            if (list.classList.contains('collapsed')) {
                list.classList.remove('collapsed');
                btnText.textContent = '{{ translate('Show Less') }}';
                button.classList.add('expanded');
            } else {
                list.classList.add('collapsed');
                btnText.textContent = '{{ translate('Show More') }}';
                button.classList.remove('expanded');
            }
        }

        // Add smooth scroll behavior
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading animation
            const categoryCards = document.querySelectorAll('.category-card-modern');
            categoryCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
@endsection

