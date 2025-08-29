<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        return view('supplier.index');
    }

    public function data()
    {
        $supplier = Supplier::orderBy('id_supplier', 'desc')->get();

        return datatables()
            ->of($supplier)
            ->addIndexColumn()
            ->addColumn('aksi', function ($supplier) {
                // pakai supplier.show untuk GET prefill, dan supplier.destroy untuk hapus
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('supplier.show', $supplier->id_supplier) . '`)" class="btn btn-xs btn-info btn-flat" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </button>
                    <button type="button" onclick="deleteData(`' . route('supplier.destroy', $supplier->id_supplier) . '`)" class="btn btn-xs btn-danger btn-flat" title="Hapus">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'    => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat'  => 'nullable|string|max:500',
        ], [
            'nama.required'    => 'Nama supplier harus diisi',
            'telepon.required' => 'Nomor telepon harus diisi',
        ]);

        Supplier::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier berhasil ditambahkan!'
        ], 200);
    }

    public function show($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json(['error' => 'Supplier tidak ditemukan'], 404);
        }

        // show perlu JSON untuk prefill form edit
        return response()->json($supplier);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama'    => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat'  => 'nullable|string|max:500',
        ], [
            'nama.required'    => 'Nama supplier harus diisi',
            'telepon.required' => 'Nomor telepon harus diisi',
        ]);

        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json(['error' => 'Supplier tidak ditemukan'], 404);
        }

        $supplier->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier berhasil diperbarui!'
        ], 200);
    }

    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier tidak ditemukan!'
            ], 404);
        }

        $namaSupplier = $supplier->nama;
        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => "Supplier '{$namaSupplier}' berhasil dihapus!"
        ], 200);
    }
}
