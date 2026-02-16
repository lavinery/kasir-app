<?php

namespace App\Http\Controllers\Api;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Http\Resources\PembelianResource;
use Illuminate\Http\Request;

class PembelianController extends ApiController
{
    public function index(Request $request)
    {
        $query = Pembelian::query();

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        // Filter by supplier
        if ($request->filled('id_supplier')) {
            $query->where('id_supplier', $request->id_supplier);
        }

        // Include relations
        if ($request->filled('include')) {
            $includes = array_map('trim', explode(',', $request->include));
            $allowed = ['supplier'];
            $query->with(array_intersect($includes, $allowed));
        }

        $sortBy = $request->get('sort_by', 'id_pembelian');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $pembelian = $query->paginate($perPage);

        return $this->paginatedResponse($pembelian->through(fn ($item) => new PembelianResource($item)));
    }

    public function show(Request $request, $id)
    {
        $query = Pembelian::with(['supplier']);

        if ($request->get('include') && str_contains($request->include, 'detail')) {
            $query->with(['detail' => function ($q) {
                $q->with('produk');
            }]);
        }

        $pembelian = $query->find($id);
        if (! $pembelian) {
            return $this->errorResponse('Pembelian tidak ditemukan', 404);
        }

        if (! $pembelian->relationLoaded('detail')) {
            $detail = PembelianDetail::with('produk')->where('id_pembelian', $id)->get();
            $pembelian->setRelation('detail', $detail);
        }

        return $this->successResponse(new PembelianResource($pembelian));
    }
}
