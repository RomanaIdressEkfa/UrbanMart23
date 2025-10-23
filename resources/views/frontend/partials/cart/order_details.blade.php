{{-- Mohammad Hassan --}}
@php
$physical = false;
$subtotal = 0;
// Mohammad Hassan - Debug: Let's see what data we're working with
// dd(['product_variation' => $product_variation, 'carts' => $carts, 'owner_id' => $owner_id]);

// Mohammad Hassan - Fixed: Use product variations array to get unique cart items with different variants
$cart_items = [];
if(isset($product_variation) && is_array($product_variation)) {
    // Mohammad Hassan - Use product_variation array which contains unique cart item IDs for each variant
    foreach ($product_variation as $cart_id) {
        foreach($carts as $cart){
            if($cart['id'] == $cart_id && $cart['owner_id'] == $owner_id){
                $cart_items[] = $cart;
                $product = get_single_product($cart['product_id']);
                if ($product->digital == 0) {
                    $physical = true;
                }
                break;
            }
        }
    }
} else {
    // Mohammad Hassan - Fallback: If product_variation is not available, filter carts by owner_id
    foreach($carts as $cart){
        if($cart['owner_id'] == $owner_id){
            $cart_items[] = $cart;
            $product = get_single_product($cart['product_id']);
            if ($product->digital == 0) {
                $physical = true;
            }
        }
    }
}

// Mohammad Hassan - Debug: Check what cart_items we got
// dd(['cart_items' => $cart_items, 'count' => count($cart_items)]);
@endphp

<!-- Order Details Table -->
<div class="mb-4">
    {{-- Mohammad Hassan - Use reusable cart table component with proper cart items --}}
    @include('frontend.partials.cart.cart_table', [
        'carts' => $cart_items,
        'is_checkout' => true,
        'show_actions' => false,
        'show_selection' => false
    ])
</div>

{{-- Mohammad Hassan - JavaScript for quantity controls and price updates --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to format price
    function formatPrice(amount) {
        return '৳' + parseFloat(amount).toFixed(2);
    }

    // Function to update item total and subtotal
    function updatePrices() {
        let subtotal = 0;

        // Update each item total
        document.querySelectorAll('.quantity-input').forEach(function(input) {
            const quantity = parseInt(input.value) || 1;
            const unitPrice = parseFloat(input.dataset.unitPrice) || 0;
            const productId = input.dataset.productId;
            const itemTotal = quantity * unitPrice;

            // Update item total display
            const totalElement = document.querySelector('.item-total[data-product-id="' + productId + '"]');
            if (totalElement) {
                totalElement.textContent = formatPrice(itemTotal);
            }

            subtotal += itemTotal;
        });

        // Update subtotal in order details
        const subtotalElement = document.getElementById('order-details-subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = formatPrice(subtotal);
        }

        // Update subtotal in order summary (cart summary)
        const cartSubtotalElement = document.getElementById('cart-subtotal-amount');
        if (cartSubtotalElement) {
            cartSubtotalElement.textContent = formatPrice(subtotal);
        }

        // Update total in order summary
        updateOrderSummaryTotal(subtotal);
    }

    // Function to update order summary total
    function updateOrderSummaryTotal(subtotal) {
        // Get tax and shipping values
        const taxElement = document.querySelector('.cart-tax td:last-child');
        const shippingElement = document.querySelector('.cart-shipping td:last-child');

        let tax = 0;
        let shipping = 0;

        if (taxElement) {
            const taxText = taxElement.textContent.replace(/[^\d.]/g, '');
            tax = parseFloat(taxText) || 0;
        }

        if (shippingElement) {
            const shippingText = shippingElement.textContent.replace(/[^\d.]/g, '');
            shipping = parseFloat(shippingText) || 0;
        }

        const total = subtotal + tax + shipping;

        // Update total in cart summary
        const totalElement = document.querySelector('#cart-total-amount');
        if (totalElement) {
            totalElement.textContent = formatPrice(total);
        }
    }

    // Plus button click handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.quantity-btn-plus')) {
            const button = e.target.closest('.quantity-btn-plus');
            const productId = button.dataset.productId;
            const input = document.querySelector('.quantity-input[data-product-id="' + productId + '"]');

            if (input) {
                let currentValue = parseInt(input.value) || 1;
                const maxValue = parseInt(input.getAttribute('max')) || 999;

                if (currentValue < maxValue) {
                    input.value = currentValue + 1;
                    updatePrices();
                }
            }
        }
    });

    // Minus button click handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.quantity-btn-minus')) {
            const button = e.target.closest('.quantity-btn-minus');
            const productId = button.dataset.productId;
            const input = document.querySelector('.quantity-input[data-product-id="' + productId + '"]');

            if (input) {
                let currentValue = parseInt(input.value) || 1;
                const minValue = parseInt(input.getAttribute('min')) || 1;

                if (currentValue > minValue) {
                    input.value = currentValue - 1;
                    updatePrices();
                }
            }
        }
    });

    // Input change handler
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            const input = e.target;
            let value = parseInt(input.value) || 1;
            const minValue = parseInt(input.getAttribute('min')) || 1;
            const maxValue = parseInt(input.getAttribute('max')) || 999;

            // Ensure value is within bounds
            if (value < minValue) {
                value = minValue;
                input.value = value;
            } else if (value > maxValue) {
                value = maxValue;
                input.value = value;
            }

            updatePrices();
        }
    });
});
</script>


