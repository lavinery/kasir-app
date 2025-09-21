<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\PenjualanDailySummary;
use App\Models\Produk;
use App\Models\Setting;
use Illuminate\Http\Request;
use PDF;

class PenjualanController extends Controller
{
    public function index()
    {
        return view('penjualan.index');
    }

    public function dailySummary()
    {
        try {
            $summaries = PenjualanDailySummary::orderBy('tanggal', 'desc')->get();

            return datatables()
                ->of($summaries)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($summary) {
                    return tanggal_indonesia($summary->tanggal, false);
                })
                ->addColumn('total_penjualan', function ($summary) {
                    return 'Rp. ' . format_uang($summary->total_penjualan ?? 0);
                })
                ->addColumn('total_transaksi', function ($summary) {
                    return format_uang($summary->total_transaksi ?? 0);
                })
                ->addColumn('total_item', function ($summary) {
                    return format_uang($summary->total_item ?? 0);
                })
                ->addColumn('total_keuntungan', function ($summary) {
                    if (auth()->user()->level == 1) {
                        return 'Rp. ' . format_uang($summary->total_keuntungan ?? 0);
                    }
                    return '-';
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
        $penjualan = Penjualan::with(['member', 'user'])
            ->whereDate('created_at', $date)
            ->orderBy('id_penjualan', 'desc')
            ->get();

        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('waktu', function ($penjualan) {
                return $penjualan->created_at->format('H:i:s');
            })
            ->addColumn('total_item', function ($penjualan) {
                return format_uang($penjualan->total_item);
            })
            ->addColumn('total_harga', function ($penjualan) {
                return 'Rp. ' . format_uang($penjualan->total_harga);
            })
            ->addColumn('bayar', function ($penjualan) {
                return 'Rp. ' . format_uang($penjualan->bayar);
            })
            ->addColumn('kode_member', function ($penjualan) {
                $member = $penjualan->member->kode_member ?? '';
                return '<span class="label label-success">' . $member . '</span>';
            })
            ->editColumn('diskon', function ($penjualan) {
                return $penjualan->diskon . '%';
            })
            ->editColumn('kasir', function ($penjualan) {
                return $penjualan->user->name ?? '';
            })
            ->addColumn('keuntungan', function ($penjualan) {
                if (auth()->user()->level == 1) {
                    return 'Rp. ' . format_uang($penjualan->keuntungan);
                }
                return '-';
            })
            ->addColumn('aksi', function ($penjualan) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`' . route('penjualan.show', $penjualan->id_penjualan) . '`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="printNotaKecil(`' . route('penjualan.notaKecil', $penjualan->id_penjualan) . '`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-print"></i></button>
                    <button onclick="printNotaBesar(`' . route('penjualan.notaBesar', $penjualan->id_penjualan) . '`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-file-pdf-o"></i></button>
                    <button onclick="deleteData(`' . route('penjualan.destroy', $penjualan->id_penjualan) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>';
            })
            ->rawColumns(['aksi', 'kode_member'])
            ->make(true);
    }

    public function data()
    {
        $penjualan = Penjualan::with('member')->orderBy('id_penjualan', 'desc')->get();

        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('total_item', function ($penjualan) {
                return format_uang($penjualan->total_item);
            })
            ->addColumn('total_harga', function ($penjualan) {
                return 'Rp. ' . format_uang($penjualan->total_harga);
            })
            ->addColumn('bayar', function ($penjualan) {
                return 'Rp. ' . format_uang($penjualan->bayar);
            })
            ->addColumn('tanggal', function ($penjualan) {
                return tanggal_indonesia($penjualan->created_at, false);
            })
            ->addColumn('kode_member', function ($penjualan) {
                $member = $penjualan->member->kode_member ?? '';
                return '<span class="label label-success">' . $member . '</spa>';
            })
            ->editColumn('diskon', function ($penjualan) {
                return $penjualan->diskon . '%';
            })
            ->editColumn('kasir', function ($penjualan) {
                return $penjualan->user->name ?? '';
            })
            ->addColumn('keuntungan', function ($penjualan) {
                if (auth()->user()->level == 1) {
                    return 'Rp. ' . format_uang($penjualan->keuntungan);
                }
                return '-';
            })
            ->addColumn('aksi', function ($penjualan) {
                return '
                <div class="btn-group">
    <button onclick="showDetail(`' . route('penjualan.show', $penjualan->id_penjualan) . '`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
    <button onclick="printNotaKecil(`' . route('penjualan.notaKecil', $penjualan->id_penjualan) . '`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-print"></i></button>
    <button onclick="printNotaBesar(`' . route('penjualan.notaBesar', $penjualan->id_penjualan) . '`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-file-pdf-o"></i></button>
    <button onclick="deleteData(`' . route('penjualan.destroy', $penjualan->id_penjualan) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
</div>
                ';
            })
            ->rawColumns(['aksi', 'kode_member'])
            ->make(true);
    }

    public function create()
    {
        $penjualan = new Penjualan();
        $penjualan->id_member = null;
        $penjualan->total_item = 0;
        $penjualan->total_harga = 0;
        $penjualan->diskon = 0;
        $penjualan->bayar = 0;
        $penjualan->diterima = 0;
        $penjualan->id_user = auth()->id();
        $penjualan->save();

        session(['id_penjualan' => $penjualan->id_penjualan]);
        return redirect()->route('transaksi.index');
    }

    public function store(Request $request)
    {
        $penjualan = Penjualan::findOrFail($request->id_penjualan);
        $penjualan->id_member = $request->id_member;
        $penjualan->total_item = $request->total_item;
        $penjualan->total_harga = $request->total;
        $penjualan->diskon = $request->diskon; // Diskon member disimpan di tabel penjualan
        $penjualan->bayar = $request->bayar;
        $penjualan->diterima = $request->diterima;

        // Hitung total keuntungan dari detail produk
        $total_keuntungan = 0;
        $detail = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();

        foreach ($detail as $item) {
            $produk = Produk::find($item->id_produk);

            // Hitung keuntungan per item (harga_jual - harga_beli) x jumlah
            $keuntungan_per_item = ($produk->harga_jual - $produk->harga_beli) * $item->jumlah;
            $total_keuntungan += $keuntungan_per_item;

            // PERBAIKAN: JANGAN override diskon produk dengan diskon member!
            // $item->diskon = $request->diskon; // ❌ HAPUS BARIS INI!

            // Diskon produk tetap disimpan di $item->diskon (dari produk)
            // Diskon member disimpan di $penjualan->diskon (terpisah)

            $item->keuntungan = $keuntungan_per_item; // jika ada kolom ini di penjualan_detail
            $item->update();

            // Kurangi stok produk
            $produk->stok -= $item->jumlah;
            $produk->save();
        }

        $penjualan->keuntungan = $total_keuntungan;
        $penjualan->save();

        // Update daily summary setelah penjualan disimpan
        PenjualanDailySummary::updateDailySummary(now()->toDateString());

        return redirect()->route('transaksi.selesai');
    }

    public function show($id)
    {
        $detail = PenjualanDetail::with('produk')->where('id_penjualan', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">' . $detail->produk->kode_produk . '</span>';
            })
            ->addColumn('nama_produk', function ($detail) {
                return $detail->produk->nama_produk;
            })
            ->addColumn('harga_jual', function ($detail) {
                return 'Rp. ' . format_uang($detail->harga_jual);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('subtotal', function ($detail) {
                return 'Rp. ' . format_uang($detail->subtotal);
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::find($id);
        $detail    = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $produk = Produk::find($item->id_produk);
            if ($produk) {
                $produk->stok += $item->jumlah;
                $produk->update();
            }

            $item->delete();
        }

        $penjualan->delete();

        // Update daily summary setelah penjualan dihapus
        PenjualanDailySummary::updateDailySummary($penjualan->created_at->toDateString());

        return response(null, 204);
    }

    public function selesai()
    {
        $setting = Setting::first();

        return view('penjualan.selesai', compact('setting'));
    }

    public function notaKecil()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        return view('penjualan.nota_kecil', compact('setting', 'penjualan', 'detail'));
    }

    public function notaBesar()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        $pdf = PDF::loadView('penjualan.nota_besar', compact('setting', 'penjualan', 'detail'));
        $pdf->setPaper(0, 0, 609, 440, 'potrait');
        return $pdf->stream('Transaksi-' . date('Y-m-d-his') . '.pdf');
    }

    public function cetakNotaKecil($id)
    {
        $setting = Setting::first();
        $penjualan = Penjualan::findOrFail($id);
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', $id)
            ->get();

        return view('penjualan.nota_kecil', compact('setting', 'penjualan', 'detail'));
    }

    public function cetakNotaBesar($id)
    {
        $setting = Setting::first();
        $penjualan = Penjualan::findOrFail($id);
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', $id)
            ->get();

        $pdf = PDF::loadView('penjualan.nota_besar', compact('setting', 'penjualan', 'detail'));
        $pdf->setPaper([0, 0, 609, 440], 'portrait');
        return $pdf->stream('Transaksi-' . date('Y-m-d-his') . '.pdf');
    }
}
