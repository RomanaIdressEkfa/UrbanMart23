@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Wholesale Settings') }}</h5>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 h6">{{ translate('General Wholesale Configuration') }}</h6>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('wholesale.settings.update') }}" method="POST">
                        @csrf

                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label class="col-from-label">{{ translate('Minimum Order Quantity for Wholesale') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="number" name="wholesale_min_order_quantity" class="form-control"
                                    value="{{ $wholesale_min_order_quantity }}" min="0" step="1"
                                    placeholder="{{ translate('e.g. 50') }}">
                                <small class="form-text text-muted">{{ translate('Set the minimum quantity required for a product to be considered a wholesale purchase.') }}</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label class="col-from-label">{{ translate('Default Wholesale Discount Percentage') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="number" name="wholesale_discount_percentage" class="form-control"
                                    value="{{ $wholesale_discount_percentage }}" min="0" max="100" step="0.01"
                                    placeholder="{{ translate('e.g. 10.50') }}">
                                <small class="form-text text-muted">{{ translate('This percentage will be applied as a default discount for wholesale products if no specific wholesale price is set for a product.') }}</small>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Save Settings') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

