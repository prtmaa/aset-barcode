@extends('layouts.master')

@section('tittle')
    Data Aset
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"> <a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Aset</li>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">

            <section class="col-lg-12 connectedSortable">

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <label for="" class="mb-2">Export</label>

                                <form action="{{ route('asset.export') }}" method="POST">
                                    @csrf

                                    <div class="form-row">
                                        <div class="col-md-5">
                                            <small>Kategori</small>
                                            <select id="kategorifilter" name="kategori_id[]" class="form-control" multiple>
                                                @foreach ($kategori as $k)
                                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <small>Dari Tanggal</small>
                                            <input type="text" name="tanggal_dari" class="form-control tanggal">
                                        </div>

                                        <div class="col-md-3">
                                            <small>Sampai Tanggal</small>
                                            <input type="text" name="tanggal_sampai" class="form-control tanggal">
                                        </div>

                                        <div class="col-md-1 d-flex align-items-end mt-3 mt-sm-0">
                                            <button class="btn btn-success btn-sm">
                                                <i class="fa fa-file-excel"></i> Excel
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <div class="btn-group">
                                    <button onclick="addForm('{{ route('asset.store') }}')" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus-circle"></i> Tambah Data
                                    </button>
                                </div>
                            </div>

                            <div class="card-body table-responsive">
                                <form action="" class="form-produk" method="post">
                                    @csrf
                                    <table class="table table-bordered text-center" id="table-aset">
                                        <thead>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Nama Aset</th>
                                            <th>Vendor</th>
                                            <th>Tipe</th>
                                            <th>Jumlah</th>
                                            <th>Lokasi</th>
                                            <th>PIC/Divisi</th>
                                            <th>Tanggal Perolehan</th>
                                            <th>Nilai Perolehan</th>
                                            <th>Kondisi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </form>
                            </div>

                        </div>

                    </div>

                </div>

            </section>
        </div>

        @include('asset.form')
        @include('asset.foto')
        @include('asset.qrcode')
        @include('asset.detail')
        @include('asset.depresiasi')
    @endsection

    @push('js')
        <script>
            let table;
            $(function() {
                table = $('#table-aset').DataTable({
                    processing: true,
                    serverSide: true,
                    deferRender: true,
                    autoWidth: false,
                    responsive: true,
                    "language": {
                        "sProcessing": "Sedang memproses...",
                        "sLengthMenu": "Tampilkan _MENU_ entri",
                        "sZeroRecords": "Tidak ditemukan data yang sesuai",
                        "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                        "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                        "sSearch": "Pencarian:",
                        "oPaginate": {
                            "sFirst": "Pertama",
                            "sPrevious": "Sebelumnya",
                            "sNext": "Selanjutnya",
                            "sLast": "Terakhir"
                        },
                    },
                    ajax: {
                        url: '{{ route('asset.data') }}',
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false
                        },
                        {
                            data: 'kode_aset'
                        },
                        {
                            data: 'nama_aset'
                        },
                        {
                            data: 'vendor'
                        },
                        {
                            data: 'tipe'
                        },
                        {
                            data: 'jumlah'
                        },
                        {
                            data: 'lokasi'
                        },
                        {
                            data: 'pengguna',
                        },
                        {
                            data: 'tanggal_pembelian'
                        },
                        {
                            data: 'harga'
                        },
                        {
                            data: 'kondisi'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'aksi',
                            orderable: false,
                            searchable: false
                        },
                    ]

                });

                $('#modal-form').validator().on('submit', function(e) {
                    if (!e.preventDefault()) {
                        $.ajax({
                                enctype: 'multipart/form-data',
                                url: $('#modal-form form').attr('action'),
                                type: $('#modal-form form').attr('method'),
                                data: new FormData($('#modal-form form')[0]),
                                async: false,
                                processData: false,
                                contentType: false
                            })
                            .done((response) => {
                                $('#modal-form').modal('hide');
                                table.ajax.reload();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data berhasil disimpan',
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                            })
                            .fail((errors) => {
                                Swal.fire({
                                    icon: 'warning',
                                    confirmButtonColor: '#3085d6',
                                    iconColor: '#dc3545',
                                    title: 'Oops...',
                                    text: 'Data gagal disimpan',
                                })
                            });
                    }
                })

            });

            $('#modal-form').on('hidden.bs.modal', function() {
                resetFoto();

                $('select[name="kategori_id"]').val('').trigger('change');
                $('#atribut-area').html('<i class="text-muted">Pilih kategori terlebih dahulu</i>');

                $('#is_assign').prop('checked', false); // uncheck checkbox
                $('#assign-area').hide(); // sembunyikan form assign

                $('#assign-area')
                    .find('input, select, textarea')
                    .val(''); // reset field assign
            });

            function addForm(url) {
                $('#modal-form').modal({
                    backdrop: 'static',
                    keyboard: false
                }).modal('show');
                $('#modal-form .modal-title').text('Tambah Data');

                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('post');
                $('#modal-form [name=nama]').focus();
            }

            function editForm(url) {
                $('#modal-form').modal({
                    backdrop: 'static',
                    keyboard: false
                }).modal('show');

                $('#modal-form .modal-title').text('Edit Data');
                $('#modal-form form')[0].reset();

                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('put');

                $.get(url).done(function(res) {

                    $('[name=kode_aset]').val(res.kode_aset);
                    $('[name=nama_aset]').val(res.nama_aset);
                    $('[name=lokasi_id]').val(res.lokasi_id);
                    $('[name=tipe_id]').val(res.tipe_id);
                    $('[name=vendor_id]').val(res.vendor_id);
                    $('[name=kondisi]').val(res.kondisi);
                    $('[name=catatan]').val(res.catatan);
                    $('[name=kelengkapan]').val(res.kelengkapan);
                    $('[name=tanggal_pembelian]').val(res.tanggal_pembelian);
                    const formattedharga = formatEdit(res.harga);
                    $('#modal-form [name=harga]').val(formattedharga);
                    $('[name=jumlah]').val(res.jumlah);
                    $('[name=status]').val(res.status);

                    // PREVIEW FOTO LAMA
                    if (res.foto) {
                        $('#preview-foto')
                            .attr('src', '/' + res.foto + '?v=' + Date.now())
                            .css('display', 'block');

                        $('.custom-file-label').text(res.foto.split('/').pop());
                    }

                    // ASSIGNMENT
                    if (res.assignment_aktif) {

                        $('#is_assign').prop('checked', true);
                        $('#assign-area').show();

                        $('[name=employee_id]').val(res.assignment_aktif.employee_id).trigger('change');
                        $('[name=tanggal_mulai]').val(res.assignment_aktif.tanggal_mulai);
                        $('[name=keterangan]').val(res.assignment_aktif.keterangan);

                    } else {
                        $('#is_assign').prop('checked', false);
                        $('#assign-area').hide();
                    }

                    // atribut
                    let atributValues = {};
                    if (res.atribut_values) {
                        res.atribut_values.forEach(i => {
                            atributValues[i.asset_attribute_id] = i.nilai;
                        });
                    }

                    $('[name=kategori_id]').val(res.kategori_id).trigger('change');

                    const interval = setInterval(() => {
                        let ready = true;
                        for (const id in atributValues) {
                            if ($(`[name="atribut[${id}]"]`).length === 0) {
                                ready = false;
                                break;
                            }
                        }

                        if (ready) {
                            clearInterval(interval);
                            for (const id in atributValues) {
                                $(`[name="atribut[${id}]"]`).val(atributValues[id]);
                            }
                        }
                    }, 100);
                });

            }

            function deleteData(url) {
                Swal.fire({
                    title: 'Yakin?',
                    text: "Data akan dihapus",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                                '_token': $('[name=csrf-token]').attr('content'),
                                '_method': 'delete'
                            })
                            .done((response) => {
                                table.ajax.reload();
                                $('.alertdelete').fadeIn();

                                setTimeout(() => {
                                    $('.alertdelete').fadeOut();
                                }, 3000);
                            })
                            .fail((errors) => {
                                Swal.fire({
                                    icon: 'warning',
                                    confirmButtonColor: '#3085d6',
                                    iconColor: '#dc3545',
                                    title: 'Oops...',
                                    text: 'Data gagal dihapus',
                                })
                            });
                    }
                })
            }

            $('select[name="kategori_id"]').change(function() {

                let kategoriId = $(this).val();
                $('#atribut-area').html('');

                if (!kategoriId) {
                    $('#atribut-area').html('<i class="text-muted">Pilih kategori terlebih dahulu</i>');
                    return;
                }

                $.get('/asset/atribut/' + kategoriId, function(data) {

                    if (data.length === 0) {
                        $('#atribut-area').html('<i class="text-muted">Tidak ada spesifikasi</i>');
                        return;
                    }

                    data.forEach(attr => {

                        let field = '';

                        //BUILD INPUT DASAR
                        let input = '';

                        // NUMBER
                        if (attr.tipe_input === 'number') {
                            input =
                                `<input type="number" name="atribut[${attr.id}]" class="form-control">`;
                        }

                        // SELECT
                        else if (attr.tipe_input === 'select') {

                            let optionsHtml = `<option value="">-- Pilih --</option>`;

                            if (attr.opsi) {
                                attr.opsi.split(',').forEach(item => {
                                    let parts = item.split('|');
                                    let label = parts[0].trim();
                                    let value = parts[1] ? parts[1].trim() : label;
                                    optionsHtml += `<option value="${value}">${label}</option>`;
                                });
                            }

                            input = `
                    <select name="atribut[${attr.id}]" class="form-control">
                        ${optionsHtml}
                    </select>
                `;
                        }

                        // TEXT
                        else {
                            input =
                                `<input type="text" name="atribut[${attr.id}]" class="form-control">`;
                        }

                        // JIKA ADA SATUAN → INPUT GROUP
                        if (attr.satuan) {
                            field = `
                    <div class="input-group">
                        ${input}
                        <div class="input-group-append">
                            <span class="input-group-text">${attr.satuan}</span>
                        </div>
                    </div>
                `;
                        } else {
                            field = input;
                        }

                        // APPEND KE FORM
                        $('#atribut-area').append(`
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">
                        ${attr.nama_atribut}
                    </label>
                    <div class="col-md-8">
                        ${field}
                    </div>
                </div>
            `);
                    });

                });
            });


            function previewFoto() {
                const input = document.getElementById('foto');
                const preview = document.getElementById('preview-foto');
                const label = document.querySelector('label[for="foto"]');

                if (input.files && input.files[0]) {
                    const file = input.files[0];

                    // Ubah label jadi nama file
                    label.innerText = file.name;

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            }

            function resetFoto() {
                const input = document.getElementById('foto');
                const preview = document.getElementById('preview-foto');
                const label = document.querySelector('label[for="foto"]');

                input.value = '';
                preview.src = '';
                preview.style.display = 'none';
                label.innerText = 'Pilih foto aset';
            }

            flatpickr(".tanggal", {
                dateFormat: "Y-m-d",
                locale: "id",
                allowInput: true,
                disableMobile: true,
                onReady: function(selectedDates, dateStr, instance) {

                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.innerHTML = "Reset";
                    btn.className = "btn btn-sm btn-light w-100 mt-1";

                    btn.addEventListener("click", function() {
                        instance.clear();
                        instance.close();
                    });

                    instance.calendarContainer.appendChild(btn);
                    instance.input.style.backgroundColor = "#fff";
                    instance.input.style.color = "#000";
                    instance.input.style.border = "1px solid #ced4da";
                }
            });


            $('#is_assign').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#assign-area').slideDown();
                } else {
                    $('#assign-area').slideUp();

                    // reset field assign
                    $('#assign-area')
                        .find('input, select, textarea')
                        .val('');
                }
            });

            $(document).on('click', '.btn-detail', function() {

                $('#modal-kategori').text($(this).data('kategori') || '-');
                $('#modal-spesifikasi').text($(this).data('spesifikasi') || '-');
                $('#modal-kelengkapan').text($(this).data('kelengkapan') || '-');
                $('#modal-catatan').text($(this).data('catatan') || '-');

                $('#detailModal').modal('show');
            });
        </script>
        <script>
            function showFotoModal(src) {
                if (!src || src === 'null') {
                    Swal.fire({
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        title: 'Foto tidak tersedia',
                        text: 'Aset ini belum memiliki foto.',
                    });
                    return;
                }

                const img = document.getElementById('fotoPreview');
                img.src = src;
                $('#fotoModal').modal('show');
            }

            let currentKodeAset = null;

            function showQrModal(kode) {
                currentKodeAset = kode;
                $('#qrContainer').html('');
                $('#kodeAset').text(kode);

                let qr = new QRCode(document.getElementById("qrContainer"), {
                    text: kode,
                    width: 200,
                    height: 200
                });

                setTimeout(() => {
                    let canvas = $('#qrContainer canvas')[0];
                    let base64 = canvas.toDataURL("image/png");
                    $('#qrBase64').val(base64);
                }, 300);

                $('#qrModal').modal('show');
            }

            function downloadQrPdf() {
                let base64 = $('#qrBase64').val();

                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '/asset/qr-pdf';

                form.innerHTML = `
                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                    <input type="hidden" name="kode" value="${currentKodeAset}">
                    <input type="hidden" name="qr" value="${base64}">
                `;

                document.body.appendChild(form);
                form.submit();
            }


            // input harga
            function formatRibuanKoma(el) {
                let value = el.value;

                // hanya izinkan angka dan koma
                value = value.replace(/[^0-9,]/g, '');

                let parts = value.split(',');

                // format bagian ribuan
                let angka = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                if (parts.length > 1) {
                    el.value = angka + ',' + parts[1];
                } else {
                    el.value = angka;
                }
            }

            document.getElementById('harga').addEventListener('input', function() {
                formatRibuanKoma(this);
            });

            function formatEdit(value) {
                if (!value) return "";

                // ubah ke string
                value = value.toString();

                // ubah titik desimal SQL → koma
                value = value.replace('.', ',');

                // pecah angka
                let parts = value.split(',');

                // tambahkan titik ribuan
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                return parts.join(',');
            }
        </script>
        <script>
            $(document).ready(function() {
                $('#modal-form').on('shown.bs.modal', function() {
                    $(this).find('.select2').select2({
                        dropdownParent: $('#modal-form .modal-body'),
                        theme: 'bootstrap4',
                        width: '100%'
                    });
                });
            });


            $(document).ready(function() {
                $('#kategorifilter').select2({
                    width: '100%'
                });
            });
        </script>
        <script>
            let currentAssetId = null;
            let currentUmur = null;

            $(document).on('click', '.show-depresiasi', function() {
                currentAssetId = $(this).data('id');
                currentUmur = $(this).data('umur');
                currentBulanTerpakai = $(this).data('bulan');
                currentPerBulan = $(this).data('dep-bulan');
                currentTotalDep = $(this).data('total-dep');
                currentNilaiBuku = $(this).data('nilai-buku');

                $('#depHarga').text($(this).data('harga'));
                $('#depUmur').text($(this).data('umur') + ' Bulan');
                $('#depBulan').text($(this).data('bulan') + '/' + $(this).data('umur') + ' Bulan');
                $('#depPerBulan').text($(this).data('dep-bulan'));
                $('#depTotal').text($(this).data('total-dep'));
                $('#depNilaiBuku').text($(this).data('nilai-buku'));

                if ($(this).data('disposal') == 1) {
                    $('#depDisposal').removeClass('d-none');
                    $('#depTglDisposal').text($(this).data('tgl-disposal'));
                } else {
                    $('#depDisposal').addClass('d-none');
                }

                $('#modalDepresiasi').modal('show');
            });

            $(document).on('click', '#btnEditUmur', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Edit',
                    input: 'number',
                    inputLabel: 'Umur Manfaat (bulan)',
                    inputValue: currentUmur,
                    inputAttributes: {
                        min: 1
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    confirmButtonColor: '#3085d6',
                    cancelButtonText: 'Batal',
                    cancelButtonColor: '#dc3545',
                    inputValidator: (value) => {
                        if (!value || value < 1) {
                            return 'Umur manfaat tidak valid';
                        }
                    }
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    updateUmurManfaat(result.value);
                });
            });


            function updateUmurManfaat(umur) {

                $.ajax({
                    url: `/asset/${currentAssetId}/update-umur`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        umur_manfaat: umur
                    },
                    success: function(res) {

                        currentUmur = res.umur;
                        currentBulanTerpakai = res.bulan;
                        currentPerBulan = res.per_bulan;
                        currentTotalDep = res.total_dep;
                        currentNilaiBuku = res.nilai_buku;

                        $('#depUmur').text(res.umur + ' Bulan');
                        $('#depBulan').text(res.bulan + '/' + res.umur + ' Bulan');
                        $('#depPerBulan').text(res.per_bulan);
                        $('#depTotal').text(res.total_dep);
                        $('#depNilaiBuku').text(res.nilai_buku);

                        if (res.is_disposal) {
                            $('#depDisposal').removeClass('d-none');
                            $('#depTglDisposal').text(res.tgl_disposal);
                        } else {
                            $('#depDisposal').addClass('d-none');
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Umur manfaat diperbarui',
                            timer: 1300,
                            showConfirmButton: false
                        });

                        $('#tableAsset').DataTable().ajax.reload(null, false);
                    }

                });
            }
        </script>
    @endpush
