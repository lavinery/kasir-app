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
            'tanggal' => 'required|date',
            'shift' => 'required|in:1,2,3',
        ]);

        $kasir = User::findOrFail($request->kasir_id);
        $tanggal = Carbon::parse($request->tanggal)->setTimezone('Asia/Jakarta');
        $shift = $request->shift;

        // Define shift times
        $shiftTimes = [
            1 => ['start' => '07:00:00', 'end' => '15:00:00'],
            2 => ['start' => '15:00:00', 'end' => '23:00:00'],
            3 => ['start' => '23:00:00', 'end' => '07:00:00'],
        ];

        $startTime = $tanggal->copy()->setTimeFromTimeString($shiftTimes[$shift]['start']);
        $endTime = $tanggal->copy()->setTimeFromTimeString($shiftTimes[$shift]['end']);

        // Adjust for overnight shift
        if ($shift == 3) {
            $endTime->addDay();
        }

        // Convert times to UTC for database query
        $startTimeUTC = $startTime->utc();
        $endTimeUTC = $endTime->utc();

        // Debug: Log the query parameters
        \Log::info("Generating report for kasir_id: {$kasir->id}, start: {$startTimeUTC}, end: {$endTimeUTC}");

        DB::enableQueryLog();

        $penjualan = Penjualan::where('id_user', $kasir->id)
            ->whereDate('created_at', $tanggal->toDateString())
            ->get();

        // Debug: Log the SQL query
        \Log::info(DB::getQueryLog());

        $totalPenjualan = $penjualan->sum('total_harga');
        $jumlahTransaksi = $penjualan->count();

        // Debug: Log the results
        \Log::info("Found {$jumlahTransaksi} transactions, total: {$totalPenjualan}");

        // Convert penjualan timestamps back to Asia/Jakarta timezone for display
        $penjualan->transform(function ($item) {
            $item->created_at = Carbon::parse($item->created_at)->setTimezone('Asia/Jakarta');
            return $item;
        });

        return view('laporan.kasir.report', compact('kasir', 'tanggal', 'shift', 'penjualan', 'totalPenjualan', 'jumlahTransaksi'));
    }
}
