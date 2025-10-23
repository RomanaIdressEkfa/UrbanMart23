{{-- All JavaScript content for product details page --}}
<script type="text/javascript">
    $(document).ready(function() {
        getVariantPrice();
    });

    function CopyToClipboard(e) {
        var url = $(e).data('url');
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(url).select();
        try {
            document.execCommand("copy");
            AIZ.plugins.notify('success', '{{ translate('Link copied to clipboard') }}');
        } catch (err) {
            AIZ.plugins.notify('danger', '{{ translate('Oops, unable to copy') }}');
        }
        $temp.remove();
    }

    function show_chat_modal() {
        @if (Auth::check())
            $('#chat_modal').modal('show');
        @else
            // Mohammad Hassan
$('#customerAuthModal').modal('show');
        @endif
    }

    // Pagination using ajax
    // Note: If you want pagination within AJAX loaded content to also be AJAX,
    // ensure these handlers are rebound or use event delegation.
    // The current $(document).on('click', ...) approach is good for delegation.
    $(window).on('hashchange', function() {
        if(window.history.pushState) {
            window.history.pushState('', '/', window.location.pathname);
        } else {
            window.location.hash = '';
        }
    });

    $(document).ready(function() {
        $(document).on('click', '.product-queries-pagination .pagination a', function(e) {
            getPaginateData($(this).attr('href').split('page=')[1], 'query', 'queries-area');
            e.preventDefault();
        });
    });

    $(document).ready(function() {
        $(document).on('click', '.product-reviews-pagination .pagination a', function(e) {
            getPaginateData($(this).attr('href').split('page=')[1], 'review', 'reviews-area');
            e.preventDefault();
        });
    });

    function getPaginateData(page, type, section) {
        $.ajax({
            url: '?page=' + page,
            dataType: 'json',
            data: {type: type},
        }).done(function(data) {
            $('.'+section).html(data);
            location.hash = page;
        }).fail(function() {
            alert('Something went worng! Data could not be loaded.');
        });
    }
    // Pagination end

    function showImage(photo) {
        $('#image_modal img').attr('src', photo);
        $('#image_modal img').attr('data-src', photo);
        $('#image_modal').modal('show');
    }

    function bid_modal(){
        @if (isCustomer() || isSeller())
            $('#bid_for_detail_product').modal('show');
        @elseif (isAdmin())
            AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers & Sellers can Bid.") }}');
        @else
            // Mohammad Hassan
                $('#customerAuthModal').modal('show');
        @endif
    }

    function product_review(product_id) {
        @if (isCustomer())
            @if ($review_status == 1)
                $.post('{{ route('product_review_modal') }}', {
                    _token: '{{ @csrf_token() }}',
                    product_id: product_id
                }, function(data) {
                    $('#product-review-modal-content').html(data);
                    $('#product-review-modal').modal('show', {
                        backdrop: 'static'
                    });
                    AIZ.extra.inputRating();
                });
            @else
                AIZ.plugins.notify('warning', '{{ translate("Sorry, You need to buy this product to give review.") }}');
            @endif
        @elseif (Auth::check() && !isCustomer())
            AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers can give review.") }}');
        @else
            // Mohammad Hassan
                $('#customerAuthModal').modal('show');
        @endif
    }

    function showSizeChartDetail(id, name){
        $('#size-chart-show-modal .modal-title').html('');
        $('#size-chart-show-modal .modal-body').html('');
        if (id == 0) {
            AIZ.plugins.notify('warning', '{{ translate("Sorry, There is no size guide found for this product.") }}');
            return false;
        }
        $.ajax({
            type: "GET",
            url: "{{ route('size-charts-show', '') }}/"+id,
            data: {},
            success: function(data) {
                $('#size-chart-show-modal .modal-title').html(name);
                $('#size-chart-show-modal .modal-body').html(data);
                $('#size-chart-show-modal').modal('show');
            }
        });
    }

    // Mohammad Hassan
    function showLoginModal() {
        showUserTypeModal();
    }
</script>

