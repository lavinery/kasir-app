<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranDailySummary extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_daily_summary';
    protected $fillable = [
        'tanggal',
        'total_transaksi',
        'total_pengeluaran'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    public static function updateDailySummary($date)
    {
        $summary = Pengeluaran::whereDate('created_at', $date)
            ->selectRaw('
                COUNT(*) as total_transaksi,
                SUM(nominal) as total_pengeluaran
            ')
            ->first();

        return self::updateOrCreate(
            ['tanggal' => $date],
            [
                'total_transaksi' => $summary->total_transaksi ?? 0,
                'total_pengeluaran' => $summary->total_pengeluaran ?? 0,
            ]
        );
    }
}