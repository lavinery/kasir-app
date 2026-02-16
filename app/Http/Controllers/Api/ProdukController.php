<?php

namespace App\Http\Controllers\Api;

use App\Models\Produk;
use App\Http\Resources\ProdukResource;
use Illuminate\Http\Request;

class ProdukController extends ApiController
{
    public function index(Request $request)
    {
        $query = Produk::query();

        // Search by nama_produk or kode_produk
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        // Include relations
        if ($request->filled('include')) {
            $includes = array_map('trim', explode(',', $request->include));
            $allowed = ['kategori'];
            $query->with(array_intersect($includes, $allowed));
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id_produk');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $produk = $query->paginate($perPage);

        return $this->paginatedResponse($produk->through(fn ($item) => new ProdukResource($item)));
    }

    public function show(Request $request, $id)
    {
        $query = Produk::query();

        if ($request->filled('include')) {
            $includes = array_map('trim', explode(',', $request->include));
            $allowed = ['kategori'];
            $query->with(array_intersect($includes, $allowed));
        }

        $produk = $query->find($id);
        if (! $produk) {
            return $this->errorResponse('Produk tidak ditemukan', 404);
        }

        return $this->successResponse(new ProdukResource($produk));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk'  => 'required|string|max:255',
            'id_kategori'  => 'required|integer|exists:kategori,id_kategori',
            'harga_beli'   => 'required|numeric|min:0|max:999999999',
            'harga_jual'   => 'required|numeric|min:0|max:999999999',
            'stok'         => 'required|integer|min:0|max:999999',
            'diskon'       => 'nullable|integer|min:0|max:100',
            'kode_produk'  => 'nullable|string|max:50|unique:produk,kode_produk',
        ]);

        $data = $request->all();

        if (! $request->filled('kode_produk')) {
            $produkTerakhir = Produk::latest()->first();
            $lastId = $produkTerakhir ? $produkTerakhir->id_produk : 0;
            $data['kode_produk'] = 'P' . tambah_nol_didepan($lastId + 1, 6);
        }

        $data['keuntungan'] = $data['harga_jual'] - $data['harga_beli'];

        $produk = Produk::create($data);
        $produk->load('kategori');

        return $this->successResponse(new ProdukResource($produk), 'Produk berhasil ditambahkan', 201);
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        if (! $produk) {
            return $this->errorResponse('Produk tidak ditemukan', 404);
        }

        $request->validate([
            'nama_produk'  => 'required|string|max:255',
            'id_kategori'  => 'required|integer|exists:kategori,id_kategori',
            'harga_beli'   => 'required|numeric|min:0|max:999999999',
            'harga_jual'   => 'required|numeric|min:0|max:999999999',
            'stok'         => 'required|integer|min:0|max:999999',
            'diskon'       => 'nullable|integer|min:0|max:100',
            'kode_produk'  => 'nullable|string|max:50|unique:produk,kode_produk,' . $id . ',id_produk',
        ]);

        $data = $request->all();
        $data['keuntungan'] = $data['harga_jual'] - $data['harga_beli'];

        $produk->update($data);
        $produk->load('kategori');

        return $this->successResponse(new ProdukResource($produk), 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $produk = Produk::find($id);
        if (! $produk) {
            return $this->errorResponse('Produk tidak ditemukan', 404);
        }

        $produk->delete();

        return $this->successResponse(null, 'Produk berhasil dihapus');
    }
}
