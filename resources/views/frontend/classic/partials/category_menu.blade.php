<aside class="left-sidebar">
    @php
        // কন্ট্রোলার থেকে আসা ডাটার উপর ভিত্তি করে আমরা ঠিক করছি এটা সাব-ক্যাটাগরি পেইজ কিনা।
        $isSubcategoryPage = isset($parentCategory) && $parentCategory != null;
    @endphp

    <!-- Main Categories View -->
    <div id="main-categories" class="category-view" style="{{ $isSubcategoryPage ? 'display: none;' : 'display: block;' }}">
        <div class="category-grid">
            @foreach (get_level_zero_categories()->take(20) as $key => $cat)
                @php
                    $category_name = $cat->getTranslation('name');
                    $category_icon_src = isset($cat->catIcon->file_name) ? my_asset($cat->catIcon->file_name) : static_asset('assets/img/placeholder.jpg');
                    $product_count = $cat->products()->count();
                    $has_subcategories = $cat->childrenCategories()->count() > 0;
                @endphp
                <div class="category-item js-category-item"
                     data-category-id="{{ $cat->id }}"
                     data-category-slug="{{ $cat->slug }}"
                     data-category-name="{{ $category_name }}"
                     data-has-subcategories="{{ $has_subcategories ? 'true' : 'false' }}">

                    <div class="category-icon">
                        <img class="cat-image lazyload"
                             src="{{ static_asset('assets/img/placeholder.jpg') }}"
                             data-src="{{ $category_icon_src }}"
                             width="24"
                             alt="{{ $category_name }}"
                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                    </div>
                    <div class="category-name">{{ $category_name }}</div>
                    <div class="category-count">{{ $product_count }} Items</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Subcategories View -->
    <div id="subcategories-view" class="category-view" style="{{ $isSubcategoryPage ? 'display: block;' : 'display: none;' }}">
        <div class="subcategory-header">
            <button class="back-button" onclick="goBackToMainCategories()">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h3 class="sidebar-title subcategory-title" id="subcategory-title">
                @if($isSubcategoryPage)
                    {{ $parentCategory->getTranslation('name') }}
                @endif
            </h3>
        </div>
        <div class="subcategory-list" id="subcategory-list">
            @if ($isSubcategoryPage && count($siblingSubcategories) > 0)
                @foreach ($siblingSubcategories as $subcat)
                    <a href="{{ route('products.category', $subcat->slug) }}" class="subcategory-item {{ optional($category)->slug == $subcat->slug ? 'active' : '' }}">
                        <span class="subcategory-name">{{ $subcat->getTranslation('name') }}</span>
                        <span class="subcategory-count">{{ $subcat->products()->count() }} Items</span>
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</aside>

<style>
    .cat-image{
        width: 56px;
    }
    .left-sidebar {
        width: 320px;
        background: white;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 70px;
        height: calc(100vh - 80px);
        padding: 20px;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .left-sidebar::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }
    .sidebar-title {
        font-size: 16px;
        font-weight: bold;
        color: var(--skybuy-blue);
        text-align: center;
    }
    .category-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    .category-item {
        display: flex;
        flex-direction: column;
        text-decoration: none;
        align-items: center;
        padding: 10px 10px;
        cursor: pointer;
        transition: all 0.3s;
        color: #333;
        border-radius: 12px;
        background: #f3f7fa;
        position: relative;
    }
    .category-item:hover {
        background: #e8f4f8;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .category-icon {
        font-size: 24px;
        margin-bottom: 8px;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .category-name {
        font-size: 13px;
        text-align: center;
        font-weight: 600;
    }
    .category-count {
        font-size: 11px;
        text-align: center;
        color: #666;
        font-weight: 500;
        margin-top: 2px;
    }
    .category-view {
        transition: all 0.3s ease-in-out;
    }
    .subcategory-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e0e0e0;
    }
    .back-button {
        background: none;
        border: none;
        color: var(--skybuy-blue);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        padding: 8px 12px;
        border-radius: 8px;
        transition: all 0.3s;
        margin-right: 15px;
    }
    .back-button:hover {
        background: #e8f4f8;
    }
    .subcategory-title {
        margin: 0;
        font-size: 18px;
        color: var(--skybuy-blue);
    }
    .subcategory-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .subcategory-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        transition: all 0.3s;
        border-left: 3px solid transparent;
    }
    .subcategory-item:hover {
        background: #e8f4f8;
        border-left-color: var(--skybuy-blue);
        text-decoration: none;
        color: var(--skybuy-blue);
    }
    .subcategory-item.active {
        background: #e8f4f8;
        border-left-color: var(--skybuy-blue);
        color: var(--skybuy-blue);
        font-weight: bold;
    }
    .subcategory-item .subcategory-name {
        font-size: 14px;
        font-weight: 700;
        flex: 1;
    }
    .subcategory-item .subcategory-count {
        font-size: 12px;
        color: #666;
        background: #e9ecef;
        padding: 2px 8px;
        border-radius: 12px;
    }
    .loading {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
        color: #666;
    }
    .loading i {
        margin-right: 8px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categoryItems = document.querySelectorAll('.js-category-item');

    categoryItems.forEach(item => {
        item.addEventListener('click', function (event) {
            event.preventDefault();
            const categoryId = this.dataset.categoryId;
            const categorySlug = this.dataset.categorySlug;
            const categoryName = this.dataset.categoryName;
            const hasSubcategories = this.dataset.hasSubcategories === 'true';
            handleCategoryClick(categoryId, categorySlug, categoryName, hasSubcategories);
        });
    });
});

function handleCategoryClick(categoryId, categorySlug, categoryName, hasSubcategories) {
    if (hasSubcategories) {
        showSubcategories(categoryId, categoryName);
    } else {
        window.location.href = "{{ url('/category') }}/" + categorySlug;
    }
}

function showSubcategories(categoryId, categoryName) {
    document.getElementById('subcategory-title').textContent = categoryName;
    const subcategoryList = document.getElementById('subcategory-list');
    subcategoryList.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

    document.getElementById('main-categories').style.display = 'none';
    document.getElementById('subcategories-view').style.display = 'block';

    fetch('{{ route("category.subcategories") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ category_id: categoryId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.subcategories) {
            renderSubcategories(data.subcategories);
        } else {
            subcategoryList.innerHTML = '<div class="loading">No subcategories found</div>';
        }
    })
    .catch(error => console.error('Error:', error));
}

function renderSubcategories(subcategories) {
    const subcategoryList = document.getElementById('subcategory-list');
    if (subcategories.length === 0) {
        subcategoryList.innerHTML = '<div class="loading">No subcategories found</div>';
        return;
    }
    const baseUrl = "{{ url('/category') }}";
    let html = '';
    subcategories.forEach(subcategory => {
        html += `
            <a href="${baseUrl}/${subcategory.slug}" class="subcategory-item">
                <span class="subcategory-name">${subcategory.name}</span>
                <span class="subcategory-count">${subcategory.product_count} Items</span>
            </a>
        `;
    });
    subcategoryList.innerHTML = html;
}

function goBackToMainCategories() {
    document.getElementById('subcategories-view').style.display = 'none';
    document.getElementById('main-categories').style.display = 'block';
}
</script>