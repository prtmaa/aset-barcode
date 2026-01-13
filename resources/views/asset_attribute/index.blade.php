@extends('layouts.master')

@section('tittle', 'Data Spesifikasi Aset')

@section('breadcrumb')
    <li class="breadcrumb-item"> <a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Master</li>
    <li class="breadcrumb-item active">Spesifikasi</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header text-right">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 mb-3 mb-md-0">
                        <select name="kategori_id" id="filter-kategori" class="form-control form-control-sm"
                            style="min-width: 220px;">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($kategoris as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button onclick="addForm('{{ route('assetattribute.store') }}')"
                        class="btn btn-primary btn-sm align-self-md-center align-self-start">
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
                            <th>Kategori</th>
                            <th>Nama Atribut</th>
                            <th>Tipe Atribut</th>
                            <th>Opsi</th>
                            <th>Aksi</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    @include('asset_attribute.form')
@endsection

@push('js')
    <script>
        let table;
        $(document).ready(function() {

            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                searching: false,
                order: [
                    [2, 'asc']
                ],
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
                    url: '{{ route('assetattribute.data') }}',
                    data: function(d) {
                        d.kategori_id = $('#filter-kategori').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kategori',
                        name: 'kategori.nama'
                    },
                    {
                        data: 'nama_atribut',
                        name: 'nama_atribut'
                    },
                    {
                        data: 'tipe_input',
                        name: 'tipe_input'
                    },
                    {
                        data: 'opsi',
                        name: 'opsi'
                    },
                    {
                        data: 'aksi',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // FILTER KATEGORI
            $('#filter-kategori').on('change', function() {
                table.ajax.reload();
            });

            // SUBMIT FORM MODAL
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

            // TIPE INPUT
            $(document).on('change', '.tipe-input', function() {
                $('.opsi-input').toggleClass('d-none', $(this).val() !== 'select');
            });

        });

        // MODAL TAMBAH
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
            $('#modal-form [name=nama_atribut]').focus();

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=kategori_id]').val(response.kategori_id);
                    $('#modal-form [name=nama_atribut]').val(response.nama_atribut);
                    $('#modal-form [name=tipe_input]').val(response.tipe_input);
                    $('#modal-form [name=opsi]').val(response.opsi);
                })
                .fail((errors) => {
                    Swal.fire({
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        iconColor: '#dc3545',
                        title: 'Oops...',
                        text: 'Data gagal ditampilkan',
                    })
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
    </script>
@endpush
