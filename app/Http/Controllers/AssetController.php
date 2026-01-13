<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetAttribute;
use App\Models\AssetAttributeValue;
use App\Models\Employee;
use App\Models\Kategori;
use App\Models\Lokasi;
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

        return view('asset.index', compact('kategori', 'lokasi', 'employee'));
    }

    public function data()
    {
        $asset = Asset::with([
            'kategori',
            'lokasi',
            'atributValues.atribut',
            'activeAssignment.employee'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return datatables()
            ->of($asset)
            ->addIndexColumn()

            ->addColumn('kategori', fn($asset) => $asset->kategori->nama ?? '-')
            ->addColumn('lokasi', fn($asset) => $asset->lokasi->nama ?? '-')
            ->addColumn('atribut', function ($asset) {

                if (!$asset->atributValues || $asset->atributValues->isEmpty()) {
                    return '<div class="text-left"><i class="text-muted">-</i></div>';
                }

                $html = '<div class="text-left">';
                $html .= '<ul class="pl-3 mb-0">';
                foreach ($asset->atributValues as $val) {

                    $nama = optional($val->atribut)->nama_atribut ?? '-';

                    $html .= '<li><b>'
                        . e($nama)
                        . ':</b> '
                        . e($val->nilai)
                        . '</li>';
                }
                $html .= '</ul></div>';

                return $html;
            })
            ->addColumn('tanggal_pembelian', function ($asset) {

                if (!$asset->tanggal_pembelian) {
                    return '<span class="text-muted">-</span>';
                }

                return
                    e(formatTanggalIndo($asset->tanggal_pembelian)) .
                    '<small class="text-muted d-block">
                        ' . e(usiaSejak($asset->tanggal_pembelian)) . ' sejak pembelian
                    </small>';
            })
            ->addColumn('pengguna', function ($asset) {

                if (!$asset->activeAssignment) {
                    return '<p>-</p>';
                }

                $emp = $asset->activeAssignment->employee;

                return '<b>' . e($emp->nama) . '</b><br>
                        <small class="text-muted">' . e($emp->jabatan ?? '-') . '</small>';
            })

            ->addColumn('qr_code', function ($asset) {

                return '
                        <button type="button" class="btn btn-sm btn-outline-success"
                            onclick="showQrModal(`' . e($asset->kode_aset) . '`)">
                            <i class="fas fa-qrcode"></i> QR
                        </button>
                    ';
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

            // SIMPAN ASSET
            $data = $request->except(['atribut', 'is_assign', 'employee_id', 'tanggal_mulai', 'keterangan']);

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $nama = 'aset_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/foto'), $nama);
                $data['foto'] = 'assets/foto/' . $nama;
            }

            $asset = Asset::create($data);

            // SIMPAN ATRIBUT DINAMIS
            if ($request->filled('atribut')) {
                $insert = [];

                foreach ($request->atribut as $attributeId => $nilai) {
                    if ($nilai === null || $nilai === '') continue;

                    $insert[] = [
                        'asset_id' => $asset->id,
                        'asset_attribute_id' => $attributeId,
                        'nilai' => $nilai,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                AssetAttributeValue::insert($insert);
            }

            // ASSIGN
            if ($request->is_assign && $request->employee_id) {
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
                'keterangan'
            ]);

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

            $asset->update($data);

            // ATRIBUT DINAMIS
            AssetAttributeValue::where('asset_id', $asset->id)->delete();

            if ($request->filled('atribut')) {
                $insert = [];

                foreach ($request->atribut as $attributeId => $nilai) {
                    if ($nilai === null || $nilai === '') continue;

                    $insert[] = [
                        'asset_id' => $asset->id,
                        'asset_attribute_id' => $attributeId,
                        'nilai' => $nilai,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($insert) {
                    AssetAttributeValue::insert($insert);
                }
            }

            // ASSIGNMENT (UPDATE LOGIC)
            $assignment = AssetAssignment::where('asset_id', $asset->id)
                ->where('status', 'aktif')
                ->first();

            // jika checkbox dicentang
            if ($request->is_assign && $request->employee_id) {

                if ($assignment) {
                    // update assignment aktif
                    $assignment->update([
                        'employee_id' => $request->employee_id,
                        'tanggal_mulai' => $request->tanggal_mulai,
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
