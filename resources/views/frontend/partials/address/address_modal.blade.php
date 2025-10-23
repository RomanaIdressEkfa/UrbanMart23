<!-- New Address Modal -->
<div class="modal fade" id="new-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body c-scrollbar-light">
                    <div class="p-3">
                        <!-- Name -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Name')}} <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Full Name')}}" name="name" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label>{{ translate('Phone')}} <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-10">
                                <input type="tel" class="form-control rounded-0" placeholder="01XXXXXXXXX" name="phone" autocomplete="off" required pattern="01[0-9]{9}" maxlength="11" title="Phone number must be 11 digits starting with 01">
                            </div>
                        </div>

                        <!-- City (Dropdown for Bangladesh) -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('City')}} <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="city_id" id="city_id" required>
                                    <option value="">{{ translate('Select City') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Address')}} <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-10">
                                <textarea class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required></textarea>
                            </div>
                        </div>

                        @if (get_active_countries()->count() > 1)
                        <!-- Country -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Country')}}</label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <select class="form-control aiz-selectpicker rounded-0" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="country_id" required>
                                        <option value="">{{ translate('Select your country') }}</option>
                                        @foreach (get_active_countries() as $key => $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @elseif(get_active_countries()->count() == 1)
                        <input type="hidden" name="country_id" value="{{get_active_countries()[0]->id }}">
                        @endif
                        @if (get_setting('has_state') == 1)
                        <!-- State -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('State')}}</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="state_id" required>

                                </select>
                            </div>
                        </div>
                        @endif

                         <!--Area-->
                        <div class="row area-field d-none">
                            <div class="col-md-2">
                                <label>{{ translate('Area')}}</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="area_id">

                                </select>
                            </div>
                        </div>

                        @if (get_setting('google_map') == 1)
                            <!-- Google Map -->
                            <div class="row mt-3 mb-3">
                                <input id="searchInput" class="controls" type="text" placeholder="{{translate('Enter a location')}}">
                                <div id="map"></div>
                                <ul id="geoData">
                                    <li style="display: none;">Full Address: <span id="location"></span></li>
                                    <li style="display: none;">Postal Code: <span id="postal_code"></span></li>
                                    <li style="display: none;">Country: <span id="country"></span></li>
                                    <li style="display: none;">Latitude: <span id="lat"></span></li>
                                    <li style="display: none;">Longitude: <span id="lon"></span></li>
                                </ul>
                            </div>
                            <!-- Longitude -->
                            <div class="row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">{{ translate('Longitude')}}</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="text" class="form-control mb-3 rounded-0" id="longitude" name="longitude" readonly="">
                                </div>
                            </div>
                            <!-- Latitude -->
                            <div class="row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">{{ translate('Latitude')}}</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="text" class="form-control mb-3 rounded-0" id="latitude" name="latitude" readonly="">
                                </div>
                            </div>
                        @endif

                        <!-- Postal code -->
                        {{-- Mohammad Hassan - Hid postal code row --}}
                        <div class="row" style="display: none;">
                            <div class="col-md-2">
                                <label>{{ translate('Postal code')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Postal Code')}}" name="postal_code" value="" style="display: none;">
                            </div>
                        </div>

                        <!-- Save button -->
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary rounded-0 w-150px">{{translate('Save')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Mohammad Hassan - Load Bangladesh cities (country_id = 18) when modal opens
    function loadBangladeshCities() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('get-city-by-country')}}",
            type: 'POST',
            data: {
                country_id: 18 // Bangladesh country ID
            },
            success: function (response) {
                var obj = JSON.parse(response);
                if(obj != '' && $('<select></select>').html(obj).find('option').length > 1) {
                    $('#city_id').html(obj);
                    $('#city_id').attr('disabled', false);
                    AIZ.plugins.bootstrapSelect('refresh');
                } else {
                    $('#city_id').html('<option value="">{{ translate('No cities available') }}</option>');
                    $('#city_id').attr('disabled', true);
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            }
        });
    }

    // Load cities when new address modal opens
    $('#new-address-modal').on('shown.bs.modal', function () {
        loadBangladeshCities();
    });

    // Mohammad Hassan - Debug: log form submission data when ?debug=1
    const params = new URLSearchParams(window.location.search);
    if (params.get('debug') === '1') {
        $('form[action="{{ route('addresses.store') }}"]').on('submit', function(e) {
            try {
                const payload = $(this).serializeArray();
                console.log('Address store submit payload:', payload);
            } catch (err) {
                console.error('Failed to log address submit payload:', err);
            }
        });
    }
});
</script>

<!-- Edit Address Modal -->
<div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Edit Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body c-scrollbar-light" id="edit_modal_body">

            </div>
        </div>
    </div>
</div>

