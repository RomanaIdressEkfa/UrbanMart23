@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">{{ translate('Shipping Charge Settings') }}</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h1 class="mb-0 h6">{{ translate('Shipping Charge Configuration') }}</h1>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('shipping_charge.update') }}" method="POST">
                    @csrf
                    
                    <!-- Enable/Disable Shipping Charges -->
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label">{{ translate('Enable Shipping Charges') }}</label>
                        <div class="col-sm-9">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" name="is_enabled" value="1" {{ $settings->is_enabled ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Shipping Title -->
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label">{{ translate('Shipping Title') }}</label>
                        <div class="col-sm-9">
                            <input type="text" name="title" class="form-control" 
                                   placeholder="{{ translate('Enter shipping title') }}" 
                                   value="{{ $settings->title }}" required>
                            <small class="text-muted">{{ translate('This will be displayed on the frontend') }}</small>
                        </div>
                    </div>

                    <!-- Shipping Description -->
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label">{{ translate('Description') }}</label>
                        <div class="col-sm-9">
                            <textarea name="description" class="aiz-text-editor form-control" 
                                      data-buttons='[["font", ["bold", "underline", "italic"]],["para", ["ul", "ol"]],["insert", ["link"]],["view", ["undo","redo"]]]'
                                      data-min-height="150"
                                      placeholder="{{ translate('Enter shipping description') }}">{{ $settings->description }}</textarea>
                            <small class="text-muted">{{ translate('Optional description about shipping charges') }}</small>
                        </div>
                    </div>

                    <!-- Inside Dhaka Charge -->
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label">{{ translate('Inside Dhaka Charge') }}</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="number" name="inside_dhaka_charge" class="form-control" 
                                       placeholder="0" step="0.01" min="0" 
                                       value="{{ $settings->inside_dhaka_charge }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ get_setting('system_default_currency') }}</span>
                                </div>
                            </div>
                            <small class="text-muted">{{ translate('Shipping charge for deliveries inside Dhaka') }}</small>
                        </div>
                    </div>

                    <!-- Outside Dhaka Charge -->
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label">{{ translate('Outside Dhaka Charge') }}</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="number" name="outside_dhaka_charge" class="form-control" 
                                       placeholder="0" step="0.01" min="0" 
                                       value="{{ $settings->outside_dhaka_charge }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ get_setting('system_default_currency') }}</span>
                                </div>
                            </div>
                            <small class="text-muted">{{ translate('Shipping charge for deliveries outside Dhaka') }}</small>
                        </div>
                    </div>

                    <!-- Free Shipping Threshold -->
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label">{{ translate('Free Shipping Threshold') }}</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="number" name="free_shipping_threshold" class="form-control" 
                                       placeholder="0" step="0.01" min="0" 
                                       value="{{ $settings->free_shipping_threshold }}">
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ get_setting('system_default_currency') }}</span>
                                </div>
                            </div>
                            <small class="text-muted">{{ translate('Orders above this amount will have free shipping (leave empty to disable)') }}</small>
                        </div>
                    </div>

                    <!-- Delivery Time Inside Dhaka -->
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label">{{ translate('Delivery Time (Inside Dhaka)') }}</label>
                        <div class="col-sm-9">
                            <input type="text" name="delivery_time_inside" class="form-control" 
                                   placeholder="{{ translate('e.g., 1-2 business days') }}" 
                                   value="{{ $settings->delivery_time_inside }}">
                            <small class="text-muted">{{ translate('Expected delivery time for inside Dhaka') }}</small>
                        </div>
                    </div>

                    <!-- Delivery Time Outside Dhaka -->
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label">{{ translate('Delivery Time (Outside Dhaka)') }}</label>
                        <div class="col-sm-9">
                            <input type="text" name="delivery_time_outside" class="form-control" 
                                   placeholder="{{ translate('e.g., 3-5 business days') }}" 
                                   value="{{ $settings->delivery_time_outside }}">
                            <small class="text-muted">{{ translate('Expected delivery time for outside Dhaka') }}</small>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Update Settings') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection