@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h5>Detail Aset</h5>
                </div>

                <a href="{{ route('scan.index') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-back"></i> kembali
                </a>
            </div>

        </div>

        <div class="card-body">
            <div class="row">

                {{-- FOTO --}}
                <div class="col-md-4 text-center">
                    @if ($asset->foto)
                        <img src="{{ asset($asset->foto) }}" class="img-thumbnail mb-2" width="400">
                    @else
                        <span class="text-muted">Tidak ada foto</span>
                    @endif
                </div>

                {{-- DATA --}}
                <div class="col-md-4">
                    <table class="table table-bordered">
                        <tr>
                            <th width="130">Kode Aset</th>
                            <td>{{ $asset->kode_aset }}</td>
                        </tr>
                        <tr>
                            <th>Nama Aset</th>
                            <td>{{ $asset->nama_aset }}</td>
                        </tr>
                        <tr>
                            <th>Tipe Aset</th>
                            <td>{{ $asset->tipe->nama }}</td>
                        </tr>
                        <tr>
                            <th>PIC/Divisi</th>
                            <td>
                                @if ($asset->activeAssignment)
                                    {{ $asset->activeAssignment->employee->nama }}
                                    <small class="text-muted d-block">
                                        {{ $asset->activeAssignment->employee->jabatan . ' ' . $asset->activeAssignment->employee->depertemen ?? '-' }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Lokasi</th>
                            <td>{{ $asset->lokasi->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Vendor</th>
                            <td>{{ $asset->vendor->nama }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-4">
                    <table class="table table-bordered">
                        <tr>
                            <th width="130">Kategori</th>
                            <td>{{ $asset->kategori->nama ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Jumlah</th>
                            <td>{{ $asset->jumlah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nilai Perolehan</th>
                            <td>{{ formatRupiah($asset->harga) ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tgl Perolehan</th>
                            <td>
                                {{ formatTanggalIndo($asset->tanggal_pembelian) }}
                                <small class="text-muted d-block">
                                    <i class="fas fa-clock"></i> {{ usiaSejak($asset->tanggal_pembelian) }} sejak pembelian
                                </small>

                            </td>
                        </tr>
                        <tr>
                            <th>Kondisi</th>
                            <td>
                                {{ $asset->kondisi ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                {{ $asset->status ?? '-' }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- ATRIBUT --}}
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6>Spesifikasi Aset</h6>

                    @if ($asset->atributValues->isEmpty())
                        <i class="text-muted">Tidak ada Spesifikasi</i>
                    @else
                        <ul>
                            @foreach ($asset->atributValues as $val)
                                <li>
                                    <b>{{ $val->atribut->nama_atribut ?? '-' }}:</b>
                                    {{ $val->nilai }} {{ $val->atribut->satuan ?? '' }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6>Kelengkapan</h6>

                    @if (empty($asset->kelengkapan))
                        <i class="text-muted">Tidak ada kelengkapan</i>
                    @else
                        <ul>
                            @foreach (explode(',', $asset->kelengkapan) as $item)
                                <li>{{ trim($item) }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>


            <hr>
            <h6>Catatan</h6>
            @if (empty($asset->catatan))
                <i class="text-muted">Tidak ada Catatan</i>
            @else
                {{ $asset->catatan }}
            @endif

        </div>
    </div>
@endsection
