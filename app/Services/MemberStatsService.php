<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MemberStatsService
{
    /**
     * Build query untuk agregasi member stats dengan debugging
     */
    public function buildStatsQuery($params = [])
    {
        // Parameter default
        $startDate = $params['start_date'] ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = $params['end_date'] ?? Carbon::now()->format('Y-m-d');
        $minTransactions = $params['min_transactions'] ?? 0;
        $minAmount = $params['min_amount'] ?? 0;
        $search = $params['search'] ?? '';
        $showAllMembers = $params['show_all_members'] ?? false;

        // Validasi date range (maksimal 1 tahun)
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($end->diffInDays($start) > 365) {
            throw new \Exception('Periode maksimal 1 tahun');
        }

        // 🔍 DEBUG: Cek data yang ada
        Log::info('MemberStats Query Debug', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'member_count' => DB::table('member')->count(),
            'penjualan_count' => DB::table('penjualan')->count(),
            'penjualan_posted_count' => DB::table('penjualan')->where('status', 'posted')->count(), // ✅ FIXED: posted
            'penjualan_with_member' => DB::table('penjualan')->whereNotNull('id_member')->count()
        ]);

        // Base query dengan debugging yang lebih detail
        if ($showAllMembers) {
            $baseQuery = DB::table('member as m')
                ->leftJoin('penjualan as p', function ($join) use ($startDate, $endDate) {
                    $join->on('m.id_member', '=', 'p.id_member')
                        ->where('p.status', '=', 'posted')  // ✅ KONSISTEN: posted
                        ->whereBetween('p.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                });
        } else {
            $baseQuery = DB::table('member as m')
                ->join('penjualan as p', 'm.id_member', '=', 'p.id_member')
                ->where('p.status', 'posted')  // ✅ KONSISTEN: posted
                ->whereBetween('p.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        // Select fields dengan agregasi - PERBAIKAN NULL HANDLING
        $baseQuery->select([
            'm.id_member',
            'm.kode_member',
            'm.nama',
            'm.telepon',
            DB::raw('COUNT(p.id_penjualan) as total_transaksi'),
            DB::raw('COALESCE(SUM(p.total_harga), 0) as total_belanja'),
            DB::raw('CASE 
                        WHEN COUNT(p.id_penjualan) > 0 
                        THEN ROUND(COALESCE(SUM(p.total_harga) / COUNT(p.id_penjualan), 0), 0) 
                        ELSE 0 
                     END as avg_order_value'), // ✅ FIXED: AVG calculation
            DB::raw('COALESCE(SUM(p.total_item), 0) as total_item'),
            DB::raw('MAX(p.created_at) as last_transaction_date'),
            // ✅ ADDED: Include keuntungan if column exists
            DB::raw('COALESCE(SUM(p.keuntungan), 0) as total_keuntungan'),
        ]);

        // Group by member
        $baseQuery->groupBy([
            'm.id_member',
            'm.kode_member',
            'm.nama',
            'm.telepon'
        ]);

        // Filter berdasarkan pencarian
        if (!empty($search)) {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('m.nama', 'LIKE', "%{$search}%")
                    ->orWhere('m.kode_member', 'LIKE', "%{$search}%")
                    ->orWhere('m.telepon', 'LIKE', "%{$search}%"); // ✅ ADDED: search by phone
            });
        }

        // Filter minimum transaksi dan amount (menggunakan HAVING)
        if ($minTransactions > 0) {
            $baseQuery->having('total_transaksi', '>=', $minTransactions);
        }

        if ($minAmount > 0) {
            $baseQuery->having('total_belanja', '>=', $minAmount);
        }

        // 🔍 DEBUG: Log query yang akan dijalankan
        $sql = $baseQuery->toSql();
        $bindings = $baseQuery->getBindings();
        Log::info('Generated SQL', ['sql' => $sql, 'bindings' => $bindings]);

        return $baseQuery;
    }

    /**
     * Get detail transaksi untuk member tertentu
     */
    public function getMemberTransactionDetails($memberId, $startDate, $endDate)
    {
        return DB::table('penjualan as p')
            ->join('member as m', 'p.id_member', '=', 'm.id_member')
            ->leftJoin('users as u', 'p.id_user', '=', 'u.id') // ✅ FIXED: id_user instead of user_id
            ->where('p.id_member', $memberId)
            ->where('p.status', 'posted')  // ✅ KONSISTEN: posted
            ->whereBetween('p.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select([
                'p.id_penjualan',
                'p.created_at as tanggal',
                'p.total_item',
                'p.total_harga',
                'p.diskon',
                'p.bayar',
                'p.keuntungan', // ✅ ADDED: include keuntungan
                'u.name as kasir'
            ])
            ->orderBy('p.created_at', 'desc')
            ->get();
    }

    /**
     * Get summary statistics dengan debugging
     */
    public function getSummaryStats($params = [])
    {
        $startDate = $params['start_date'] ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = $params['end_date'] ?? Carbon::now()->format('Y-m-d');
        $minTransactions = $params['min_transactions'] ?? 0;
        $minAmount = $params['min_amount'] ?? 0;
        $showAllMembers = $params['show_all_members'] ?? false;

        // ✅ IMPROVED: Summary query with proper filtering
        if ($showAllMembers) {
            // Query untuk semua member (termasuk yang tidak ada transaksi)
            $summary = DB::table('member as m')
                ->leftJoin('penjualan as p', function ($join) use ($startDate, $endDate) {
                    $join->on('m.id_member', '=', 'p.id_member')
                        ->where('p.status', 'posted') // ✅ KONSISTEN: posted
                        ->whereBetween('p.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                })
                ->select([
                    DB::raw('COUNT(DISTINCT m.id_member) as total_members'),
                    DB::raw('COUNT(p.id_penjualan) as grand_total_transaksi'),
                    DB::raw('COALESCE(SUM(p.total_harga), 0) as grand_total_belanja'),
                    DB::raw('COALESCE(SUM(p.total_item), 0) as grand_total_item'),
                    DB::raw('COALESCE(SUM(p.keuntungan), 0) as grand_total_keuntungan'),
                ])->first();
        } else {
            // Query hanya member yang ada transaksi
            $summary = DB::table('penjualan as p')
                ->join('member as m', 'p.id_member', '=', 'm.id_member')
                ->where('p.status', 'posted')  // ✅ KONSISTEN: posted
                ->whereBetween('p.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereNotNull('p.id_member')
                ->select([
                    DB::raw('COUNT(DISTINCT p.id_member) as total_members'),
                    DB::raw('COUNT(p.id_penjualan) as grand_total_transaksi'),
                    DB::raw('COALESCE(SUM(p.total_harga), 0) as grand_total_belanja'),
                    DB::raw('COALESCE(SUM(p.total_item), 0) as grand_total_item'),
                    DB::raw('COALESCE(SUM(p.keuntungan), 0) as grand_total_keuntungan'),
                ])->first();
        }

        return $summary;
    }

    /**
     * Format data untuk DataTables dengan DT_RowIndex fix
     */
    public function formatDataForDataTables($data, $start = 0)
    {
        return $data->map(function ($item, $index) use ($start) {
            return [
                'DT_RowIndex' => $start + $index + 1, // ✅ FIX: Tambahkan DT_RowIndex
                'id_member' => $item->id_member,
                'kode_member' => $item->kode_member,
                'nama' => $item->nama,
                'telepon' => $item->telepon ?: '-',
                'total_transaksi' => number_format($item->total_transaksi),
                'total_belanja' => 'Rp ' . number_format($item->total_belanja),
                'total_belanja_raw' => $item->total_belanja,
                'avg_order_value' => 'Rp ' . number_format($item->avg_order_value),
                'total_item' => number_format($item->total_item),
                'last_transaction_date' => $item->last_transaction_date ?
                    Carbon::parse($item->last_transaction_date)->format('d/m/Y H:i') : '-',
                'last_transaction_raw' => $item->last_transaction_date,
                // ✅ ADDED: Include keuntungan
                'total_keuntungan' => 'Rp ' . number_format($item->total_keuntungan ?? 0),
                'total_keuntungan_raw' => $item->total_keuntungan ?? 0,
            ];
        });
    }

    /**
     * 🔍 DEBUG: Test database connection dan struktur tabel
     */
    public function debugDatabase()
    {
        try {
            $debug = [
                'database_connection' => 'OK',
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s'),
                'tables_exist' => [
                    'member' => Schema::hasTable('member'),
                    'penjualan' => Schema::hasTable('penjualan'),
                    'users' => Schema::hasTable('users'),
                ],
                'table_counts' => [
                    'member_total' => DB::table('member')->count(),
                    'penjualan_total' => DB::table('penjualan')->count(),
                    'penjualan_posted' => DB::table('penjualan')->where('status', 'posted')->count(), // ✅ FIXED
                    'penjualan_with_member' => DB::table('penjualan')->whereNotNull('id_member')->count(),
                ],
                'status_breakdown' => DB::table('penjualan')
                    ->select('status', DB::raw('COUNT(*) as count'))
                    ->groupBy('status')
                    ->get()
                    ->pluck('count', 'status')
                    ->toArray(),
                'sample_data' => [
                    'member_sample' => DB::table('member')->select('id_member', 'kode_member', 'nama')->limit(3)->get(),
                    'penjualan_sample' => DB::table('penjualan')
                        ->select('id_penjualan', 'id_member', 'status', 'total_harga', 'created_at')
                        ->where('status', 'posted') // ✅ FIXED
                        ->limit(3)
                        ->get(),
                ],
                'column_check' => [
                    'member_columns' => Schema::getColumnListing('member'),
                    'penjualan_columns' => Schema::getColumnListing('penjualan'),
                ],
                'recent_transactions' => DB::table('penjualan as p')
                    ->join('member as m', 'p.id_member', '=', 'm.id_member')
                    ->where('p.status', 'posted')
                    ->where('p.created_at', '>=', Carbon::now()->subDays(7))
                    ->count(),
            ];

            Log::info('Database Debug Results', $debug);
            return $debug;
        } catch (\Exception $e) {
            Log::error('Database Debug Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return [
                'database_connection' => 'ERROR',
                'error_message' => $e->getMessage(),
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            ];
        }
    }

    /**
     * Validate filter parameters
     */
    public function validateParams($params)
    {
        $errors = [];

        // Validasi tanggal
        if (isset($params['start_date']) && isset($params['end_date'])) {
            try {
                $start = Carbon::parse($params['start_date']);
                $end = Carbon::parse($params['end_date']);

                if ($start->gt($end)) {
                    $errors[] = 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir';
                }

                if ($end->diffInDays($start) > 365) {
                    $errors[] = 'Periode maksimal 1 tahun (365 hari)';
                }
            } catch (\Exception $e) {
                $errors[] = 'Format tanggal tidak valid';
            }
        }

        // Validasi minimum values
        if (isset($params['min_transactions']) && $params['min_transactions'] < 0) {
            $errors[] = 'Minimum transaksi tidak boleh negatif';
        }

        if (isset($params['min_amount']) && $params['min_amount'] < 0) {
            $errors[] = 'Minimum amount tidak boleh negatif';
        }

        return $errors;
    }
}
