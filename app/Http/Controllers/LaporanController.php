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
        $total_keuntungan_real = 0; // TAMBAHAN: untuk total keuntungan real

        while (strtotime($awal) <= strtotime($akhir)) {
            $tanggal = $awal;
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            $total_penjualan = Penjualan::whereDate('created_at', $tanggal)->sum('bayar');
            $total_pembelian = Pembelian::whereDate('created_at', $tanggal)->sum('bayar');
            $total_pengeluaran = Pengeluaran::whereDate('created_at', $tanggal)->sum('nominal');

            // PERBAIKAN: Ambil keuntungan REAL dari kolom keuntungan di tabel penjualan
            // Bukan dari perhitungan pendapatan seperti sebelumnya
            $keuntungan_real = Penjualan::whereDate('created_at', $tanggal)->sum('keuntungan');

            // Pendapatan tetap dihitung: penjualan - pembelian - pengeluaran
            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;

            $total_pendapatan += $pendapatan;
            $total_keuntungan_real += $keuntungan_real; // TAMBAHAN: akumulasi keuntungan real

            $row = [
                'DT_RowIndex' => $no++,
                'tanggal' => tanggal_indonesia($tanggal, false),
                'penjualan' => 'Rp. ' . format_uang($total_penjualan),
                'pembelian' => 'Rp. ' . format_uang($total_pembelian),
                'pengeluaran' => 'Rp. ' . format_uang($total_pengeluaran),
                'pendapatan' => 'Rp. ' . format_uang($pendapatan),
                // PERBAIKAN: Gunakan keuntungan real dari database
                'keuntungan' => 'Rp. ' . format_uang($keuntungan_real),
            ];

            $data[] = $row;
        }

        // PERBAIKAN: Baris total untuk pendapatan dan keuntungan
        $data[] = [
            'DT_RowIndex' => '',
            'tanggal' => '',
            'penjualan' => '',
            'pembelian' => '',
            'pengeluaran' => '',
            'pendapatan' => 'Rp. ' . format_uang($total_pendapatan),
            'keuntungan' => 'Rp. ' . format_uang($total_keuntungan_real),
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

        // PERBAIKAN: Hitung total pendapatan dan keuntungan real untuk PDF
        $total_pendapatan = 0;
        $total_keuntungan = 0;

        $current_date = $awal;
        while (strtotime($current_date) <= strtotime($akhir)) {
            $penjualan_hari = Penjualan::whereDate('created_at', $current_date)->sum('bayar');
            $pembelian_hari = Pembelian::whereDate('created_at', $current_date)->sum('bayar');
            $pengeluaran_hari = Pengeluaran::whereDate('created_at', $current_date)->sum('nominal');
            $keuntungan_hari = Penjualan::whereDate('created_at', $current_date)->sum('keuntungan');

            $pendapatan_hari = $penjualan_hari - $pembelian_hari - $pengeluaran_hari;
            $total_pendapatan += $pendapatan_hari;
            $total_keuntungan += $keuntungan_hari;

            $current_date = date('Y-m-d', strtotime("+1 day", strtotime($current_date)));
        }

        $pdf = PDF::loadView('laporan.pdf', [
            'awal' => $startDate,
            'akhir' => $endDate,
            'data' => $data,
            'total_pendapatan' => format_uang($total_pendapatan),
            'total_keuntungan' => format_uang($total_keuntungan), // TAMBAHAN: kirim total keuntungan
            // Untuk backward compatibility
            'total' => format_uang($total_pendapatan),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-pendapatan-' . date('Y-m-d-His') . '.pdf');
    }
}
