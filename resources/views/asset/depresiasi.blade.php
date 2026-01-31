<div class="modal fade" id="modalDepresiasi">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Depresiasi Aset</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div class="row mb-2">
                    <div class="col-6 text-muted">Nilai Perolehan</div>
                    <div class="col-6 text-end fw-bold" id="depHarga"></div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-6 text-muted">Umur Manfaat</div>
                    <div class="col-6 text-end fw-bold">
                        <span id="depUmur"></span>
                        <button type="button" class="btn btn-xs text-primary ms-1" id="btnEditUmur">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-6 text-muted">Bulan Terpakai</div>
                    <div class="col-6 text-end fw-bold" id="depBulan"></div>
                </div>

                <div class="row mb-2">
                    <div class="col-6 text-muted">Depresiasi / Bulan</div>
                    <div class="col-6 text-end fw-bold" id="depPerBulan"></div>
                </div>

                <div class="row mb-2">
                    <div class="col-6 text-muted">Total Depresiasi</div>
                    <div class="col-6 text-end fw-bold" id="depTotal"></div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-6 text-muted">Nilai Buku</div>
                    <div class="col-6 text-end fw-bold" id="depNilaiBuku"></div>
                </div>

                <div class="text-danger fw-bold mt-2 d-none" id="depDisposal">
                    DISPOSAL (<span id="depTglDisposal"></span>)
                </div>
            </div>
        </div>
    </div>
</div>
