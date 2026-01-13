<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        return view('scan.index');
    }

    public function process(Request $request)
    {
        $request->validate([
            'kode_aset' => 'required'
        ]);

        $asset = Asset::where('kode_aset', $request->kode_aset)->first();

        if (!$asset) {
            return redirect()
                ->route('scan.index')
                ->with('error', 'Aset tidak ditemukan');
        }

        return redirect()->route('asset.view', $asset->id);
    }
}
