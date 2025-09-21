<?php

namespace App\Console\Commands;

use App\Models\PenjualanDailySummary;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateDailySummary extends Command
{
    protected $signature = 'penjualan:update-daily-summary {date?}';
    protected $description = 'Update daily sales summary for a specific date or today';

    public function handle()
    {
        $date = $this->argument('date') ?? Carbon::today()->toDateString();

        try {
            $summary = PenjualanDailySummary::updateDailySummary($date);
            $this->info("Daily summary updated for {$date}");
            $this->info("Total Transaksi: {$summary->total_transaksi}");
            $this->info("Total Penjualan: Rp " . number_format($summary->total_penjualan));
        } catch (\Exception $e) {
            $this->error("Failed to update daily summary: " . $e->getMessage());
        }
    }
}