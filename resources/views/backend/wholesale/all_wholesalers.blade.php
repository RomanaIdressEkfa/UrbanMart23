@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('All Wholesalers') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Business Name') }}</th>
                        <th>{{ translate('Email Address') }}</th>
                        <th data-breakpoints="md">{{ translate('Phone') }}</th>
                        <th data-breakpoints="md">{{ translate('Status') }}</th>
                        <th data-breakpoints="lg">{{ translate('Submitted At') }}</th>
                        <th class="text-right" width="15%">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($all_wholesalers as $key => $wholesaler)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $wholesaler->business_name }}</td>
                            <td>{{ $wholesaler->email }}</td>
                            <td>{{ $wholesaler->phone }}</td>
                            <td>
                                {{-- স্ট্যাটাস অনুযায়ী ভিন্ন ভিন্ন রঙের ব্যাজ দেখানো হচ্ছে --}}
                                @if ($wholesaler->status == 'active')
                                    <span class="badge badge-inline badge-success">{{ translate('Active') }}</span>
                                @elseif($wholesaler->status == 'pending')
                                    <span class="badge badge-inline badge-info">{{ translate('Pending') }}</span>
                                @elseif($wholesaler->status == 'rejected')
                                    <span class="badge badge-inline badge-danger">{{ translate('Rejected') }}</span>
                                @endif
                            </td>
                            <td>{{ $wholesaler->created_at->format('d-m-Y h:i A') }}</td>
                            <td class="text-right">
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

                                {{-- শুধুমাত্র পেন্ডিং থাকলেই Approve/Reject বাটন দেখানো হবে --}}
                                @if ($wholesaler->status == 'pending')
                                    <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                        href="{{ route('wholesaler.request.approve', $wholesaler->id) }}"
                                        title="{{ translate('Approve') }}">
                                        <i class="las la-check"></i>
                                    </a>
                                    <a class="btn btn-soft-warning btn-icon btn-circle btn-sm"
                                        href="{{ route('wholesaler.request.reject', $wholesaler->id) }}"
                                        title="{{ translate('Reject') }}">
                                        <i class="las la-times"></i>
                                    </a>
                                @endif

                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                    data-href="{{ route('wholesaler.request.delete', $wholesaler->id) }}"
                                    title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="7">{{ translate('No wholesalers found.') }}</td>
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

@section('script')
    <script type="text/javascript">
        $(document).on('click', '.view-details-btn', function() {
            var modal = $('#wholesalerDetailsModal');
            var businessName = $(this).data('business_name');
            var email = $(this).data('email');
            var phone = $(this).data('phone');
            var address = $(this).data('address');
            var facebookLink = $(this).data('facebook_link');
            var websiteLink = $(this).data('website_link');
            var tradeLicense = $(this).data('trade_license');

            modal.find('#detailBusinessName').text(businessName);
            modal.find('#detailEmail').text(email);
            modal.find('#detailPhone').text(phone);
            modal.find('#detailAddress').text(address);
            modal.find('#detailTradeLicense').text(tradeLicense ? tradeLicense : 'N/A');

            if (websiteLink) {
                modal.find('#detailWebsiteLink').attr('href', websiteLink).text(websiteLink);
            } else {
                modal.find('#detailWebsiteLink').removeAttr('href').text('N/A');
            }

            if (facebookLink) {
                modal.find('#detailFacebookLink').attr('href', facebookLink).text('View Facebook Page');
            } else {
                modal.find('#detailFacebookLink').removeAttr('href').text('N/A');
            }
        });
    </script>
@endsection

