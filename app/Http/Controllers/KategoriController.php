<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        return view('kategori.index');
    }

    public function data()
    {
        $kategori = Kategori::orderBy('id_kategori', 'desc')->get();

        return datatables()
            ->of($kategori)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {
                return '
                <div class="btn-group">
                    <button onclick="editForm(`' . route('kategori.show', $kategori->id_kategori) . '`)" class="btn btn-xs btn-info btn-flat" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </button>
                    <button onclick="deleteData(`' . route('kategori.destroy', $kategori->id_kategori) . '`)" class="btn btn-xs btn-danger btn-flat" title="Hapus">
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
            'nama_kategori' => 'required|string|max:255',
        ], [
            'nama_kategori.required' => 'Nama kategori harus diisi',
        ]);

        Kategori::create($validated);

        // 204 No Content → cocok untuk AJAX .done() tanpa JSON
        return response()->noContent();
    }

    public function show($id)
    {
        $kategori = Kategori::find($id);
        if (!$kategori) {
            return response()->json(['error' => 'Kategori tidak ditemukan'], 404);
        }
        // Perlu JSON untuk prefill form edit
        return response()->json($kategori);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ], [
            'nama_kategori.required' => 'Nama kategori harus diisi',
        ]);

        $kategori = Kategori::find($id);
        if (!$kategori) {
            return response()->json(['error' => 'Kategori tidak ditemukan'], 404);
        }

        $kategori->update($validated);

        // 204 No Content
        return response()->noContent();
    }

    public function destroy($id)
    {
        $kategori = Kategori::find($id);
        if (!$kategori) {
            return response()->json(['error' => 'Kategori tidak ditemukan'], 404);
        }

        $kategori->delete();

        // 204 No Content
        return response()->noContent();
    }
}
