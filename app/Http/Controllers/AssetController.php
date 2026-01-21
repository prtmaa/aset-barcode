<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetAttribute;
use App\Models\AssetAttributeValue;
use App\Models\Employee;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Tipe;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class AssetController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        $lokasi = Lokasi::all();
        $employee = Employee::all();
        $tipe = Tipe::all();
        $vendor = Vendor::all();

        return view('asset.index', compact('kategori', 'lokasi', 'employee', 'tipe', 'vendor'));
    }

    public function data()
    {
        $asset = Asset::with([
            'kategori',
            'lokasi',
            'tipe',
            'vendor',
            'atributValues.atribut',
            'activeAssignment.employee'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return datatables()
            ->of($asset)
            ->addIndexColumn()

            ->addColumn('lokasi', fn($asset) => $asset->lokasi->nama ?? '-')
            ->addColumn('tipe', fn($asset) => $asset->tipe->nama ?? '-')
            ->addColumn('vendor', fn($asset) => $asset->vendor->nama ?? '-')
            ->addColumn('harga', fn($asset) => formatRupiah($asset->harga) ?? '-')
            // ->addColumn('atribut', function ($asset) {

            //     if (!$asset->atributValues || $asset->atributValues->isEmpty()) {
            //         return '<div class="text-left"><i class="text-muted">-</i></div>';
            //     }

            //     $html = '<div class="text-left">';
            //     $html .= '<ul class="pl-3 mb-0">';
            //     foreach ($asset->atributValues as $val) {

            //         $nama = optional($val->atribut)->nama_atribut ?? '-';

            //         $html .= '<li><b>'
            //             . e($nama)
            //             . ':</b> '
            //             . e($val->nilai)
            //             . '</li>';
            //     }
            //     $html .= '</ul></div>';

            //     return $html;
            // })
            ->addColumn('tanggal_pembelian', function ($asset) {

                if (!$asset->tanggal_pembelian) {
                    return '<span class="text-muted">-</span>';
                }

                return
                    e(formatTanggalIndo($asset->tanggal_pembelian)) .
                    '<small class="text-muted d-block">
                      <i class="fas fa-clock"></i> ' . e(usiaSejak($asset->tanggal_pembelian)) . '
                    </small>';
            })
            ->addColumn('pengguna', function ($asset) {

                if (!$asset->activeAssignment) {
                    return '';
                }

                $emp = $asset->activeAssignment->employee;

                return '<b>' . e($emp->nama) . '</b><br>
                        <small class="text-muted">' . e($emp->jabatan ?? '') . '</br>' . e($emp->departemen ?? '') . '</small>';
            })
            ->addColumn('aksi', function ($asset) {

                $btnFoto = '';

                if (!empty($asset->foto)) {
                    $btnFoto = '
                        <button type="button" class="btn btn-sm btn-success btn-flat"
                            onclick="showFotoModal(`' . asset($asset->foto) . '`)">
                            <i class="fas fa-image"></i>
                        </button>
                    ';
                } else {
                    $btnFoto = '
                        <button type="button" class="btn btn-sm btn-secondary btn-flat"
                            onclick="showFotoModal(null)">
                            <i class="fas fa-image"></i>
                        </button>
                    ';
                }

                $spesifikasi = '';

                if ($asset->atributValues && $asset->atributValues->count()) {
                    foreach ($asset->atributValues as $val) {
                        $nama = optional($val->atribut)->nama_atribut ?? '-';
                        $satuan = optional($val->atribut)->satuan ?? '';
                        $spesifikasi .= $nama . ': ' . $val->nilai . " " . $satuan . "\n";
                    }
                }

                return '
                    <div class="btn-group mb-1">
                        <button type="button" onclick="editForm(`' . route('asset.update', $asset->id) . '`)"
                            class="btn btn-sm btn-info btn-flat">
                            <i class="fa fa-pen"></i>
                        </button>
                        <button type="button" onclick="deleteData(`' . route('asset.destroy', $asset->id) . '`)"
                            class="btn btn-sm btn-danger btn-flat">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                    <div class="btn-group">
                        ' . $btnFoto . '
                        <button type="button" class="btn btn-sm btn-warning btn-flat"
                            onclick="showQrModal(`' . e($asset->kode_aset) . '`)">
                            <i class="fas fa-qrcode"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary btn-detail btn-flat"
                            data-spesifikasi="' . e(trim($spesifikasi)) . '"
                            data-kelengkapan="' . e($asset->kelengkapan) . '"
                            data-kategori="' . e($asset->kategori->nama) . '"
                            data-catatan="' . e($asset->catatan) . '">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['atribut', 'aksi', 'pengguna', 'tanggal_pembelian'])
            ->make(true);
    }

    //     $url = url('/scan/' . $asset->kode_aset);

    //     $qr = QrCode::size(80)
    //         ->margin(1)
    //         ->generate($url);


    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            // AMBIL DATA UTAMA
            $data = $request->except([
                'atribut',
                'is_assign',
                'employee_id',
                'tanggal_mulai',
                'keterangan',
                'foto'
            ]);

            // NORMALISASI HARGA
            if ($request->filled('harga')) {
                $data['harga'] = (float) str_replace(['.', ','], ['', '.'], $request->harga);
            }

            // UPLOAD FOTO
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $nama = 'aset_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/foto'), $nama);
                $data['foto'] = 'assets/foto/' . $nama;
            }

            // SIMPAN ASSET
            $asset = Asset::create($data);

            // SIMPAN ATRIBUT DINAMIS
            if ($request->filled('atribut')) {
                $insert = [];

                foreach ($request->atribut as $attributeId => $nilai) {
                    if (blank($nilai)) continue;

                    $insert[] = [
                        'asset_id' => $asset->id,
                        'asset_attribute_id' => $attributeId,
                        'nilai' => $nilai,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($insert)) {
                    AssetAttributeValue::insert($insert);
                }
            }

            // ASSIGN KE KARYAWAN
            if ($request->boolean('is_assign') && $request->filled('employee_id')) {
                AssetAssignment::create([
                    'asset_id' => $asset->id,
                    'employee_id' => $request->employee_id,
                    'tanggal_mulai' => $request->tanggal_mulai ?? now(),
                    'keterangan' => $request->keterangan,
                    'status' => 'aktif'
                ]);
            }
        });

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }



    public function show($id)
    {
        $asset = Asset::with([
            'atributValues.atribut',
            'assignmentAktif.employee'
        ])->findOrFail($id);

        return response()->json($asset);
    }


    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $asset = Asset::findOrFail($id);

            // DATA UTAMA
            $data = $request->except([
                'atribut',
                'is_assign',
                'employee_id',
                'tanggal_mulai',
                'keterangan',
                'foto'
            ]);

            // NORMALISASI HARGA
            if ($request->filled('harga')) {
                $data['harga'] = (float) str_replace(['.', ','], ['', '.'], $request->harga);
            }

            // FOTO
            if ($request->hasFile('foto')) {

                if ($asset->foto && file_exists(public_path($asset->foto))) {
                    unlink(public_path($asset->foto));
                }

                $file = $request->file('foto');
                $nama = 'aset_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/foto'), $nama);

                $data['foto'] = 'assets/foto/' . $nama;
            }

            // UPDATE ASSET
            $asset->update($data);

            // ATRIBUT DINAMIS (REPLACE)
            AssetAttributeValue::where('asset_id', $asset->id)->delete();

            if ($request->filled('atribut')) {
                $insert = [];

                foreach ($request->atribut as $attributeId => $nilai) {
                    if (blank($nilai)) continue;

                    $insert[] = [
                        'asset_id' => $asset->id,
                        'asset_attribute_id' => $attributeId,
                        'nilai' => $nilai,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($insert)) {
                    AssetAttributeValue::insert($insert);
                }
            }

            // ASSIGNMENT
            $assignment = AssetAssignment::where('asset_id', $asset->id)
                ->where('status', 'aktif')
                ->first();

            if ($request->boolean('is_assign') && $request->filled('employee_id')) {

                if ($assignment) {
                    // update assignment aktif
                    $assignment->update([
                        'employee_id' => $request->employee_id,
                        'tanggal_mulai' => $request->tanggal_mulai ?? $assignment->tanggal_mulai,
                        'keterangan' => $request->keterangan,
                    ]);
                } else {
                    // buat assignment baru
                    AssetAssignment::create([
                        'asset_id' => $asset->id,
                        'employee_id' => $request->employee_id,
                        'tanggal_mulai' => $request->tanggal_mulai ?? now(),
                        'keterangan' => $request->keterangan,
                        'status' => 'aktif'
                    ]);
                }
            } else {
                // checkbox dilepas → tutup assignment lama
                if ($assignment) {
                    $assignment->update([
                        'status' => 'selesai',
                        'tanggal_selesai' => now()
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Data berhasil diubah'], 200);
    }



    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $asset = Asset::findOrFail($id);

            // HAPUS FOTO
            if ($asset->foto && file_exists(public_path($asset->foto))) {
                unlink(public_path($asset->foto));
            }

            // HAPUS ASSIGNMENT (jika belum cascade)
            AssetAssignment::where('asset_id', $asset->id)->delete();

            // HAPUS ATRIBUT DINAMIS
            AssetAttributeValue::where('asset_id', $asset->id)->delete();

            // HAPUS ASSET
            $asset->delete();
        });

        return response()->json([
            'message' => 'Asset berhasil dihapus'
        ], 200);
    }

    public function atributByKategori($kategoriId)
    {
        return AssetAttribute::where('kategori_id', $kategoriId)->get();
    }



    public function view($id)
    {
        $asset = Asset::with([
            'atributValues.atribut',
            'assignmentAktif.employee'
        ])->findOrFail($id);

        return view('asset.view', compact('asset'));
    }

    // public function scan($kode)
    // {
    //     $asset = Asset::where('kode_aset', $kode)->firstOrFail();

    //     return redirect()->route('asset.view', $asset->id);
    // }
}
