<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDailySummary extends Model
{
    use HasFactory;

    protected $table = 'pembelian_daily_summary';
    protected $fillable = [
        'tanggal',
        'total_transaksi',
        'total_item',
        'total_pembelian'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    public static function updateDailySummary($date)
    {
        $summary = Pembelian::whereDate('created_at', $date)
            ->selectRaw('
                COUNT(*) as total_transaksi,
                SUM(total_item) as total_item,
                SUM(total_harga) as total_pembelian
            ')
            ->first();

        return self::updateOrCreate(
            ['tanggal' => $date],
            [
                'total_transaksi' => $summary->total_transaksi ?? 0,
                'total_item' => $summary->total_item ?? 0,
                'total_pembelian' => $summary->total_pembelian ?? 0,
            ]
        );
    }
}