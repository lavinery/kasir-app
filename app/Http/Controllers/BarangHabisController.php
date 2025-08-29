<?php

namespace App\Http\Controllers;

use App\Models\BarangHabis;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangHabisController extends Controller
{
    /**
     * Display the main page
     */
    public function index()
    {
        $kategori = Kategori::orderBy('nama_kategori')->get();

        return view('barang_habis.index', compact('kategori'));
    }

    /**
     * Get data for DataTables
     */
    public function data(Request $request)
    {
        try {
            $query = BarangHabis::with(['produk.kategori']);

            // Filter berdasarkan kategori
            if ($request->has('kategori') && $request->kategori != '') {
                $query->whereHas('produk', function ($q) use ($request) {
                    $q->where('id_kategori', $request->kategori);
                });
            }

            // Filter berdasarkan tipe
            if ($request->has('tipe') && $request->tipe != '') {
                $query->where('tipe', $request->tipe);
            }

            // Search
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('produk', function ($subQ) use ($search) {
                        $subQ->where('nama_produk', 'LIKE', "%{$search}%")
                            ->orWhere('merk', 'LIKE', "%{$search}%");
                    })
                        ->orWhere('keterangan', 'LIKE', "%{$search}%");
                });
            }

            // Count total records
            $totalData = $query->count();

            // Apply pagination
            if ($request->has('start') && $request->has('length')) {
                $query->skip($request->start)->take($request->length);
            }

            // Apply ordering
            $query->orderBy('created_at', 'desc');

            $data = $query->get();

            // Format data untuk DataTables
            $formattedData = $data->map(function ($item, $index) use ($request) {
                return [
                    'DT_RowIndex' => ($request->start ?? 0) + $index + 1,
                    'id' => $item->id,
                    'kategori' => $item->produk->kategori->nama_kategori ?? 'Tanpa Kategori',
                    'nama_produk' => $item->produk->nama_produk ?? '-',
                    'merk' => $item->produk->merk ?? '-',
                    'stok' => $item->produk->stok ?? 0,
                    'keterangan' => $item->keterangan ?? '-',
                    'tipe' => $item->tipe === 'auto'
                        ? '<span class="badge badge-warning">Auto</span>'
                        : '<span class="badge badge-info">Manual</span>',
                    'created_at' => $item->created_at->format('d/m/Y H:i'),
                    'action' => view('barang_habis.action', compact('item'))->render()
                ];
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalData,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            \Log::error('BarangHabis data error: ' . $e->getMessage());

            return response()->json([
                'draw' => 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get products for select dropdown
     */
    public function getAvailableProducts(Request $request)
    {
        $search = $request->get('q', '');

        $products = Produk::with('kategori')
            ->whereNotIn('id_produk', function ($query) {
                $query->select('id_produk')->from('barang_habis');
            })
            ->when($search, function ($query, $search) {
                return $query->where('nama_produk', 'LIKE', "%{$search}%")
                    ->orWhere('merk', 'LIKE', "%{$search}%");
            })
            ->limit(50)
            ->get();

        $formatted = $products->map(function ($product) {
            return [
                'id' => $product->id_produk,
                'text' => $product->nama_produk . ' (' . ($product->kategori->nama_kategori ?? 'Tanpa Kategori') . ') - Stok: ' . $product->stok
            ];
        });

        return response()->json(['results' => $formatted]);
    }

    /**
     * Store manual entry
     */
    public function storeManual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_produk' => 'required|exists:produk,id_produk',
            'keterangan' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check jika sudah ada di daftar
            $existing = BarangHabis::where('id_produk', $request->id_produk)->first();
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk sudah ada dalam daftar barang habis'
                ], 400);
            }

            BarangHabis::create([
                'id_produk' => $request->id_produk,
                'tipe' => 'manual',
                'keterangan' => $request->keterangan ?: 'Ditambahkan manual oleh operator'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke daftar barang habis'
            ]);
        } catch (\Exception $e) {
            \Log::error('BarangHabis store error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update keterangan
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'keterangan' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Keterangan wajib diisi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $barangHabis = BarangHabis::find($id);
            if (!$barangHabis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $barangHabis->update(['keterangan' => $request->keterangan]);

            return response()->json([
                'success' => true,
                'message' => 'Keterangan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove from list
     */
    public function destroy($id)
    {
        try {
            $barangHabis = BarangHabis::find($id);
            if (!$barangHabis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $barangHabis->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari daftar barang habis'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $query = BarangHabis::with(['produk.kategori']);

            // Apply same filters as data method
            if ($request->has('kategori') && $request->kategori != '') {
                $query->whereHas('produk', function ($q) use ($request) {
                    $q->where('id_kategori', $request->kategori);
                });
            }

            if ($request->has('tipe') && $request->tipe != '') {
                $query->where('tipe', $request->tipe);
            }

            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('produk', function ($subQ) use ($search) {
                        $subQ->where('nama_produk', 'LIKE', "%{$search}%")
                            ->orWhere('merk', 'LIKE', "%{$search}%");
                    })
                        ->orWhere('keterangan', 'LIKE', "%{$search}%");
                });
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            // Group by category
            $groupedData = $data->groupBy(function ($item) {
                return $item->produk->kategori->nama_kategori ?? 'Tanpa Kategori';
            });

            $totalItems = $data->count();
            $filterInfo = $this->buildFilterInfo($request);

            $pdf = PDF::loadView('barang_habis.pdf', compact('groupedData', 'totalItems', 'filterInfo'));

            return $pdf->download('daftar-barang-habis-' . date('Y-m-d-His') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Build filter info for PDF
     */
    private function buildFilterInfo($request)
    {
        $filters = [];

        if ($request->has('kategori') && $request->kategori != '') {
            $kategori = Kategori::find($request->kategori);
            $filters[] = 'Kategori: ' . ($kategori ? $kategori->nama_kategori : 'Tidak ditemukan');
        }

        if ($request->has('tipe') && $request->tipe != '') {
            $filters[] = 'Tipe: ' . ucfirst($request->tipe);
        }

        if ($request->has('search') && $request->search != '') {
            $filters[] = 'Pencarian: "' . $request->search . '"';
        }

        return $filters;
    }

    /**
     * Bulk delete - Tambahkan method ini di BarangHabisController
     */
    public function bulkDestroy(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada item yang dipilih'
                ], 400);
            }

            // Hapus berdasarkan IDs
            $deleted = BarangHabis::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deleted} item dari daftar barang habis"
            ]);
        } catch (\Exception $e) {
            \Log::error('Bulk delete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export PDF dengan IDs tertentu
     */
    public function exportPdfByIds(Request $request)
    {
        try {
            $ids = explode(',', $request->input('ids', ''));

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Tidak ada item yang dipilih');
            }

            $query = BarangHabis::with(['produk.kategori'])
                ->whereIn('id', $ids);

            $data = $query->orderBy('created_at', 'desc')->get();

            // Group by category
            $groupedData = $data->groupBy(function ($item) {
                return $item->produk->kategori->nama_kategori ?? 'Tanpa Kategori';
            });

            $totalItems = $data->count();
            $filterInfo = ['Filter: Item terpilih (' . $totalItems . ' item)'];

            $pdf = \PDF::loadView('barang_habis.pdf', compact('groupedData', 'totalItems', 'filterInfo'));

            return $pdf->download('daftar-barang-habis-terpilih-' . date('Y-m-d-His') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * FIXED: Manual sync all products - triggered by refresh button
     */
    public function syncAll(Request $request)
    {
        try {
            // Ambil threshold dari request atau default 5
            $threshold = $request->input('threshold', 5);

            $processed = 0;
            $added = 0;
            $removed = 0;
            $errors = [];

            // Mulai database transaction
            \DB::beginTransaction();

            // 1. Tambahkan produk dengan stok <= threshold yang belum ada di daftar
            $produkHabis = Produk::where('stok', '<=', $threshold)
                ->whereNotIn('id_produk', function ($query) {
                    $query->select('id_produk')->from('barang_habis');
                })
                ->with(['kategori'])
                ->get();

            foreach ($produkHabis as $produk) {
                try {
                    BarangHabis::create([
                        'id_produk' => $produk->id_produk,
                        'tipe' => 'auto',
                        'keterangan' => "Sinkronisasi otomatis - stok {$produk->stok} ≤ {$threshold} (" . now()->format('Y-m-d H:i:s') . ")"
                    ]);
                    $added++;
                    $processed++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal menambah {$produk->nama_produk}: " . $e->getMessage();
                }
            }

            // 2. Hapus produk AUTO yang stoknya sudah > threshold
            $produkAman = BarangHabis::with('produk')
                ->where('tipe', 'auto')
                ->whereHas('produk', function ($query) use ($threshold) {
                    $query->where('stok', '>', $threshold);
                })
                ->get();

            foreach ($produkAman as $item) {
                try {
                    $item->delete();
                    $removed++;
                    $processed++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal menghapus {$item->produk->nama_produk}: " . $e->getMessage();
                }
            }

            // Commit transaction jika tidak ada error fatal
            \DB::commit();

            // 3. Log aktivitas sync
            \Log::info('Manual sync barang habis completed from web interface', [
                'threshold' => $threshold,
                'processed' => $processed,
                'added' => $added,
                'removed' => $removed,
                'errors_count' => count($errors),
                'user' => auth()->user()->name ?? 'system',
                'timestamp' => now()
            ]);

            $message = "Sinkronisasi selesai! Diproses: {$processed} item (Ditambah: {$added}, Dihapus: {$removed})";

            if (count($errors) > 0) {
                $message .= " dengan " . count($errors) . " error.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'stats' => [
                    'threshold' => $threshold,
                    'processed' => $processed,
                    'added' => $added,
                    'removed' => $removed,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            // Rollback transaction pada error
            \DB::rollback();

            \Log::error('Manual sync barang habis failed from web interface: ' . $e->getMessage(), [
                'user' => auth()->user()->name ?? 'system',
                'timestamp' => now(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * FIXED: Get sync statistics
     */
    public function getSyncStats(Request $request)
    {
        try {
            // Ambil threshold dari config atau request
            $threshold = $request->input('threshold', config('app.stock_threshold', 5));

            $stats = [
                'total_barang_habis' => BarangHabis::count(),
                'auto_entries' => BarangHabis::where('tipe', 'auto')->count(),
                'manual_entries' => BarangHabis::where('tipe', 'manual')->count(),
                'produk_stok_rendah' => Produk::where('stok', '<=', $threshold)->count(),
                'perlu_ditambah' => Produk::where('stok', '<=', $threshold)
                    ->whereNotIn('id_produk', function ($query) {
                        $query->select('id_produk')->from('barang_habis');
                    })->count(),
                'perlu_dihapus' => BarangHabis::where('tipe', 'auto')
                    ->whereHas('produk', function ($query) use ($threshold) {
                        $query->where('stok', '>', $threshold);
                    })->count(),
                'threshold' => $threshold,
                'needs_sync' => false,
                'auto_sync_enabled' => true, // Auto sync sudah aktif
                'last_auto_sync' => $this->getLastAutoSyncTime()
            ];

            // Tentukan apakah perlu sync
            $stats['needs_sync'] = ($stats['perlu_ditambah'] + $stats['perlu_dihapus']) > 0;

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Get sync stats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error getting stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get last auto sync time from logs
     */
    private function getLastAutoSyncTime()
    {
        // Cek dari log terbaru
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            if (preg_match('/Auto (added|removed) to barang habis.*?(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $logs, $matches)) {
                return $matches[2];
            }
        }
        
        // Fallback: cek dari data barang habis terbaru
        $latestAuto = BarangHabis::where('tipe', 'auto')
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $latestAuto ? $latestAuto->created_at->format('Y-m-d H:i:s') : 'Belum pernah';
    }

    /**
     * TAMBAHAN: Method untuk reset semua data barang habis (HANYA untuk development)
     */
    public function resetAll(Request $request)
    {
        // HANYA untuk development - jangan aktifkan di production
        if (app()->environment('production')) {
            return response()->json([
                'success' => false,
                'message' => 'Reset tidak diizinkan di production'
            ], 403);
        }

        try {
            $count = BarangHabis::count();
            BarangHabis::truncate();

            \Log::warning('Barang Habis data reset by user', [
                'total_deleted' => $count,
                'user' => auth()->user()->name ?? 'system',
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$count} data barang habis"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reset data: ' . $e->getMessage()
            ], 500);
        }
    }
}
