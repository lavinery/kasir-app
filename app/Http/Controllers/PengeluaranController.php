<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Models\PengeluaranDailySummary;

class PengeluaranController extends Controller
{
    public function index()
    {
        return view('pengeluaran.index');
    }

    public function dailySummary()
    {
        try {
            $summaries = PengeluaranDailySummary::orderBy('tanggal', 'desc')->get();

            return datatables()
                ->of($summaries)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($summary) {
                    return tanggal_indonesia($summary->tanggal, false);
                })
                ->addColumn('total_pengeluaran', function ($summary) {
                    return 'Rp. ' . format_uang($summary->total_pengeluaran ?? 0);
                })
                ->addColumn('total_transaksi', function ($summary) {
                    return format_uang($summary->total_transaksi ?? 0);
                })
                ->addColumn('aksi', function ($summary) {
                    return '
                    <button onclick="loadDailyDetails(\'' . $summary->tanggal . '\')"
                            class="btn btn-xs btn-info btn-flat">
                        <i class="fa fa-eye"></i> Detail
                    </button>';
                })
                ->rawColumns(['aksi'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function dailyDetails($date)
    {
        $pengeluaran = Pengeluaran::whereDate('created_at', $date)
            ->orderBy('id_pengeluaran', 'desc')
            ->get();

        return datatables()
            ->of($pengeluaran)
            ->addIndexColumn()
            ->addColumn('waktu', function ($pengeluaran) {
                return $pengeluaran->created_at->format('H:i:s');
            })
            ->addColumn('deskripsi', function ($pengeluaran) {
                return $pengeluaran->deskripsi;
            })
            ->addColumn('nominal', function ($pengeluaran) {
                return 'Rp. ' . format_uang($pengeluaran->nominal);
            })
            ->addColumn('aksi', function ($pengeluaran) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('pengeluaran.update', $pengeluaran->id_pengeluaran) . '`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`' . route('pengeluaran.destroy', $pengeluaran->id_pengeluaran) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function data()
    {
        $pengeluaran = Pengeluaran::orderBy('id_pengeluaran', 'desc')->get();

        return datatables()
            ->of($pengeluaran)
            ->addIndexColumn()
            ->addColumn('created_at', function ($pengeluaran) {
                return tanggal_indonesia($pengeluaran->created_at, false);
            })
            ->addColumn('nominal', function ($pengeluaran) {
                return format_uang($pengeluaran->nominal);
            })
            ->addColumn('aksi', function ($pengeluaran) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('pengeluaran.update', $pengeluaran->id_pengeluaran) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`'. route('pengeluaran.destroy', $pengeluaran->id_pengeluaran) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pengeluaran = Pengeluaran::create($request->all());

        // Update daily summary setelah pengeluaran ditambah
        PengeluaranDailySummary::updateDailySummary(now()->toDateString());

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil ditambahkan!'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pengeluaran = Pengeluaran::find($id);

        return response()->json($pengeluaran);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::find($id);
        if (!$pengeluaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pengeluaran tidak ditemukan!'
            ], 404);
        }
        
        $pengeluaran->update($request->all());

        // Update daily summary setelah pengeluaran diperbarui
        PengeluaranDailySummary::updateDailySummary($pengeluaran->created_at->toDateString());

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil diperbarui!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::find($id);
        if (!$pengeluaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pengeluaran tidak ditemukan!'
            ], 404);
        }

        $deskripsi = $pengeluaran->deskripsi;
        $tanggal = $pengeluaran->created_at->toDateString();
        $pengeluaran->delete();

        // Update daily summary setelah pengeluaran dihapus
        PengeluaranDailySummary::updateDailySummary($tanggal);

        return response()->json([
            'success' => true,
            'message' => "Pengeluaran '{$deskripsi}' berhasil dihapus!"
        ], 200);
    }
}
