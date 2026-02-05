<?php

namespace App\Http\Controllers;

use App\Models\Tipe;
use Illuminate\Http\Request;

class TipeController extends Controller
{
    public function index()
    {
        return view('tipe.index');
    }

    public function data()
    {
        $tipe = Tipe::orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($tipe)
            ->addIndexColumn()
            ->addColumn('aksi', function ($tipe) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('tipe.update', $tipe->id) . '`)" class="btn btn-sm btn-info btn-flat"><i class="fa fa-pen"></i></button>
                     <button type="button" onclick="deleteData(`' . route('tipe.destroy', $tipe->id) . '`)" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $tipe = new Tipe();
        $tipe->nama = $request->nama;
        $tipe->kode = $request->kode;
        $tipe->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function show($id)
    {
        $tipe = Tipe::find($id);

        return response()->json($tipe);
    }

    public function update(Request $request, $id)
    {
        $tipe = Tipe::find($id);
        $tipe->nama = $request->nama;
        $tipe->kode = $request->kode;
        $tipe->update();

        return response()->json('Data berhasil diubah', 200);
    }

    public function destroy($id)
    {
        $tipe = Tipe::find($id);
        $tipe->delete();

        return response()->json('Data berhasil dihapus', 200);
    }
}
