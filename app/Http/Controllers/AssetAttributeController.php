<?php

namespace App\Http\Controllers;

use App\Models\AssetAttribute;
use App\Models\Kategori;
use Illuminate\Http\Request;

class AssetAttributeController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::orderBy('nama')->get();

        return view('asset_attribute.index', compact('kategoris'));
    }

    public function data(Request $request)
    {
        $query = AssetAttribute::with('kategori')
            ->orderBy('asset_attributes.created_at', 'desc');

        if ($request->kategori_id) {
            $query->where('asset_attributes.kategori_id', $request->kategori_id);
        }

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('kategori', function ($row) {
                return $row->kategori->nama ?? '-';
            })
            ->addColumn('aksi', function ($row) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('assetattribute.update', $row->id) . '`)"
                        class="btn btn-sm btn-info"><i class="fa fa-pen"></i></button>

                    <button type="button" onclick="deleteData(`' . route('assetattribute.destroy', $row->id) . '`)"
                        class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                </div>
            ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function store(Request $request)
    {
        $request->validate([
            'kategori_id'  => 'required',
            'nama_atribut' => 'required',
            'tipe_input'   => 'required',
        ]);

        AssetAttribute::create([
            'kategori_id'  => $request->kategori_id,
            'nama_atribut' => $request->nama_atribut,
            'tipe_input'   => $request->tipe_input,
            'opsi'         => $request->opsi,
            'satuan'       => $request->satuan,
        ]);

        return back()->with('success', 'Atribut berhasil ditambahkan');
    }

    public function show($id)
    {
        return response()->json(AssetAttribute::find($id));
    }

    public function update(Request $request, $id)
    {
        $asset = AssetAttribute::find($id);

        $data = $request->all();

        $asset->update($data);
        return response()->json('Data berhasil diubah', 200);
    }

    public function destroy($id)
    {
        $asset = AssetAttribute::find($id);
        $asset->delete();

        return response()->json('Data berhasil diubah', 200);
    }
}
