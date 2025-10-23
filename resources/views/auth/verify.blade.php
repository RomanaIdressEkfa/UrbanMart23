{{-- Mohammad Hassan --}}
@extends('frontend.layouts.app')

@section('content')
    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 d-none d-xl-block">
                        @include('frontend.inc.user_side_nav')
                </div>

                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ translate('Email Verification') }}</h6>
                            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-soft-primary btn-sm">{{ translate('Resend') }}</button>
                            </form>
                        </div>
                        <div class="card-body">
                            @if (session('resent'))
                                <div class="alert alert-success" role="alert">
                                    {{ translate('A fresh verification link has been sent to your email address.') }}
                                </div>
                            @endif

                            <p>{{ translate('Before proceeding, please check your email for a verification link or request another.') }}</p>

                            <hr>
                            <h6>{{ translate('Have a verification code?') }}</h6>
                            <form method="POST" action="{{ route('verify-code') }}">
                                @csrf
                                <div class="form-group row">
                                    <label for="code" class="col-md-4 col-form-label">{{ translate('Verification Code') }}</label>
                                    <div class="col-md-6">
                                        <input id="code" type="text" class="form-control" name="code" required autofocus>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">{{ translate('Verify Code') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

