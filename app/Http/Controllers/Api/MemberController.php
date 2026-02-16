<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use App\Http\Resources\MemberResource;
use Illuminate\Http\Request;

class MemberController extends ApiController
{
    public function index(Request $request)
    {
        $query = Member::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_member', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'id_member');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $member = $query->paginate($perPage);

        return $this->paginatedResponse($member->through(fn ($item) => new MemberResource($item)));
    }

    public function show($id)
    {
        $member = Member::find($id);
        if (! $member) {
            return $this->errorResponse('Member tidak ditemukan', 404);
        }

        return $this->successResponse(new MemberResource($member));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat'  => 'nullable|string|max:500',
        ]);

        $last = Member::latest('id_member')->first();
        $lastKode = $last ? (int) $last->kode_member : 0;
        $kode_member = tambah_nol_didepan($lastKode + 1, 5);

        $member = Member::create([
            'kode_member' => $kode_member,
            'nama'        => $request->nama,
            'telepon'     => $request->telepon,
            'alamat'      => $request->alamat,
        ]);

        return $this->successResponse(new MemberResource($member), 'Member berhasil ditambahkan', 201);
    }

    public function update(Request $request, $id)
    {
        $member = Member::find($id);
        if (! $member) {
            return $this->errorResponse('Member tidak ditemukan', 404);
        }

        $request->validate([
            'nama'    => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat'  => 'nullable|string|max:500',
        ]);

        $member->update($request->only(['nama', 'telepon', 'alamat']));

        return $this->successResponse(new MemberResource($member), 'Member berhasil diperbarui');
    }

    public function destroy($id)
    {
        $member = Member::find($id);
        if (! $member) {
            return $this->errorResponse('Member tidak ditemukan', 404);
        }

        $member->delete();

        return $this->successResponse(null, 'Member berhasil dihapus');
    }
}
