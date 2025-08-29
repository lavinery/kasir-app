<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Setting;
use Illuminate\Http\Request;
use PDF;

class MemberController extends Controller
{
    public function index()
    {
        return view('member.index');
    }

    public function data()
    {
        $member = Member::orderBy('kode_member')->get();

        return datatables()
            ->of($member)
            ->addIndexColumn()
            ->addColumn('select_all', function ($member) {
                return '<input type="checkbox" name="id_member[]" value="' . e($member->id_member) . '">';
            })
            ->addColumn('kode_member', function ($member) {
                return '<span class="label label-success">' . e($member->kode_member) . '</span>';
            })
            ->addColumn('aksi', function ($member) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('member.show', $member->id_member) . '`)" class="btn btn-xs btn-info btn-flat" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </button>
                    <button type="button" onclick="deleteData(`' . route('member.destroy', $member->id_member) . '`)" class="btn btn-xs btn-danger btn-flat" title="Hapus">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'select_all', 'kode_member'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string|max:500'
        ], [
            'nama.required' => 'Nama member harus diisi',
            'telepon.required' => 'Nomor telepon harus diisi',
            'nama.max' => 'Nama maksimal 255 karakter',
            'telepon.max' => 'Nomor telepon maksimal 20 karakter',
            'alamat.max' => 'Alamat maksimal 500 karakter'
        ]);

        // Generate kode member (increment berbasis kode terakhir)
        $last = Member::latest('id_member')->first();
        $lastKode = $last ? (int) $last->kode_member : 0;
        $kode_member = tambah_nol_didepan($lastKode + 1, 5);

        Member::create([
            'kode_member' => $kode_member,
            'nama'        => $request->nama,
            'telepon'     => $request->telepon,
            'alamat'      => $request->alamat,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member berhasil ditambahkan!'
        ], 200);
    }

    public function show($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['error' => 'Member tidak ditemukan'], 404);
        }
        // perlu JSON untuk prefill form edit
        return response()->json($member);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string|max:500'
        ], [
            'nama.required' => 'Nama member harus diisi',
            'telepon.required' => 'Nomor telepon harus diisi',
            'nama.max' => 'Nama maksimal 255 karakter',
            'telepon.max' => 'Nomor telepon maksimal 20 karakter',
            'alamat.max' => 'Alamat maksimal 500 karakter'
        ]);

        $member = Member::find($id);
        if (!$member) {
            return response()->json(['error' => 'Member tidak ditemukan'], 404);
        }

        $member->update($request->only(['nama', 'telepon', 'alamat']));

        return response()->json([
            'success' => true,
            'message' => 'Member berhasil diperbarui!'
        ], 200);
    }

    public function destroy($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan!'
            ], 404);
        }

        $namaMember = $member->nama;
        $member->delete();

        return response()->json([
            'success' => true,
            'message' => "Member '{$namaMember}' berhasil dihapus!"
        ], 200);
    }

    public function bulkDestroy(Request $request)
    {
        if (!$request->id_member || count($request->id_member) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih member yang akan dihapus!'
            ], 400);
        }

        $deletedCount = 0;
        $deletedNames = [];
        
        foreach ($request->id_member as $id) {
            $member = Member::find($id);
            if ($member) {
                $deletedNames[] = $member->nama;
                $member->delete();
                $deletedCount++;
            }
        }
        
        if ($deletedCount == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada member yang berhasil dihapus!'
            ], 400);
        }
        
        $message = $deletedCount == 1 
            ? "Member '{$deletedNames[0]}' berhasil dihapus!"
            : "{$deletedCount} member berhasil dihapus!";
            
        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }

    public function cetakMember(Request $request)
    {
        $request->validate([
            'id_member'   => 'required|array',
            'id_member.*' => 'exists:member,id_member'
        ]);

        $datamember = Member::whereIn('id_member', $request->id_member)->get();

        if ($datamember->isEmpty()) {
            return back()->with('error', 'Tidak ada member yang dipilih untuk dicetak');
        }

        $datamember = $datamember->chunk(2); // 2 kartu per baris
        $setting = Setting::first();
        $no = 1;

        try {
            $pdf = PDF::loadView('member.cetak', compact('datamember', 'no', 'setting'));
            $pdf->setPaper([0, 0, 566.93, 850.39], 'portrait');
            return $pdf->stream('kartu_member_' . date('Y-m-d_H-i-s') . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mencetak kartu member: ' . $e->getMessage());
        }
    }
}
