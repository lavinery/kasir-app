<?php

namespace App\Http\Controllers\Api;

use App\Models\Pengeluaran;
use App\Http\Resources\PengeluaranResource;
use Illuminate\Http\Request;

class PengeluaranController extends ApiController
{
    public function index(Request $request)
    {
        $query = Pengeluaran::query();

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        // Search by deskripsi
        if ($request->filled('search')) {
            $query->where('deskripsi', 'like', '%' . $request->search . '%');
        }

        $sortBy = $request->get('sort_by', 'id_pengeluaran');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $pengeluaran = $query->paginate($perPage);

        return $this->paginatedResponse($pengeluaran->through(fn ($item) => new PengeluaranResource($item)));
    }

    public function show($id)
    {
        $pengeluaran = Pengeluaran::find($id);
        if (! $pengeluaran) {
            return $this->errorResponse('Pengeluaran tidak ditemukan', 404);
        }

        return $this->successResponse(new PengeluaranResource($pengeluaran));
    }
}
