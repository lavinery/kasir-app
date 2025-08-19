<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanKasirController extends Controller
{
    public function index()
    {
        $kasirs = User::where('level', 2)->get(); // Assuming level 2 is for cashiers
        return view('laporan.kasir.index', compact('kasirs'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'kasir_id' => 'required|exists:users,id',
            'tanggal_dari' => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
            'shift' => 'required|in:1,2,all',
        ], [
            'kasir_id.required' => 'Pilih kasir terlebih dahulu',
            'kasir_id.exists' => 'Kasir yang dipilih tidak valid',
            'tanggal_dari.required' => 'Tanggal mulai harus diisi',
            'tanggal_dari.date' => 'Format tanggal mulai tidak valid',
            'tanggal_sampai.required' => 'Tanggal akhir harus diisi',
            'tanggal_sampai.date' => 'Format tanggal akhir tidak valid',
            'tanggal_sampai.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai',
            'shift.required' => 'Pilih shift terlebih dahulu',
            'shift.in' => 'Shift yang dipilih tidak valid',
        ]);

        // Validate date range (max 31 days)
        $tanggalDari = Carbon::parse($request->tanggal_dari);
        $tanggalSampai = Carbon::parse($request->tanggal_sampai);

        if ($tanggalDari->diffInDays($tanggalSampai) > 31) {
            return back()->withErrors(['tanggal_sampai' => 'Rentang tanggal maksimal 31 hari'])->withInput();
        }

        $kasir = User::findOrFail($request->kasir_id);
        $shift = $request->shift;

        // Define shift times
        $shiftTimes = [
            1 => ['start' => '07:00:00', 'end' => '15:59:59'],
            2 => ['start' => '16:00:00', 'end' => '21:59:59'],
        ];

        // Get sales data based on shift selection
        $query = Penjualan::where('id_user', $kasir->id)
            ->whereDate('created_at', '>=', $tanggalDari->toDateString())
            ->whereDate('created_at', '<=', $tanggalSampai->toDateString());

        // Apply shift filter if specific shift is selected
        if ($shift !== 'all') {
            $startTime = $shiftTimes[$shift]['start'];
            $endTime = $shiftTimes[$shift]['end'];

            $query->whereTime('created_at', '>=', $startTime)
                ->whereTime('created_at', '<=', $endTime);
        }

        $penjualan = $query->orderBy('created_at', 'asc')->get();

        // Calculate totals
        $totalPenjualan = $penjualan->sum('total_harga');
        $jumlahTransaksi = $penjualan->count();

        // Group data by date for better reporting
        $penjualanPerHari = $penjualan->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d');
        });

        // Calculate daily statistics
        $statistikHarian = [];
        foreach ($penjualanPerHari as $tanggal => $transaksiHarian) {
            $statistikHarian[$tanggal] = [
                'tanggal' => Carbon::parse($tanggal)->format('d F Y'),
                'jumlah_transaksi' => $transaksiHarian->count(),
                'total_penjualan' => $transaksiHarian->sum('total_harga'),
                'rata_rata' => $transaksiHarian->count() > 0 ? $transaksiHarian->sum('total_harga') / $transaksiHarian->count() : 0,
                'transaksi' => $transaksiHarian
            ];
        }

        // Calculate shift statistics if specific shift selected
        $statistikShift = null;
        if ($shift !== 'all') {
            $statistikShift = [
                'shift' => $shift,
                'waktu' => $shift == 1 ? '07:00 - 16:00' : '16:00 - 22:00',
                'jumlah_hari' => $tanggalDari->diffInDays($tanggalSampai) + 1,
                'rata_rata_harian' => $tanggalDari->diffInDays($tanggalSampai) + 1 > 0 ?
                    $totalPenjualan / ($tanggalDari->diffInDays($tanggalSampai) + 1) : 0,
            ];
        }

        return view('laporan.kasir.report', compact(
            'kasir',
            'tanggalDari',
            'tanggalSampai',
            'shift',
            'penjualan',
            'totalPenjualan',
            'jumlahTransaksi',
            'statistikHarian',
            'statistikShift'
        ));
    }
}
