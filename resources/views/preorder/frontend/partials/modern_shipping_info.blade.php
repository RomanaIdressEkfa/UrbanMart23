<!-- Modern Shipping Information Design for Preorder -->
<div class="shipping-information">
    <!-- Delivery/Pickup Options -->
    <div class="delivery-options mb-4">
        <div class="row">
            <div class="col-md-6">
                <div class="delivery-option active" data-option="delivery">
                    <div class="d-flex align-items-center p-3 border rounded">
                        <div class="delivery-icon mr-3">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 7H16V6C16 4.9 15.1 4 14 4H10C8.9 4 8 4.9 8 6V7H5C3.9 7 3 7.9 3 9V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V9C21 7.9 20.1 7 19 7ZM10 6H14V7H10V6ZM19 19H5V9H19V19Z" fill="#4F46E5"/>
                            </svg>
                        </div>
                        <div>
                            <h6 class="mb-0 text-primary">{{ translate('Delivery') }}</h6>
                            <small class="text-muted">{{ translate('Deliver to your address') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="delivery-option" data-option="pickup">
                    <div class="d-flex align-items-center p-3 border rounded">
                        <div class="delivery-icon mr-3">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22S19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9S10.62 6.5 12 6.5S14.5 7.62 14.5 9S13.38 11.5 12 11.5Z" fill="#6B7280"/>
                            </svg>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ translate('Pick up') }}</h6>
                            <small class="text-muted">{{ translate('Pick up from store') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipping Form -->
    <div class="shipping-form">
        <!-- Full Name -->
        <div class="form-group mb-3">
            <label class="form-label">{{ translate('Full name') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-lg" name="name"
                   value="{{ auth()->check() ? auth()->user()->name : old('name') }}"
                   placeholder="{{ translate('Enter full name') }}" required>
        </div>

        <!-- Email Address -->
        <div class="form-group mb-3">
            <label class="form-label">{{ translate('Email address') }} <span class="text-danger">*</span></label>
            <input type="email" class="form-control form-control-lg" name="email"
                   value="{{ auth()->check() ? auth()->user()->email : old('email') }}"
                   placeholder="{{ translate('Enter email address') }}" required>
        </div>

        <!-- Phone Number -->
        <div class="form-group mb-3">
            <label class="form-label">{{ translate('Phone number') }} <span class="text-danger">*</span></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <img src="https://flagcdn.com/w20/bd.png" alt="BD" class="mr-2" style="width: 20px;">
                        +880
                    </span>
                </div>
                <input type="tel" class="form-control form-control-lg" name="phone"
                       value="{{ auth()->check() ? auth()->user()->phone : old('phone') }}"
                       placeholder="{{ translate('Enter 11 digit phone number') }}" pattern="[0-9]{11}" maxlength="11" required>
            </div>
            <small class="text-muted">{{ translate('Enter 11 digit Bangladesh mobile number (e.g., 01712345678)') }}</small>
        </div>

        <!-- City -->
        <div class="form-group mb-3">
            <label class="form-label">{{ translate('City') }} <span class="text-danger">*</span></label>
            <select class="aiz-selectpicker form-control" data-live-search="true" name="city_id" id="preorder_city" required>
                <option value="">{{ translate('Choose city') }}</option>
                <!-- Cities will be loaded via JavaScript -->
            </select>
        </div>



        <!-- Address (for delivery only) -->
        <div class="form-group mb-3" id="address_field">
            <label class="form-label">{{ translate('Address') }} <span class="text-danger">*</span></label>
            <textarea class="form-control form-control-lg" name="address" rows="3" placeholder="{{ translate('Enter address') }}" required></textarea>
        </div>

        <!-- Pickup Point Selection (for pickup only) -->
        <div class="form-group mb-3 d-none" id="pickup_point_field">
            <label class="form-label">{{ translate('Select Pickup Point') }} <span class="text-danger">*</span></label>
            <select class="form-control form-control-lg" name="pickup_point_id" id="pickup_point_select">
                <option value="">{{ translate('Choose pickup point') }}</option>
                <!-- Pickup points will be loaded via JavaScript -->
            </select>
        </div>
    </div>

    <!-- Terms and Conditions -->
    <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" id="terms_conditions" required>
        <label class="form-check-label" for="terms_conditions">
            {{ translate('I have read and agree to the') }}
            <a href="#" class="text-primary">{{ translate('Terms and Conditions') }}</a>
        </label>
    </div>

    <!-- Hidden Fields -->
    <input type="hidden" name="country_id" value="18"> <!-- Bangladesh -->
    <input type="hidden" name="delivery_type" value="delivery" id="delivery_type">
</div>

<style>
.shipping-information {
    background: #fff;
    border-radius: 8px;
}

.delivery-options .delivery-option {
    cursor: pointer;
    transition: all 0.3s ease;
}

.delivery-options .delivery-option:hover {
    transform: translateY(-2px);
}

.delivery-options .delivery-option.active .border {
    border-color: #4F46E5 !important;
    background-color: #F8FAFF;
}

.delivery-options .delivery-option.active .text-primary {
    color: #4F46E5 !important;
}

.delivery-options .delivery-option:not(.active) .delivery-icon svg path {
    fill: #6B7280;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

/* Shipping form typography & sizing tweaks */
.shipping-information .form-label,
.shipping-information .form-control,
.shipping-information .bootstrap-select .btn {
    font-family: Jost, sans-serif !important;
}

.shipping-information .form-control,
.shipping-information .bootstrap-select .btn {
    /* padding: 10px 12px; */
    /* border: 1px solid #D1D5DB; */
    border-radius: 6px;
    font-size: 14px;
}

.shipping-information .input-group-text {
    font-family: Jost, sans-serif !important;
    padding: 10px 12px;
    font-size: 14px;
}

.shipping-information .form-control:focus,
.shipping-information .bootstrap-select .btn:focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.12);
}

.input-group-text {
    background-color: #F9FAFB;
    border: 1px solid #D1D5DB;
    border-right: none;
}

.form-check-input:checked {
    background-color: #4F46E5;
    border-color: #4F46E5;
}

.text-primary {
    color: #4F46E5 !important;
}

/* Adjust bootstrap-select dropdown sizing */
.shipping-information .bootstrap-select .dropdown-menu .inner .dropdown-item {
    font-size: 14px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load Bangladesh cities
    loadBangladeshCities();

    // Wire city change to update order summary via AJAX
    const citySelectEl = document.getElementById('preorder_city');
    if (citySelectEl) {
        citySelectEl.addEventListener('change', function() {
            const cityId = this.value;
            const countryId = 18; // Bangladesh default

            if (!cityId) { return; }

            // Show loading state in summary container
            const summaryEl = document.getElementById('order_summary_ajax');
            if (summaryEl) {
                summaryEl.innerHTML = '<div class="text-center py-3">{{ translate('Updating totals...') }}</div>';
            }

            // Prepare payload similar to updateDeliveryAddress expectations
            const payload = {
                address_id: countryId, // For guests, controller treats address_id as country_id
                city_id: cityId
            };

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('updateDeliveryAddress') }}",
                type: 'POST',
                data: payload,
                success: function (res) {
                    try {
                        // Update the cart summary without page reload
                        if (res && res.cart_summary) {
                            if (summaryEl) {
                                summaryEl.innerHTML = res.cart_summary;
                            }
                        } else {
                            // Try to update the preorder summary section
                            const preorderSummaryContainer = document.querySelector('.preorder-summary-container');
                            if (preorderSummaryContainer && res.preorder_summary) {
                                preorderSummaryContainer.innerHTML = res.preorder_summary;
                            }
                        }
                        if (typeof ensurePaymentVisible === 'function') { ensurePaymentVisible(); }
                    } catch (e) {
                        if (summaryEl) {
                            summaryEl.innerHTML = '<div class="text-danger">{{ translate('Error parsing response') }}</div>';
                        }
                    }
                },
                error: function(xhr) {
                    if (summaryEl) {
                        summaryEl.innerHTML = '<div class="text-danger">{{ translate('Error updating shipping/totals') }}</div>';
                    }
                }
            });
        });
    }

    // Function to toggle delivery/pickup fields
    function toggleDeliveryFields(deliveryType) {
        const addressField = document.getElementById('address_field');
        const pickupPointField = document.getElementById('pickup_point_field');
        const addressTextarea = addressField.querySelector('textarea[name="address"]');
        const pickupSelect = pickupPointField.querySelector('select[name="pickup_point_id"]');

        if (deliveryType === 'pickup') {
            // Show pickup point field, hide address field
            addressField.classList.add('d-none');
            pickupPointField.classList.remove('d-none');

            // Remove required from address, add to pickup point
            addressTextarea.removeAttribute('required');
            pickupSelect.setAttribute('required', 'required');

            // Load pickup points
            loadPickupPoints();
        } else {
            // Show address field, hide pickup point field
            addressField.classList.remove('d-none');
            pickupPointField.classList.add('d-none');

            // Add required to address, remove from pickup point
            addressTextarea.setAttribute('required', 'required');
            pickupSelect.removeAttribute('required');
        }
    }

    // Function to load pickup points
    function loadPickupPoints() {
        const pickupSelect = document.getElementById('pickup_point_select');

        // Clear existing options except the first one
        pickupSelect.innerHTML = '<option value="">{{ translate("Choose pickup point") }}</option>';

        // Add loading option
        pickupSelect.innerHTML += '<option value="">{{ translate("Loading...") }}</option>';

        // Make AJAX request to get pickup points
        fetch('/pickup-points')
            .then(response => response.json())
            .then(data => {
                // Clear loading option
                pickupSelect.innerHTML = '<option value="">{{ translate("Choose pickup point") }}</option>';

                // Add pickup points
                if (data.pickup_points && data.pickup_points.length > 0) {
                    data.pickup_points.forEach(point => {
                        const option = document.createElement('option');
                        option.value = point.id;
                        option.textContent = point.name + ' - ' + point.address;
                        pickupSelect.appendChild(option);
                    });
                } else {
                    pickupSelect.innerHTML += '<option value="">{{ translate("No pickup points available") }}</option>';
                }
            })
            .catch(error => {
                console.error('Error loading pickup points:', error);
                pickupSelect.innerHTML = '<option value="">{{ translate("Error loading pickup points") }}</option>';
            });
    }

    // Handle delivery option selection
    document.querySelectorAll('.delivery-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            document.querySelectorAll('.delivery-option').forEach(opt => opt.classList.remove('active'));

            // Add active class to clicked option
            this.classList.add('active');

            // Update hidden field
            const deliveryType = this.getAttribute('data-option');
            document.getElementById('delivery_type').value = deliveryType;

            // Show/hide appropriate fields
            toggleDeliveryFields(deliveryType);
        });
    });

    // Phone number validation for Bangladesh
    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Remove any non-digit characters
            let value = e.target.value.replace(/\D/g, '');

            // Limit to 11 digits
            if (value.length > 11) {
                value = value.substring(0, 11);
            }

            e.target.value = value;

            // Validate Bangladesh mobile number format
            if (value.length === 11 && value.startsWith('01')) {
                e.target.setCustomValidity('');
            } else if (value.length > 0) {
                e.target.setCustomValidity('Please enter a valid 11-digit Bangladesh mobile number starting with 01');
            } else {
                e.target.setCustomValidity('');
            }
        });

        phoneInput.addEventListener('blur', function(e) {
            const value = e.target.value;
            if (value.length > 0 && (value.length !== 11 || !value.startsWith('01'))) {
                e.target.setCustomValidity('Please enter a valid 11-digit Bangladesh mobile number starting with 01');
                e.target.reportValidity();
            }
        });
    }
});

function loadBangladeshCities() {
    const citySelect = document.getElementById('preorder_city');

    // Clear existing options
    citySelect.innerHTML = '<option value="">{{ translate("Choose city") }}</option>';

    // Fetch cities for Bangladesh (country_id = 18) using the existing route
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('get-city-by-country') }}",
        type: 'POST',
        data: {
            country_id: 18 // Bangladesh
        },
        success: function (response) {
            var htmlOptions = '';
            try {
                var parsed = response;
                if (typeof parsed === 'string') {
                    parsed = JSON.parse(parsed);
                }
                if (typeof parsed === 'string') {
                    htmlOptions = parsed;
                } else if (Array.isArray(parsed)) {
                    htmlOptions = parsed.map(function(city){
                        return '<option value="' + (city.id || '') + '">' + (city.name || '') + '</option>';
                    }).join('');
                } else if (parsed && parsed.html) {
                    htmlOptions = parsed.html;
                } else {
                    htmlOptions = response;
                }
            } catch (e) {
                htmlOptions = response;
            }
            citySelect.innerHTML = htmlOptions;
            if (AIZ && AIZ.plugins && typeof AIZ.plugins.bootstrapSelect === 'function') {
                AIZ.plugins.bootstrapSelect('refresh');
                // Ensure payment section remains visible after city list refresh
                if (typeof ensurePaymentVisible === 'function') {
                    ensurePaymentVisible();
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading cities:', error);
            loadFallbackCities();
        }
    });
}

function loadFallbackCities() {
    const citySelect = document.getElementById('preorder_city');
    const commonCities = [
        {id: '', name: '{{ translate("Choose city") }}'},
        {id: 1, name: 'Dhaka'},
        {id: 2, name: 'Chittagong'},
        {id: 3, name: 'Sylhet'},
        {id: 4, name: 'Rajshahi'},
        {id: 5, name: 'Khulna'},
        {id: 6, name: 'Barisal'},
        {id: 7, name: 'Rangpur'},
        {id: 8, name: 'Mymensingh'}
    ];

    citySelect.innerHTML = '';
    commonCities.forEach(city => {
        const option = document.createElement('option');
        option.value = city.id;
        option.textContent = city.name;
        citySelect.appendChild(option);
    });
    if (AIZ && AIZ.plugins && typeof AIZ.plugins.bootstrapSelect === 'function') {
        AIZ.plugins.bootstrapSelect('refresh');
        // Ensure payment section remains visible after fallback city load
        if (typeof ensurePaymentVisible === 'function') {
            ensurePaymentVisible();
        }
    }
}


</script>

