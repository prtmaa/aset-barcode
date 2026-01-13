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
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>Pengguna</th>
                                            <th>Kategori</th>
                                            <th>Spesifikasi</th>
                                            <th>Pembelian</th>
                                            <th>Kelengkapan</th>
                                            <th>Lokasi</th>
                                            <th>Kondisi</th>
                                            <th>Catatan</th>
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
    @endsection

    @push('js')
        <script>
            let table;
            $(function() {
                table = $('.table').DataTable({
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
                            data: 'pengguna',
                        },
                        {
                            data: 'kategori'
                        },
                        {
                            data: 'atribut',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tanggal_pembelian'
                        },
                        {
                            data: 'kelengkapan'
                        },
                        {
                            data: 'lokasi'
                        },
                        {
                            data: 'kondisi'
                        },
                        {
                            data: 'catatan',
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
                    $('[name=kondisi]').val(res.kondisi);
                    $('[name=catatan]').val(res.catatan);
                    $('[name=kelengkapan]').val(res.kelengkapan);
                    $('[name=tanggal_pembelian]').val(res.tanggal_pembelian);

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

                        let input = '';

                        // NUMBER
                        if (attr.tipe_input === 'number') {
                            input =
                                `<input type="number" name="atribut[${attr.id}]" class="form-control">`;
                        }

                        // SELECT (dari kolom OPSI)
                        else if (attr.tipe_input === 'select') {

                            let optionsHtml = `<option value="">-- Pilih --</option>`;

                            if (attr.opsi) {
                                attr.opsi.split(',').forEach(item => {

                                    // cek format label|value
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

                        $('#atribut-area').append(`
                           <div class="form-group row">
                                <p class="col-md-2">
                                    ${attr.nama_atribut}
                                </p>
                                <div class="col-md-10">
                                    ${input}
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
                defaultDate: "today",
                locale: "id",
                onReady: function(selectedDates, dateStr, instance) {
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

            function showQrModal(kode) {
                $('#qrContainer').html('');
                $('#kodeAset').text(kode);

                new QRCode(document.getElementById("qrContainer"), {
                    text: kode,
                    width: 200,
                    height: 200
                });

                $('#qrModal').modal('show');
            }
        </script>
    @endpush
