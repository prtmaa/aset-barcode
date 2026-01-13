<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employe.index');
    }

    public function data()
    {
        $employe = Employee::orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($employe)
            ->addIndexColumn()
            ->addColumn('aksi', function ($employe) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('employe.update', $employe->id) . '`)" class="btn btn-sm btn-info btn-flat"><i class="fa fa-pen"></i></button>
                     <button type="button" onclick="deleteData(`' . route('employe.destroy', $employe->id) . '`)" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $employe = new Employee();
        $employe->nama = $request->nama;
        $employe->email = $request->email;
        $employe->jabatan = $request->jabatan;
        $employe->departemen = $request->departemen;
        $employe->no_hp = $request->no_hp;
        $employe->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function show($id)
    {
        $employe = Employee::find($id);

        return response()->json($employe);
    }

    public function update(Request $request, $id)
    {
        $employe = Employee::find($id);
        $employe->nama = $request->nama;
        $employe->email = $request->email;
        $employe->jabatan = $request->jabatan;
        $employe->departemen = $request->departemen;
        $employe->no_hp = $request->no_hp;
        $employe->update();

        return response()->json('Data berhasil diubah', 200);
    }

    public function destroy($id)
    {
        $employe = Employee::find($id);
        $employe->delete();

        return response()->json('Data berhasil dihapus', 200);
    }
}
