<?php

namespace App\Http\Controllers\Api;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Http\Resources\PenjualanResource;
use Illuminate\Http\Request;

class PenjualanController extends ApiController
{
    public function index(Request $request)
    {
        $query = Penjualan::query();

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        // Filter by member
        if ($request->filled('id_member')) {
            $query->where('id_member', $request->id_member);
        }

        // Include relations
        if ($request->filled('include')) {
            $includes = array_map('trim', explode(',', $request->include));
            $allowed = ['member', 'user'];
            $query->with(array_intersect($includes, $allowed));
        }

        $sortBy = $request->get('sort_by', 'id_penjualan');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $penjualan = $query->paginate($perPage);

        return $this->paginatedResponse($penjualan->through(fn ($item) => new PenjualanResource($item)));
    }

    public function show(Request $request, $id)
    {
        $query = Penjualan::query();

        // Always load detail with produk for show
        $query->with(['member', 'user']);

        // Include detail if requested (or always include)
        if ($request->get('include') && strpos($request->include, 'detail') !== false) {
            $query->with(['detail' => function ($q) {
                $q->with('produk');
            }]);
        }

        $penjualan = $query->find($id);
        if (! $penjualan) {
            return $this->errorResponse('Penjualan tidak ditemukan', 404);
        }

        // If detail not loaded via include, load it separately for backward compat
        if (! $penjualan->relationLoaded('detail')) {
            $detail = PenjualanDetail::with('produk')->where('id_penjualan', $id)->get();
            $penjualan->setRelation('detail', $detail);
        }

        return $this->successResponse(new PenjualanResource($penjualan));
    }
}
