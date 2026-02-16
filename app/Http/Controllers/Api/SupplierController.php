<?php

namespace App\Http\Controllers\Api;

use App\Models\Supplier;
use App\Http\Resources\SupplierResource;
use Illuminate\Http\Request;

class SupplierController extends ApiController
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'id_supplier');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $supplier = $query->paginate($perPage);

        return $this->paginatedResponse($supplier->through(fn ($item) => new SupplierResource($item)));
    }

    public function show($id)
    {
        $supplier = Supplier::find($id);
        if (! $supplier) {
            return $this->errorResponse('Supplier tidak ditemukan', 404);
        }

        return $this->successResponse(new SupplierResource($supplier));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat'  => 'nullable|string|max:500',
        ]);

        $supplier = Supplier::create($request->only(['nama', 'telepon', 'alamat']));

        return $this->successResponse(new SupplierResource($supplier), 'Supplier berhasil ditambahkan', 201);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        if (! $supplier) {
            return $this->errorResponse('Supplier tidak ditemukan', 404);
        }

        $request->validate([
            'nama'    => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat'  => 'nullable|string|max:500',
        ]);

        $supplier->update($request->only(['nama', 'telepon', 'alamat']));

        return $this->successResponse(new SupplierResource($supplier), 'Supplier berhasil diperbarui');
    }

    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        if (! $supplier) {
            return $this->errorResponse('Supplier tidak ditemukan', 404);
        }

        $supplier->delete();

        return $this->successResponse(null, 'Supplier berhasil dihapus');
    }
}
