<?php

use App\Http\Controllers\{
    DashboardController,
    KategoriController,
    LaporanController,
    ProdukController,
    MemberController,
    PengeluaranController,
    PembelianController,
    PembelianDetailController,
    PenjualanController,
    PenjualanDetailController,
    SettingController,
    SupplierController,
    UserController,
    LaporanKasirController,
    BarangHabisController,
    MemberStatsController,
    FavoriteProductController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // DASHBOARD (semua user yang login)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | ADMIN & KASIR ROUTES (Level 1 & 2)
    |--------------------------------------------------------------------------
    */
    Route::middleware('level:1,2')->group(function () {

        // ==================== MASTER DATA ====================

        // KATEGORI
        Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
        Route::resource('/kategori', KategoriController::class);

        // MEMBER (CRUD - untuk semua admin & kasir)
        Route::prefix('member')->name('member.')->group(function () {
            Route::get('/', [MemberController::class, 'index'])->name('index');
            Route::get('/data', [MemberController::class, 'data'])->name('data');
            Route::post('/', [MemberController::class, 'store'])->name('store');
            Route::get('/{member}', [MemberController::class, 'show'])->name('show');
            Route::put('/{member}', [MemberController::class, 'update'])->name('update');
            Route::delete('/{member}', [MemberController::class, 'destroy'])->name('destroy');

            // Bulk actions
            Route::delete('/bulk/delete', [MemberController::class, 'bulkDestroy'])->name('bulk_destroy');
            Route::post('/cetak-member', [MemberController::class, 'cetakMember'])->name('cetak_member');
        });

        // SUPPLIER
        Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
        Route::resource('/supplier', SupplierController::class);

        // ==================== PRODUK ====================

        Route::prefix('produk')->name('produk.')->group(function () {
            Route::get('/', [ProdukController::class, 'index'])->name('index');
            Route::get('/data', [ProdukController::class, 'data'])->name('data');
            Route::post('/', [ProdukController::class, 'store'])->name('store');
            Route::get('/{produk}', [ProdukController::class, 'show'])->name('show');
            Route::get('/{produk}/edit', [ProdukController::class, 'edit'])->name('edit');
            Route::put('/{produk}', [ProdukController::class, 'update'])->name('update');
            Route::delete('/{produk}', [ProdukController::class, 'destroy'])->name('destroy');

            // Bulk actions
            Route::post('/delete-selected', [ProdukController::class, 'deleteSelected'])->name('delete_selected');

            // Barcode & Export
            Route::post('/cetak-barcode', [ProdukController::class, 'cetakBarcode'])->name('cetak_barcode');
            Route::get('/barcode-pdf', [ProdukController::class, 'barcodePDF'])->name('barcode_pdf');
            Route::post('/barcode-png', [ProdukController::class, 'barcodePNG'])->name('barcode_png');
            Route::post('/cetak-daftar', [ProdukController::class, 'cetakDaftar'])->name('cetak_daftar');
            Route::post('/export-excel', [ProdukController::class, 'exportExcel'])->name('export_excel');

            // Label printing
            Route::post('/cetak-barcode-label-33x15', [ProdukController::class, 'cetakBarcodeLabel33x15'])->name('cetak_barcode_label_33x15');
            Route::post('/cetak-barcode-label-105', [ProdukController::class, 'cetakBarcodeLabel105'])->name('cetak_barcode_label_105');
            Route::post('/cetak-barcode-label-107', [ProdukController::class, 'cetakBarcodeLabel107'])->name('cetak_barcode_label_107');
        });

        // ==================== BARANG HABIS ====================

        Route::prefix('barang-habis')->name('barang_habis.')->group(function () {
            Route::get('/', [BarangHabisController::class, 'index'])->name('index');
            Route::get('/data', [BarangHabisController::class, 'data'])->name('data');
            Route::get('/products', [BarangHabisController::class, 'getAvailableProducts'])->name('products');
            Route::post('/manual', [BarangHabisController::class, 'storeManual'])->name('store_manual');
            Route::put('/{id}', [BarangHabisController::class, 'update'])->name('update');
            Route::delete('/{id}', [BarangHabisController::class, 'destroy'])->name('destroy');

            // Export & Bulk actions
            Route::get('/export-pdf', [BarangHabisController::class, 'exportPdf'])->name('export_pdf');
            Route::delete('/bulk/delete', [BarangHabisController::class, 'bulkDestroy'])->name('bulk_destroy');
            Route::get('/export-pdf-by-ids', [BarangHabisController::class, 'exportPdfByIds'])->name('export_pdf_by_ids');

            // Sync
            Route::post('/sync', [BarangHabisController::class, 'syncAll'])->name('sync');
            Route::get('/sync-stats', [BarangHabisController::class, 'getSyncStats'])->name('sync_stats');
        });

        // ==================== TRANSAKSI ====================

        // PEMBELIAN
        Route::prefix('pembelian')->name('pembelian.')->group(function () {
            Route::get('/', [PembelianController::class, 'index'])->name('index');
            Route::get('/data', [PembelianController::class, 'data'])->name('data');
            Route::get('/{id}/create', [PembelianController::class, 'create'])->name('create');
            Route::post('/', [PembelianController::class, 'store'])->name('store');
            Route::get('/{pembelian}', [PembelianController::class, 'show'])->name('show');
            Route::put('/{pembelian}', [PembelianController::class, 'update'])->name('update');
            Route::delete('/{pembelian}', [PembelianController::class, 'destroy'])->name('destroy');
        });

        // PEMBELIAN DETAIL - PERBAIKAN DI SINI
        Route::prefix('pembelian_detail')->name('pembelian_detail.')->group(function () {
            // Tambah route index yang hilang
            Route::get('/', [PembelianDetailController::class, 'index'])->name('index');

            Route::get('/{id}/data', [PembelianDetailController::class, 'data'])->name('data');
            Route::get('/loadform/{diskon}/{total}', [PembelianDetailController::class, 'loadForm'])->name('load_form');
            Route::post('/', [PembelianDetailController::class, 'store'])->name('store');
            Route::put('/{pembelian_detail}', [PembelianDetailController::class, 'update'])->name('update');
            Route::delete('/{pembelian_detail}', [PembelianDetailController::class, 'destroy'])->name('destroy');
        });

        // PENGELUARAN
        Route::get('/pengeluaran/data', [PengeluaranController::class, 'data'])->name('pengeluaran.data');
        Route::resource('/pengeluaran', PengeluaranController::class);

        // PENJUALAN
        Route::prefix('penjualan')->name('penjualan.')->group(function () {
            Route::get('/', [PenjualanController::class, 'index'])->name('index');
            Route::get('/data', [PenjualanController::class, 'data'])->name('data');
            Route::get('/{id}', [PenjualanController::class, 'show'])->name('show');
            Route::delete('/{id}', [PenjualanController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/nota-kecil', [PenjualanController::class, 'cetakNotaKecil'])->name('notaKecil');
            Route::get('/{id}/nota-besar', [PenjualanController::class, 'cetakNotaBesar'])->name('notaBesar');
        });

        // TRANSAKSI (POS)
        Route::prefix('transaksi')->name('transaksi.')->group(function () {
            Route::get('/baru', [PenjualanController::class, 'create'])->name('baru');
            Route::get('/', [PenjualanDetailController::class, 'index'])->name('index');
            Route::post('/simpan', [PenjualanController::class, 'store'])->name('simpan');
            Route::get('/selesai', [PenjualanController::class, 'selesai'])->name('selesai');
            Route::get('/nota-kecil', [PenjualanController::class, 'notaKecil'])->name('nota_kecil');
            Route::get('/nota-besar', [PenjualanController::class, 'notaBesar'])->name('nota_besar');

            // Detail transaksi
            Route::get('/{id}/data', [PenjualanDetailController::class, 'data'])->name('data');
            Route::get('/loadform/{diskon}/{total}/{diterima}', [PenjualanDetailController::class, 'loadForm'])->name('load_form');
            Route::post('/', [PenjualanDetailController::class, 'store'])->name('store');
            Route::put('/{transaksi}', [PenjualanDetailController::class, 'update'])->name('update');
            Route::delete('/{transaksi}', [PenjualanDetailController::class, 'destroy'])->name('destroy');
        });

        // ==================== FAVORIT PRODUK ====================

        // API untuk transaksi (semua admin & kasir)
        Route::get('/transactions/favorites', [FavoriteProductController::class, 'forTransaction'])
            ->name('transactions.favorites');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY ROUTES (Level 1)
    |--------------------------------------------------------------------------
    */
    Route::middleware('level:1')->group(function () {

        // ==================== LAPORAN ====================

        // LAPORAN UMUM
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/data/{awal}/{akhir}', [LaporanController::class, 'data'])->name('data');
            Route::get('/pdf/{awal}/{akhir}', [LaporanController::class, 'exportPDF'])->name('export_pdf');
        });

        // LAPORAN MEMBER (Member Statistics & Analytics)
        Route::prefix('member-stats')->name('member_stats.')->group(function () {
            Route::get('/', [MemberStatsController::class, 'index'])->name('index');
            Route::get('/data', [MemberStatsController::class, 'data'])->name('data');
            Route::get('/{id_member}/detail', [MemberStatsController::class, 'detail'])->name('detail');

            // Export
            Route::get('/export/csv', [MemberStatsController::class, 'exportCsv'])->name('export_csv');
            Route::get('/export/pdf', [MemberStatsController::class, 'exportPdf'])->name('export_pdf');

            // Sync & Utilities
            Route::post('/sync', [MemberStatsController::class, 'syncData'])->name('sync');
            Route::get('/summary', [MemberStatsController::class, 'getSummary'])->name('get_summary');
            Route::get('/system-status', [MemberStatsController::class, 'getSystemStatus'])->name('system_status');
        });

        // ==================== SISTEM ====================

        // USER MANAGEMENT
        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);

        // PENGATURAN
        Route::prefix('setting')->name('setting.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::get('/first', [SettingController::class, 'show'])->name('show');
            Route::post('/', [SettingController::class, 'update'])->name('update');

            // FAVORIT PRODUK
            Route::get('/favorites', [FavoriteProductController::class, 'index'])->name('favorites');
            Route::post('/favorites/add', [FavoriteProductController::class, 'add'])->name('favorites.add');
            Route::patch('/favorites/reorder', [FavoriteProductController::class, 'reorder'])->name('favorites.reorder');
            Route::patch('/favorites/toggle/{favorite}', [FavoriteProductController::class, 'toggle'])->name('favorites.toggle');
            Route::delete('/favorites/{favorite}', [FavoriteProductController::class, 'destroy'])->name('favorites.destroy');
        });

        // API untuk pencarian produk (admin only)
        Route::get('/api/products/search', [FavoriteProductController::class, 'searchProducts'])
            ->name('api.products.search');
    });

    /*
    |--------------------------------------------------------------------------
    | SHARED ROUTES (Admin & Kasir)
    |--------------------------------------------------------------------------
    */
    Route::middleware('level:1,2')->group(function () {

        // PROFIL USER
        Route::prefix('profil')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'profil'])->name('profil');
            Route::post('/', [UserController::class, 'updateProfil'])->name('update_profil');
        });

        // LAPORAN KASIR (bisa diakses admin & kasir)
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/kasir', [LaporanKasirController::class, 'index'])->name('kasir.index');
            Route::post('/kasir/generate', [LaporanKasirController::class, 'generateReport'])->name('kasir.generate');
        });
    });
});
