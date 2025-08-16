<?php

namespace App\Http\Controllers;

use App\Services\MemberStatsService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Response;
use Illuminate\Support\Facades\Log;

class MemberStatsController extends Controller
{
    protected $memberStatsService;

    public function __construct(MemberStatsService $memberStatsService)
    {
        $this->memberStatsService = $memberStatsService;
    }

    /**
     * Halaman utama member stats
     */
    public function index()
    {
        return view('member_stats.index');
    }

    /**
     * 🔧 FIXED: Data untuk DataTables (server-side processing)
     */
    public function data(Request $request)
    {
        try {
            // 🔍 DEBUG: Log semua parameter yang masuk
            Log::info('MemberStats Data Request', [
                'all_params' => $request->all(),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
            ]);

            // Ambil parameter dari request
            $params = [
                'start_date' => $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d')),
                'end_date' => $request->get('end_date', Carbon::now()->format('Y-m-d')),
                'min_transactions' => (int) $request->get('min_transactions', 0),
                'min_amount' => (float) $request->get('min_amount', 0),
                'search' => $request->get('search.value', ''),
                'show_all_members' => filter_var($request->get('show_all_members', false), FILTER_VALIDATE_BOOLEAN)
            ];

            // 🔍 DEBUG: Database debug (jalankan sekali untuk cek)
            if ($request->get('debug') == '1') {
                $debugInfo = $this->memberStatsService->debugDatabase();
                return response()->json(['debug' => $debugInfo]);
            }

            // Validasi parameter
            $errors = $this->memberStatsService->validateParams($params);
            if (!empty($errors)) {
                return response()->json(['error' => implode(', ', $errors)], 400);
            }

            // Build query
            $query = $this->memberStatsService->buildStatsQuery($params);

            // 🔧 FIX: Clone query untuk count (hindari interference dengan pagination)
            $countQuery = clone $query;
            $totalQuery = clone $query;

            // Total records (tanpa filter search)
            $totalRecords = $this->memberStatsService->buildStatsQuery(
                array_merge($params, ['search' => ''])
            )->count();

            // Total records setelah filter
            $filteredRecords = $countQuery->count();

            // Sorting
            $orderColumn = $request->get('order.0.column', 0);
            $orderDirection = $request->get('order.0.dir', 'desc');

            $columns = [
                0 => 'kode_member',     // No
                1 => 'kode_member',     // Kode Member
                2 => 'nama',            // Nama Member
                3 => 'telepon',         // Telepon
                4 => 'total_transaksi', // Total Transaksi
                5 => 'total_belanja',   // Total Belanja
                6 => 'avg_order_value', // AOV
                7 => 'total_item',      // Total Item
                8 => 'last_transaction_date', // Transaksi Terakhir
                9 => 'kode_member'      // Aksi
            ];

            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDirection);
            } else {
                // Default sorting
                $query->orderBy('total_belanja', 'desc');
            }

            // Pagination
            $start = (int) $request->get('start', 0);
            $length = (int) $request->get('length', 25);

            $data = $query->skip($start)->take($length)->get();

            // 🔍 DEBUG: Log hasil query
            Log::info('Query Results', [
                'data_count' => $data->count(),
                'total_records' => $totalRecords,
                'filtered_records' => $filteredRecords,
                'first_item' => $data->first()
            ]);

            // 🔧 FIX: Format data dengan DT_RowIndex yang benar
            $formattedData = $this->memberStatsService->formatDataForDataTables($data, $start);

            // Get summary statistics jika diminta
            $summary = null;
            if ($request->get('get_summary', false)) {
                $summary = $this->memberStatsService->getSummaryStats($params);
            }

            return response()->json([
                'draw' => (int) $request->get('draw', 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $formattedData->values(), // 🔧 FIX: Pastikan array index berurutan
                'summary' => $summary
            ]);
        } catch (\Exception $e) {
            Log::error('Member stats data error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_params' => $request->all()
            ]);

            return response()->json([
                'error' => $e->getMessage(),
                'draw' => (int) $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ], 500);
        }
    }

    /**
     * 🔄 ENHANCED: Sync data dengan debugging
     */
    public function syncData(Request $request)
    {
        try {
            $startTime = microtime(true);

            // Get current filter parameters
            $params = [
                'start_date' => $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d')),
                'end_date' => $request->get('end_date', Carbon::now()->format('Y-m-d')),
                'min_transactions' => (int) $request->get('min_transactions', 0),
                'min_amount' => (float) $request->get('min_amount', 0),
                'show_all_members' => filter_var($request->get('show_all_members', false), FILTER_VALIDATE_BOOLEAN)
            ];

            // 🔍 DEBUG: Database health check
            $debugInfo = $this->memberStatsService->debugDatabase();

            // Force refresh data with current filters
            $query = $this->memberStatsService->buildStatsQuery($params);
            $totalRecords = $query->count();

            // Get summary stats
            $summary = $this->memberStatsService->getSummaryStats($params);

            $endTime = microtime(true);
            $processTime = round(($endTime - $startTime) * 1000, 2);

            Log::info('Member stats sync completed', [
                'total_records' => $totalRecords,
                'process_time_ms' => $processTime,
                'params' => $params,
                'debug_info' => $debugInfo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disinkronkan',
                'stats' => [
                    'total_records' => $totalRecords,
                    'process_time_ms' => $processTime,
                    'synced_at' => Carbon::now()->format('d/m/Y H:i:s'),
                    'period' => $params['start_date'] . ' - ' . $params['end_date']
                ],
                'summary' => [
                    'total_members' => $summary->total_members ?? 0,
                    'total_transactions' => $summary->grand_total_transaksi ?? 0,
                    'grand_total' => number_format($summary->grand_total_belanja ?? 0),
                    'total_items' => $summary->grand_total_item ?? 0
                ],
                'debug' => $debugInfo
            ]);
        } catch (\Exception $e) {
            Log::error('Member stats sync error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal sinkronisasi data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time summary statistics
     */
    public function getSummary(Request $request)
    {
        try {
            $params = [
                'start_date' => $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d')),
                'end_date' => $request->get('end_date', Carbon::now()->format('Y-m-d')),
                'min_transactions' => (int) $request->get('min_transactions', 0),
                'min_amount' => (float) $request->get('min_amount', 0),
                'show_all_members' => filter_var($request->get('show_all_members', false), FILTER_VALIDATE_BOOLEAN)
            ];

            $summary = $this->memberStatsService->getSummaryStats($params);

            return response()->json([
                'success' => true,
                'summary' => [
                    'total_members' => $summary->total_members ?? 0,
                    'total_transactions' => $summary->grand_total_transaksi ?? 0,
                    'grand_total' => number_format($summary->grand_total_belanja ?? 0),
                    'total_items' => $summary->grand_total_item ?? 0,
                    'avg_per_member' => $summary->total_members > 0 ?
                        number_format(($summary->grand_total_belanja ?? 0) / $summary->total_members) : 0
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Summary stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detail transaksi member
     */
    public function detail($memberId, Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

            $transactions = $this->memberStatsService->getMemberTransactionDetails(
                $memberId,
                $startDate,
                $endDate
            );

            // Format untuk tampilan
            $formattedTransactions = $transactions->map(function ($item) {
                return [
                    'tanggal' => Carbon::parse($item->tanggal)->format('d/m/Y H:i'),
                    'id_penjualan' => $item->id_penjualan,
                    'total_item' => number_format($item->total_item),
                    'total_harga' => 'Rp ' . number_format($item->total_harga),
                    'diskon' => 'Rp ' . number_format($item->diskon),
                    'bayar' => 'Rp ' . number_format($item->bayar),
                    // Uncomment jika ada kolom keuntungan:
                    // 'keuntungan' => 'Rp ' . number_format($item->keuntungan),
                    'kasir' => $item->kasir ?: '-'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedTransactions
            ]);
        } catch (\Exception $e) {
            Log::error('Member detail error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 🔧 ENHANCED: Get system status untuk debugging
     */
    public function getSystemStatus()
    {
        try {
            $debugInfo = $this->memberStatsService->debugDatabase();

            return response()->json(array_merge($debugInfo, [
                'service_status' => 'OK',
                'last_check' => Carbon::now()->format('d/m/Y H:i:s'),
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION
            ]));
        } catch (\Exception $e) {
            return response()->json([
                'database_connection' => 'ERROR',
                'error_message' => $e->getMessage(),
                'last_check' => Carbon::now()->format('d/m/Y H:i:s')
            ], 500);
        }
    }

    /**
     * Export CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            $params = [
                'start_date' => $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d')),
                'end_date' => $request->get('end_date', Carbon::now()->format('Y-m-d')),
                'min_transactions' => (int) $request->get('min_transactions', 0),
                'min_amount' => (float) $request->get('min_amount', 0),
                'search' => $request->get('search', ''),
                'show_all_members' => filter_var($request->get('show_all_members', false), FILTER_VALIDATE_BOOLEAN)
            ];

            // Get all data (tanpa pagination)
            $query = $this->memberStatsService->buildStatsQuery($params);
            $data = $query->orderBy('total_belanja', 'desc')->get();

            $filename = 'member_stats_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $callback = function () use ($data, $params) {
                $file = fopen('php://output', 'w');

                // BOM untuk Excel
                fwrite($file, "\xEF\xBB\xBF");

                // Header CSV
                fputcsv($file, [
                    'No',
                    'Kode Member',
                    'Nama Member',
                    'Telepon',
                    'Total Transaksi',
                    'Total Belanja',
                    'Rata-rata Order',
                    'Total Item',
                    'Transaksi Terakhir'
                ]);

                $no = 1;
                foreach ($data as $row) {
                    fputcsv($file, [
                        $no++,
                        $row->kode_member,
                        $row->nama,
                        $row->telepon ?: '-',
                        $row->total_transaksi,
                        $row->total_belanja,
                        $row->avg_order_value,
                        $row->total_item,
                        $row->last_transaction_date ?
                            Carbon::parse($row->last_transaction_date)->format('d/m/Y H:i') : '-'
                    ]);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('CSV export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export CSV: ' . $e->getMessage());
        }
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $params = [
                'start_date' => $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d')),
                'end_date' => $request->get('end_date', Carbon::now()->format('Y-m-d')),
                'min_transactions' => (int) $request->get('min_transactions', 0),
                'min_amount' => (float) $request->get('min_amount', 0),
                'search' => $request->get('search', ''),
                'show_all_members' => filter_var($request->get('show_all_members', false), FILTER_VALIDATE_BOOLEAN)
            ];

            // Get data
            $query = $this->memberStatsService->buildStatsQuery($params);
            $data = $query->orderBy('total_belanja', 'desc')->get();

            // Get summary
            $summary = $this->memberStatsService->getSummaryStats($params);

            $pdf = PDF::loadView('member_stats.pdf', [
                'data' => $data,
                'summary' => $summary,
                'params' => $params,
                'generated_at' => Carbon::now()
            ]);

            $filename = 'member_stats_' . date('Y-m-d_H-i-s') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }
}
