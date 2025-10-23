<!-- Wholesaler Details Modal -->
<div class="modal fade" id="wholesalerDetailsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wholesalerModalTitle">{{translate('Wholesaler Applicant Details')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{translate('Business Name')}}:</strong> <span id="detailBusinessName"></span></p>
                        <p><strong>{{translate('Email Address')}}:</strong> <span id="detailEmail"></span></p>
                        <p><strong>{{translate('Phone')}}:</strong> <span id="detailPhone"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{translate('Trade License')}}:</strong> <span id="detailTradeLicense"></span></p>
                        <p><strong>{{translate('Website')}}:</strong> <a id="detailWebsiteLink" href="#" target="_blank"></a></p>
                        <p><strong>{{translate('Facebook Page')}}:</strong> <a id="detailFacebookLink" href="#" target="_blank"></a></p>
                    </div>
                </div>
                <hr>
                <p><strong>{{translate('Full Address')}}:</strong></p>
                <p id="detailAddress"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{translate('Close')}}</button>
            </div>
        </div>
    </div>
</div>

