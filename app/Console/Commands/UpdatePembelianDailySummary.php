<?php

namespace App\Console\Commands;

use App\Models\PembelianDailySummary;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdatePembelianDailySummary extends Command
{
    protected $signature = 'pembelian:update-daily-summary {date?}';
    protected $description = 'Update daily pembelian summary for a specific date or today';

    public function handle()
    {
        $date = $this->argument('date') ?? Carbon::today()->toDateString();

        try {
            $summary = PembelianDailySummary::updateDailySummary($date);
            $this->info("Daily pembelian summary updated for {$date}");
            $this->info("Total Transaksi: {$summary->total_transaksi}");
            $this->info("Total Pembelian: Rp " . number_format($summary->total_pembelian));
        } catch (\Exception $e) {
            $this->error("Failed to update daily pembelian summary: " . $e->getMessage());
        }
    }
}