<div class="modal fade" id="modal-form" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" enctype="multipart/form-data">
            @csrf @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Atribut</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoris as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Atribut</label>
                        <input type="text" name="nama_atribut" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text" name="satuan" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tipe Input</label>
                        <select name="tipe_input" class="form-control tipe-input">
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="select">Select</option>
                        </select>
                    </div>

                    <div class="form-group opsi-input d-none">
                        <label>Opsi (pisahkan dengan koma)</label>
                        <input type="text" name="opsi" class="form-control" placeholder="Opsi 1,Opsi 2,Opsi 3">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
