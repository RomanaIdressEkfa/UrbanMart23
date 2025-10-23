<form class="form-default" role="form" action="{{ route('addresses.update', $address_data->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="p-3">
        <!-- Name -->
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Name')}} <span class="text-danger">*</span></label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Full Name')}}" name="name" value="{{ $address_data->name ?? '' }}" required>
            </div>
        </div>

        <!-- Phone -->
        <div class="row mb-3">
            <div class="col-md-2">
                <label>{{ translate('Phone')}} <span class="text-danger">*</span></label>
            </div>
            <div class="col-md-10">
                <input type="tel" class="form-control rounded-0" placeholder="01XXXXXXXXX" name="phone" value="{{ $address_data->phone ?? '' }}" autocomplete="off" required pattern="01[0-9]{9}" maxlength="11" title="Phone number must be 11 digits starting with 01">
            </div>
        </div>

        <!-- City (Dropdown for Bangladesh) -->
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('City')}} <span class="text-danger">*</span></label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="city_id" id="edit_city_id" required>
                    <option value="">{{ translate('Select City') }}</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" {{ optional($address_data->city)->id == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Address -->
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Address')}} <span class="text-danger">*</span></label>
            </div>
            <div class="col-md-10">
                <textarea class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required>{{ $address_data->address }}</textarea>
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
                    <select class="form-control aiz-selectpicker rounded-0" data-live-search="true" data-placeholder="{{ translate('Select your country')}}" name="country_id" id="edit_country" required>
                        <option value="">{{ translate('Select your country') }}</option>
                        @foreach (get_active_countries() as $key => $country)
                        <option value="{{ $country->id }}" @if($address_data->country_id == $country->id) selected @endif>
                            {{ $country->name }}
                        </option>
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
                <select class="form-control mb-3 aiz-selectpicker rounded-0" name="state_id" id="edit_state"  data-live-search="true" required>
                <option value="" disabled>{{ translate('Select State') }}</option>
                @foreach ($states as $key => $state)
                        <option value="{{ $state->id }}" @if($address_data->state_id == $state->id) selected @endif>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif


        <div class="row area-field {{ ($areas->count() == 0) ? 'd-none' : '' }}">
            <div class="col-md-2">
                <label>{{ translate('Area')}}</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="area_id">
                    @foreach ($areas as $key => $area)
                        <option value="{{ $area->id }}" @if($address_data->area_id == $area->id) selected @endif>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        @if (get_setting('google_map') == 1)
            <!-- Google Map -->
            <div class="row mt-3 mb-3">
                <input id="edit_searchInput" class="controls" type="text" placeholder="Enter a location">
                <div id="edit_map"></div>
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
                    <input type="text" class="form-control mb-3 rounded-0" id="edit_longitude" name="longitude" value="{{ $address_data->longitude }}" readonly="">
                </div>
            </div>
            <!-- Latitude -->
            <div class="row">
                <div class="col-md-2" id="">
                    <label for="exampleInputuname">{{ translate('Latitude')}}</label>
                </div>
                <div class="col-md-10" id="">
                    <input type="text" class="form-control mb-3 rounded-0" id="edit_latitude" name="latitude" value="{{ $address_data->latitude }}" readonly="">
                </div>
            </div>
        @endif

        <!-- Postal code (Optional) -->
        <div class="row" style="display: none;">
            <div class="col-md-2">
                <label>{{ translate('Postal Code')}} <span class="text-muted">({{ translate('Optional') }})</span></label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Postal Code')}} " value="{{ $address_data->postal_code }}" name="postal_code" style="display: none;">
            </div>
        </div>

        <!-- Save button -->
        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary rounded-0 w-150px">{{translate('Save')}}</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Mohammad Hassan - Updated to load cities by country_id 18 and preserve selected city
        if ($('#edit_city_id').length > 0) {
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
                        $('#edit_city_id').attr('disabled', false);
                        $('#edit_city_id').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                        // Set the selected city
                        var selectedCityId = '{{ $address_data->city_id ?? '' }}';
                        if (selectedCityId) {
                            $('#edit_city_id').val(selectedCityId);
                            AIZ.plugins.bootstrapSelect('refresh');
                        }
                    } else {
                        $('#edit_city_id').html('<option value="">{{ translate('No cities are available.') }}</option>');
                        $('#edit_city_id').attr('disabled', true);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }
    });
</script>

