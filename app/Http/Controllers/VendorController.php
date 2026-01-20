<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        return view('vendor.index');
    }

    public function data()
    {
        $vendor = Vendor::orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($vendor)
            ->addIndexColumn()
            ->addColumn('aksi', function ($vendor) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('vendor.update', $vendor->id) . '`)" class="btn btn-sm btn-info btn-flat"><i class="fa fa-pen"></i></button>
                     <button type="button" onclick="deleteData(`' . route('vendor.destroy', $vendor->id) . '`)" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $vebdor = new Vendor();
        $vebdor->nama = $request->nama;
        $vebdor->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function show($id)
    {
        $vebdor = Vendor::find($id);

        return response()->json($vebdor);
    }

    public function update(Request $request, $id)
    {
        $vebdor = Vendor::find($id);
        $vebdor->nama = $request->nama;
        $vebdor->update();

        return response()->json('Data berhasil diubah', 200);
    }

    public function destroy($id)
    {
        $vebdor = Vendor::find($id);
        $vebdor->delete();

        return response()->json('Data berhasil dihapus', 200);
    }
}
