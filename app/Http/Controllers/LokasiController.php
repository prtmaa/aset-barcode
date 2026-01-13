<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        return view('lokasi.index');
    }

    public function data()
    {
        $lokasi = Lokasi::orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($lokasi)
            ->addIndexColumn()
            ->addColumn('aksi', function ($lokasi) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('lokasi.update', $lokasi->id) . '`)" class="btn btn-sm btn-info btn-flat"><i class="fa fa-pen"></i></button>
                     <button type="button" onclick="deleteData(`' . route('lokasi.destroy', $lokasi->id) . '`)" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $lokasi = new Lokasi();
        $lokasi->nama = $request->nama;
        $lokasi->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function show($id)
    {
        $lokasi = Lokasi::find($id);

        return response()->json($lokasi);
    }

    public function update(Request $request, $id)
    {
        $lokasi = Lokasi::find($id);
        $lokasi->nama = $request->nama;
        $lokasi->update();

        return response()->json('Data berhasil diubah', 200);
    }

    public function destroy($id)
    {
        $lokasi = Lokasi::find($id);
        $lokasi->delete();

        return response()->json('Data berhasil dihapus', 200);
    }
}
