<div class="modal fade" id="customer-history-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Customer History')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($order->user)
                    <div class="text-center">
                        <span class="avatar avatar-xxl mb-3">
                            <img src="{{ $order->user->avatar_original ? uploaded_asset($order->user->avatar_original) : static_asset('assets/img/avatar-place.png') }}"
                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                        </span>
                        <h1 class="h5 mb-1">{{ $order->user->name }}</h1>
                    </div>

                    <div class="mt-4">
                        <h6 class="separator mb-3"><span class="bg-white px-3">{{ translate('Account Information') }}</span></h6>
                        <p><strong>{{ translate('Full Name') }}:</strong> <span class="ml-2">{{ $order->user->name }}</span></p>
                        <p><strong>{{ translate('Email') }}:</strong> <span class="ml-2">{{ $order->user->email }}</span></p>
                        <p><strong>{{ translate('Phone') }}:</strong> <span class="ml-2">{{ $order->user->phone }}</span></p>
                    </div>

                    <div class="mt-4">
                        <h6 class="separator mb-3"><span class="bg-white px-3">{{ translate('Pre-order Information') }}</span></h6>
                        <p><strong>{{ translate('Number of Orders') }}:</strong> <span class="ml-2">{{ $order->user->preorders()->count() }}</span></p>
                        <p><strong>{{ translate('Ordered Amount') }}:</strong> <span class="ml-2">{{ format_price($order->user->preorders()->sum('grand_total')) }}</span></p>
                    </div>
                @else
                    <p class="text-center">{{ translate('No user information available for guest.') }}</p>
                @endif
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Close')}}</button>
            </div>
        </div>
    </div>
</div>