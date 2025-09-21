<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDailySummary extends Model
{
    use HasFactory;

    protected $table = 'penjualan_daily_summary';
    protected $fillable = [
        'tanggal',
        'total_transaksi',
        'total_item',
        'total_penjualan',
        'total_keuntungan'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    public static function updateDailySummary($date)
    {
        $summary = Penjualan::whereDate('created_at', $date)
            ->selectRaw('
                COUNT(*) as total_transaksi,
                SUM(total_item) as total_item,
                SUM(total_harga) as total_penjualan,
                SUM(keuntungan) as total_keuntungan
            ')
            ->first();

        return self::updateOrCreate(
            ['tanggal' => $date],
            [
                'total_transaksi' => $summary->total_transaksi ?? 0,
                'total_item' => $summary->total_item ?? 0,
                'total_penjualan' => $summary->total_penjualan ?? 0,
                'total_keuntungan' => $summary->total_keuntungan ?? 0,
            ]
        );
    }
}