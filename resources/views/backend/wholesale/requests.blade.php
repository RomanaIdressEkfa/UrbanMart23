@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Pending Wholesaler Requests') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Business Name') }}</th>
                        <th>{{ translate('Email Address') }}</th>
                        <th data-breakpoints="md">{{ translate('Phone') }}</th>
                        <th data-breakpoints="md">{{ translate('Submitted At') }}</th>
                        <th class="text-right" width="15%">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pending_wholesalers as $key => $wholesaler)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $wholesaler->business_name }}</td>
                            <td>{{ $wholesaler->email }}</td>
                            <td>{{ $wholesaler->phone }}</td>
                            <td>{{ $wholesaler->created_at->format('d-m-Y h:i A') }}</td>
                            <td class="text-right">
                                <!-- START: নতুন "View Details" বাটন যোগ করা হয়েছে -->
                                <button type="button" class="btn btn-soft-info btn-icon btn-circle btn-sm view-details-btn"
                                    title="{{ translate('View Details') }}" data-toggle="modal"
                                    data-target="#wholesalerDetailsModal"
                                    data-business_name="{{ $wholesaler->business_name }}"
                                    data-email="{{ $wholesaler->email }}" data-phone="{{ $wholesaler->phone }}"
                                    data-address="{{ $wholesaler->address }}"
                                    data-facebook_link="{{ $wholesaler->facebook_link }}"
                                    data-website_link="{{ $wholesaler->website_link }}"
                                    data-trade_license="{{ $wholesaler->trade_license }}">
                                    <i class="las la-eye"></i>
                                </button>
                                <!-- END: নতুন "View Details" বাটন যোগ করা হয়েছে -->

                                <!-- Approve Button -->
                                <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                    href="{{ route('wholesaler.request.approve', $wholesaler->id) }}"
                                    title="{{ translate('Approve') }}">
                                    <i class="las la-check"></i>
                                </a>
                                <!-- Reject Button -->
                                <a class="btn btn-soft-warning btn-icon btn-circle btn-sm"
                                    href="{{ route('wholesaler.request.reject', $wholesaler->id) }}"
                                    title="{{ translate('Reject') }}">
                                    <i class="las la-times"></i>
                                </a>
                                <!-- Delete Button -->
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                    data-href="{{ route('wholesaler.request.delete', $wholesaler->id) }}"
                                    title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="6">{{ translate('No pending requests found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection



@section('modal')
    @include('modals.delete_modal')
    @include('backend.wholesale.wholesaler_details_modal')
@endsection

{{-- @section('modal')

    @include('modals.delete_modal')

    <!-- START: Wholesaler Details Modal -->
    <div class="modal fade" id="wholesalerDetailsModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="wholesalerModalTitle">{{ translate('Wholesaler Applicant Details') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ translate('Business Name') }}:</strong> <span id="detailBusinessName"></span></p>
                            <p><strong>{{ translate('Email Address') }}:</strong> <span id="detailEmail"></span></p>
                            <p><strong>{{ translate('Phone') }}:</strong> <span id="detailPhone"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ translate('Trade License') }}:</strong> <span id="detailTradeLicense"></span></p>
                            <p><strong>{{ translate('Website') }}:</strong> <a id="detailWebsiteLink" href="#"
                                    target="_blank"></a></p>
                            <p><strong>{{ translate('Facebook Page') }}:</strong> <a id="detailFacebookLink" href="#"
                                    target="_blank"></a></p>
                        </div>
                    </div>
                    <hr>
                    <p><strong>{{ translate('Full Address') }}:</strong></p>
                    <p id="detailAddress"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection --}}

@section('script')
    <script type="text/javascript">
        // এই জাভাস্ক্রিপ্ট কোডটি মডাল পপ-আপে ডাইনামিকভাবে ডেটা দেখানোর জন্য লেখা হয়েছে।
        $(document).on('click', '.view-details-btn', function() {
            var modal = $('#wholesalerDetailsModal');

            // বাটন থেকে data-* attribute ব্যবহার করে তথ্য সংগ্রহ করা হচ্ছে
            var businessName = $(this).data('business_name');
            var email = $(this).data('email');
            var phone = $(this).data('phone');
            var address = $(this).data('address');
            var facebookLink = $(this).data('facebook_link');
            var websiteLink = $(this).data('website_link');
            var tradeLicense = $(this).data('trade_license');

            // মডালের ভেতরে নির্দিষ্ট জায়গায় তথ্যগুলো বসানো হচ্ছে
            modal.find('#detailBusinessName').text(businessName);
            modal.find('#detailEmail').text(email);
            modal.find('#detailPhone').text(phone);
            modal.find('#detailAddress').text(address);

            // যদি ট্রেড লাইসেন্স থাকে, তাহলে দেখানো হবে, না থাকলে 'N/A' দেখানো হবে
            modal.find('#detailTradeLicense').text(tradeLicense ? tradeLicense : 'N/A');

            // যদি ওয়েবসাইট লিঙ্ক থাকে, তাহলে সেটি লিঙ্কে পরিণত করে দেখানো হবে
            if (websiteLink) {
                modal.find('#detailWebsiteLink').attr('href', websiteLink).text(websiteLink);
            } else {
                modal.find('#detailWebsiteLink').removeAttr('href').text('N/A');
            }

            // যদি ফেসবুক লিঙ্ক থাকে, তাহলে সেটি লিঙ্কে পরিণত করে দেখানো হবে
            if (facebookLink) {
                modal.find('#detailFacebookLink').attr('href', facebookLink).text('View Facebook Page');
            } else {
                modal.find('#detailFacebookLink').removeAttr('href').text('N/A');
            }
        });
    </script>
@endsection

