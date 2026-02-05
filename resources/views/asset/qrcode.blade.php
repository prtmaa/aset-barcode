<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code Aset</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <input type="hidden" id="qrBase64">

            <div class="modal-body d-flex flex-column align-items-center">
                <div id="qrContainer" class="qr-center"></div>
                <small id="kodeAset" class="text-muted mt-2"></small>

                <button class="btn btn-primary btn-sm mt-3" onclick="downloadQrPdf()"><i class="fas fa-download"></i>
                </button>
            </div>
        </div>
    </div>
</div>
