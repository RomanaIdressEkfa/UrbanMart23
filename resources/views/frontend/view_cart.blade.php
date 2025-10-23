@extends('frontend.layouts.app')

@section('content')
    <!-- Cart Details -->
    <section class="my-4" id="cart-details">
        @include('frontend.partials.cart.cart_details', ['carts' => $carts])
    </section>

@endsection

@section('script')
    <script type="text/javascript">
        function removeFromCartView(e, key) {
            e.preventDefault();
            removeFromCart(key);
        }

        // Mohammad Hassan
        // Enhanced updateQuantity function with error handling and cart summary refresh
        function updateQuantity(key, element) {
            // Store original value for rollback
            var originalValue = element.getAttribute('data-original-value') || element.value;
            
            // Disable the input temporarily to prevent multiple requests
            element.disabled = true;
            
            $.post('{{ route('cart.updateQuantity') }}', {
                _token: AIZ.data.csrf,
                id: key,
                quantity: element.value
            }, function(data) {
                if (data.status == 1) {
                    // Update original value to new value on success
                    element.setAttribute('data-original-value', element.value);
                    
                    updateNavCart(data.nav_cart_view, data.cart_count);
                    $('#cart-details').html(data.cart_view);
                    
                    // Refresh cart summary to update totals
                    refreshCartSummary();
                    
                    // Re-initialize plus/minus controls for new content
                    AIZ.extra.plusMinus();
                    
                    // Show success message
                    AIZ.plugins.notify('success', '{{ translate('Cart updated successfully') }}');
                } else {
                    // Show error message if quantity update failed
                    if (data.message) {
                        AIZ.plugins.notify('warning', data.message);
                    }
                    // Reset quantity to previous value
                    element.value = originalValue;
                }
            }).fail(function() {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                // Reset quantity to previous value
                element.value = originalValue;
            }).always(function() {
                // Re-enable the input
                element.disabled = false;
            });
        }

        // Mohammad Hassan
        // Function to refresh cart summary with updated totals
        function refreshCartSummary() {
            $.post('{{ route('cart.updateCartStatus') }}', {
                _token: AIZ.data.csrf,
                product_id: []
            }, function(data) {
                // Update cart summary if it exists
                if ($('#cart_summary').length) {
                    $('#cart_summary').html(data);
                }
            });
        }

        // Cart item selection functionality removed

        // Mohammad Hassan
        // Removed auto-opening of Customer Login modal on cart page for guests. Modal opens only when clicking "Proceed to Checkout".

        // coupon apply
        $(document).on("click", "#coupon-apply", function() {
            @if (Auth::check())
                @if(Auth::user()->user_type != 'customer')
                    AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to apply coupon code.') }}");
                    return false;
                @endif

                var data = new FormData($('#apply-coupon-form')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: "{{ route('checkout.apply_coupon_code') }}",
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data, textStatus, jqXHR) {
                        AIZ.plugins.notify(data.response_message.response, data.response_message.message);
                        $("#cart_summary").html(data.html);
                    }
                });
            @else
                // Mohammad Hassan
                // Removed auto-opening of Customer Login modal on cart page for guests while applying coupon.
            @endif
        });

        // coupon remove
        $(document).on("click", "#coupon-remove", function() {
            @if (Auth::check() && Auth::user()->user_type == 'customer')
                var data = new FormData($('#remove-coupon-form')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: "{{ route('checkout.remove_coupon_code') }}",
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data, textStatus, jqXHR) {
                        $("#cart_summary").html(data);
                    }
                });
            @endif
        });

    </script>
@endsection

