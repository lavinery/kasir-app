<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporan.index', compact('tanggalAwal', 'tanggalAkhir'));
    }

    public function getData($awal, $akhir)
    {
        $no = 1;
        $data = [];
        $total_pendapatan = 0;

        while (strtotime($awal) <= strtotime($akhir)) {
            $tanggal = $awal;
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            $total_penjualan = Penjualan::whereDate('created_at', $tanggal)->sum('bayar');
            $total_pembelian = Pembelian::whereDate('created_at', $tanggal)->sum('bayar');
            $total_pengeluaran = Pengeluaran::whereDate('created_at', $tanggal)->sum('nominal');

            $keuntungan = $total_penjualan - $total_pembelian;
            $pendapatan = $keuntungan - $total_pengeluaran;
            $total_pendapatan += $pendapatan;

            $row = [
                'DT_RowIndex' => $no++,
                'tanggal' => tanggal_indonesia($tanggal, false),
                'penjualan' => format_uang($total_penjualan),
                'pembelian' => format_uang($total_pembelian),
                'pengeluaran' => format_uang($total_pengeluaran),
                'keuntungan' => format_uang($keuntungan),
                'pendapatan' => format_uang($pendapatan),
            ];

            $data[] = $row;
        }

        $data[] = [
            'DT_RowIndex' => '',
            'tanggal' => '',
            'penjualan' => '',
            'pembelian' => '',
            'pengeluaran' => 'Total Pendapatan',
            'keuntungan' => '',
            'pendapatan' => format_uang($total_pendapatan),
        ];

        return $data;
    }

    public function data($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);

        return datatables()
            ->of($data)
            ->make(true);
    }

    public function exportPDF($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);

        // Ambil dan keluarkan baris total
        $totalRow = array_pop($data);

        $startDate = Carbon::parse($awal)->format('d M Y');
        $endDate = Carbon::parse($akhir)->format('d M Y');

        $pdf = PDF::loadView('laporan.pdf', [
            'awal' => $startDate,
            'akhir' => $endDate,
            'data' => $data,
            'total' => $totalRow['pendapatan'] ?? '0',
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-pendapatan-' . date('Y-m-d-His') . '.pdf');
    }
}
