<?php

namespace App\Http\Controllers\Api;

use App\Models\Kategori;
use App\Http\Resources\KategoriResource;
use Illuminate\Http\Request;

class KategoriController extends ApiController
{
    public function index(Request $request)
    {
        $query = Kategori::query();

        if ($request->filled('search')) {
            $query->where('nama_kategori', 'like', '%' . $request->search . '%');
        }

        $sortBy = $request->get('sort_by', 'id_kategori');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $kategori = $query->paginate($perPage);

        return $this->paginatedResponse($kategori->through(fn ($item) => new KategoriResource($item)));
    }

    public function show($id)
    {
        $kategori = Kategori::find($id);
        if (! $kategori) {
            return $this->errorResponse('Kategori tidak ditemukan', 404);
        }

        return $this->successResponse(new KategoriResource($kategori));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = Kategori::create($request->only('nama_kategori'));

        return $this->successResponse(new KategoriResource($kategori), 'Kategori berhasil ditambahkan', 201);
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);
        if (! $kategori) {
            return $this->errorResponse('Kategori tidak ditemukan', 404);
        }

        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori->update($request->only('nama_kategori'));

        return $this->successResponse(new KategoriResource($kategori), 'Kategori berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kategori = Kategori::find($id);
        if (! $kategori) {
            return $this->errorResponse('Kategori tidak ditemukan', 404);
        }

        $kategori->delete();

        return $this->successResponse(null, 'Kategori berhasil dihapus');
    }
}
