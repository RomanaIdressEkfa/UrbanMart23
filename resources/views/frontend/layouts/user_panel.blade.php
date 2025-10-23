@extends('frontend.layouts.app')
@section('content')
<section class="">
    <div class="container-full">
        <div class="d-flex align-items-start">
			@include('frontend.inc.user_side_nav')
			<div class="aiz-user-panel">
				@yield('panel_content')
            </div>
        </div>
    </div>
</section>
@endsection

