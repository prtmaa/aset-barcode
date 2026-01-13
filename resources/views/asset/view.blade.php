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
                <div class="col-md-5 text-center">
                    @if ($asset->foto)
                        <img src="{{ asset($asset->foto) }}" class="img-thumbnail mb-2" width="400">
                    @else
                        <span class="text-muted">Tidak ada foto</span>
                    @endif
                </div>

                {{-- DATA --}}
                <div class="col-md-7">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Kode Aset</th>
                            <td>{{ $asset->kode_aset }}</td>
                        </tr>
                        <tr>
                            <th>Nama Aset</th>
                            <td>{{ $asset->nama_aset }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $asset->kategori->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi</th>
                            <td>{{ $asset->lokasi->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pembelian</th>
                            <td>
                                {{ formatTanggalIndo($asset->tanggal_pembelian) }}
                                <small class="text-muted d-block">
                                    <i class="fas fa-clock"></i> {{ usiaSejak($asset->tanggal_pembelian) }} sejak pembelian
                                </small>

                            </td>
                        </tr>

                        <tr>
                            <th>Pengguna</th>
                            <td>
                                @if ($asset->activeAssignment)
                                    <b>{{ $asset->activeAssignment->employee->nama }}</b><br>
                                    <small class="text-muted">
                                        {{ $asset->activeAssignment->employee->jabatan ?? '-' }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
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
                                    {{ $val->nilai }}
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
                        <ul class="pl-3 mb-0">
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
