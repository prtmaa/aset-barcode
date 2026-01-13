   <!-- Modal -->
   <div class="modal fade bd-example-modal-lg" id="modal-form" role="dialog" aria-labelledby="modal-form"
       aria-hidden="true">
       <div class="modal-dialog modal-lg" role="document">
           <form method="post" enctype="multipart/form-data">
               @csrf @method('post')

               <div class="modal-content">
                   <div class="modal-header">
                       <h5 class="modal-title"></h5>
                   </div>

                   <div class="modal-body">

                       <div class="form-group row">
                           <label for="kode_aset" class="col-md-4 col-md-offset-1 control-label">Kode Aset</label>
                           <div class="col-md-8">
                               <input type="text" name="kode_aset" id="kode_aset" class="form-control" required
                                   autofocus oninvalid="this.setCustomValidity('Silahkan masukan kode aset')"
                                   oninput="this.setCustomValidity('')" placeholder="GRO22110100XXXX">
                               <span class="help-block with-errors"></span>
                           </div>
                       </div>

                       <div class="form-group row">
                           <label for="nama_aset" class="col-md-4 col-md-offset-1 control-label">Nama Aset</label>
                           <div class="col-md-8">
                               <input type="text" name="nama_aset" id="nama_aset" class="form-control" required
                                   autofocus oninvalid="this.setCustomValidity('Silahkan masukan nama aset')"
                                   oninput="this.setCustomValidity('')">
                               <span class="help-block with-errors"></span>
                           </div>
                       </div>

                       <div class="form-group row">
                           <div class="col-md-8 offset-md-4">
                               <div class="form-check">
                                   <input type="checkbox" class="form-check-input" id="is_assign" name="is_assign"
                                       value="1">
                                   <label class="form-check-label" for="is_assign">
                                       Assign aset ke pengguna
                                   </label>
                               </div>
                           </div>
                       </div>

                       <div id="assign-area" style="display:none;">

                           <div class="form-group row">
                               <label class="col-md-4 col-form-label">Pengguna</label>
                               <div class="col-md-8">
                                   <select name="employee_id" class="form-control select2bs4">
                                       <option value="">Pilih Pengguna...</option>
                                       @foreach ($employee as $emp)
                                           <option value="{{ $emp->id }}">{{ $emp->nama }}</option>
                                       @endforeach
                                   </select>
                               </div>
                           </div>

                           <div class="form-group row">
                               <label class="col-md-4 col-form-label">Tanggal Mulai</label>
                               <div class="col-md-8">
                                   <input type="date" name="tanggal_mulai" class="form-control tanggal">
                               </div>
                           </div>

                           <div class="form-group row">
                               <label class="col-md-4 col-form-label">Keterangan</label>
                               <div class="col-md-8">
                                   <textarea name="keterangan" class="form-control"></textarea>
                               </div>
                           </div>

                       </div>

                       <div class="form-group row">
                           <label for="kategori_id" class="col-md-4 col-md-offset-1 control-label">Kategori</label>
                           <div class="col-md-8">
                               <select name="kategori_id" id="kategori_id" class="form-control select2bs4"
                                   oninvalid="this.setCustomValidity('Silahkan pilih kategori')"
                                   oninput="this.setCustomValidity('')">
                                   <option value="" disabled selected>Pilih kategori...</option>
                                   @foreach ($kategori as $itm)
                                       <option value="{{ $itm->id }}">{{ $itm->nama }}
                                       </option>
                                   @endforeach
                               </select>
                               <span class="help-block with-errors"></span>
                           </div>
                       </div>

                       <div class="form-group row">
                           <label class="col-md-4 col-md-offset-1 control-label">Spesifikasi Aset</label>
                           <div id="atribut-area" class="col-md-8">
                               <i class="text-muted">Pilih kategori terlebih dahulu</i>
                           </div>
                       </div>

                       <div class="form-group row">
                           <label for="tanggal_pembelian" class="col-md-4 col-md-offset-1 control-label">Tgl
                               Pembelian</label>
                           <div class="col-md-8">
                               <input type="text" name="tanggal_pembelian" id="tanggal_pembelian"
                                   class="form-control tanggal_pembelian tanggal">
                           </div>
                       </div>

                       <div class="form-group row">
                           <label for="kelengkapan" class="col-md-4 col-md-offset-1 control-label">Kelengkapan</label>
                           <div class="col-md-8">
                               <input type="text" name="kelengkapan" id="kelengkapan" class="form-control"
                                   placeholder="Part 1,Part 2,Part 3">
                           </div>
                       </div>

                       <div class="form-group row">
                           <label for="lokasi_id" class="col-md-4 col-md-offset-1 control-label">Lokasi</label>
                           <div class="col-md-8">
                               <select name="lokasi_id" id="lokasi_id" class="form-control select2bs4">
                                   <option value="" disabled selected>Pilih lokasi...</option>
                                   @foreach ($lokasi as $itm)
                                       <option value="{{ $itm->id }}">{{ $itm->nama }}
                                       </option>
                                   @endforeach
                               </select>
                               <span class="help-block with-errors"></span>
                           </div>
                       </div>

                       <div class="form-group row">
                           <label class="col-md-4 col-md-offset-1 control-label">Kondisi</label>
                           <div class="col-md-8">
                               <select name="kondisi" class="form-control">
                                   <option value="baik">Baik</option>
                                   <option value="rusak">Rusak</option>
                                   <option value="maintenance">Maintenance</option>
                               </select>
                           </div>
                       </div>

                       <div class="form-group row">
                           <label class="col-md-4 col-md-offset-1 control-label">Catatan</label>
                           <div class="col-md-8">
                               <textarea name="catatan" class="form-control"></textarea>
                           </div>
                       </div>

                       <div class="form-group row">
                           <label class="col-md-4 col-form-label">
                               Foto Aset
                           </label>

                           <div class="col-md-8">
                               <div class="custom-file">
                                   <input type="file" name="foto" class="custom-file-input" id="foto"
                                       accept="image/*" onchange="previewFoto()">

                                   <label class="custom-file-label" for="foto">
                                       Pilih foto aset
                                   </label>
                               </div>

                               <small class="form-text text-muted">
                                   Format JPG/PNG • Maks. 2MB
                               </small>

                               <div class="mt-2">
                                   <img id="preview-foto" src="{{ asset('images/no-image.png') }}"
                                       class="img-thumbnail" style="max-height: 150px; display:none;">
                               </div>
                           </div>
                       </div>


                   </div>

                   <div class="modal-footer">
                       <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i
                               class="fa fa-xmark"></i> Batal</button>
                       <button class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Simpan</button>
                   </div>

               </div>
           </form>
       </div>
   </div>
