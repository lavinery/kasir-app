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
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ADMIN & KASIR: akses Master data (kategori, member, supplier)
    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
        Route::resource('/kategori', KategoriController::class);

        Route::get('/member/data', [MemberController::class, 'data'])->name('member.data');
        Route::post('/member/cetak-member', [MemberController::class, 'cetakMember'])->name('member.cetak_member');
        Route::resource('/member', MemberController::class);

        Route::prefix('member-stats')->name('member_stats.')->group(function () {
            // Basic routes
            Route::get('/', [MemberStatsController::class, 'index'])->name('index');
            Route::get('/data', [MemberStatsController::class, 'data'])->name('data');
            Route::get('/{id_member}/detail', [MemberStatsController::class, 'detail'])->name('detail');

            // Export routes
            Route::get('/export/csv', [MemberStatsController::class, 'exportCsv'])->name('export_csv');
            Route::get('/export/pdf', [MemberStatsController::class, 'exportPdf'])->name('export_pdf');

            // 🔄 NEW: Sync & Utility Routes
            Route::post('/sync', [MemberStatsController::class, 'syncData'])->name('sync');
            Route::get('/summary', [MemberStatsController::class, 'getSummary'])->name('get_summary');
            Route::get('/system-status', [MemberStatsController::class, 'getSystemStatus'])->name('system_status');
        });

        Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
        Route::resource('/supplier', SupplierController::class);

        // Produk
        Route::get('/produk/data', [ProdukController::class, 'data'])->name('produk.data');
        Route::resource('produk', ProdukController::class);
        Route::post('/produk/delete-selected', [ProdukController::class, 'deleteSelected'])->name('produk.delete_selected');
        Route::post('/produk/cetak-barcode', [ProdukController::class, 'cetakBarcode'])->name('produk.cetak_barcode');
        Route::get('/produk/barcode-pdf', [ProdukController::class, 'barcodePDF'])->name('produk.barcode_pdf');
        Route::post('/produk/barcode-png', [ProdukController::class, 'barcodePNG'])->name('produk.barcode_png');
        Route::post('/produk/cetak-daftar', [ProdukController::class, 'cetakDaftar'])->name('produk.cetak_daftar');
        Route::post('/produk/export-excel', [ProdukController::class, 'exportExcel'])->name('produk.export_excel');
        Route::post('/produk/cetak-barcode-label-33x15', [ProdukController::class, 'cetakBarcodeLabel33x15'])->name('produk.cetak_barcode_label_33x15');

        // Routes untuk cetak barcode label
        Route::post('/produk/cetak-barcode-label-105', [ProdukController::class, 'cetakBarcodeLabel105'])->name('produk.cetak_barcode_label_105');
        Route::post('/produk/cetak-barcode-label-107', [ProdukController::class, 'cetakBarcodeLabel107'])->name('produk.cetak_barcode_label_107');

        // Pembelian
        Route::get('/pembelian/data', [PembelianController::class, 'data'])->name('pembelian.data');
        Route::get('/pembelian/{id}/create', [PembelianController::class, 'create'])->name('pembelian.create');
        Route::resource('/pembelian', PembelianController::class)->except('create');

        Route::get('/pembelian_detail/{id}/data', [PembelianDetailController::class, 'data'])->name('pembelian_detail.data');
        Route::get('/pembelian_detail/loadform/{diskon}/{total}', [PembelianDetailController::class, 'loadForm'])->name('pembelian_detail.load_form');
        Route::resource('/pembelian_detail', PembelianDetailController::class)->except('create', 'show', 'edit');

        // Pengeluaran
        Route::get('/pengeluaran/data', [PengeluaranController::class, 'data'])->name('pengeluaran.data');
        Route::resource('/pengeluaran', PengeluaranController::class);

        // Penjualan
        Route::get('/penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
        Route::get('/penjualan/{id}/nota-kecil', [PenjualanController::class, 'cetakNotaKecil'])->name('penjualan.notaKecil');
        Route::get('/penjualan/{id}/nota-besar', [PenjualanController::class, 'cetakNotaBesar'])->name('penjualan.notaBesar');


        // Transaksi
        Route::get('/transaksi/baru', [PenjualanController::class, 'create'])->name('transaksi.baru');
        Route::post('/transaksi/simpan', [PenjualanController::class, 'store'])->name('transaksi.simpan');
        Route::get('/transaksi/selesai', [PenjualanController::class, 'selesai'])->name('transaksi.selesai');
        Route::get('/transaksi/nota-kecil', [PenjualanController::class, 'notaKecil'])->name('transaksi.nota_kecil');
        Route::get('/transaksi/nota-besar', [PenjualanController::class, 'notaBesar'])->name('transaksi.nota_besar');

        Route::get('/transaksi/{id}/data', [PenjualanDetailController::class, 'data'])->name('transaksi.data');
        Route::get('/transaksi/loadform/{diskon}/{total}/{diterima}', [PenjualanDetailController::class, 'loadForm'])->name('transaksi.load_form');
        Route::resource('/transaksi', PenjualanDetailController::class)->except('create', 'show', 'edit');
    });

    // ADMIN ONLY
    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/data/{awal}/{akhir}', [LaporanController::class, 'data'])->name('laporan.data');
        Route::get('/laporan/pdf/{awal}/{akhir}', [LaporanController::class, 'exportPDF'])->name('laporan.export_pdf');

        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);

        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/first', [SettingController::class, 'show'])->name('setting.show');
        Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
    });

    // PROFIL
    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/profil', [UserController::class, 'profil'])->name('user.profil');
        Route::post('/profil', [UserController::class, 'updateProfil'])->name('user.update_profil');
    });

    // Laporan kasir (boleh diakses semua yang login)
    Route::get('/laporan/kasir', [LaporanKasirController::class, 'index'])->name('laporan.kasir.index');
    Route::post('/laporan/kasir/generate', [LaporanKasirController::class, 'generateReport'])->name('laporan.kasir.generate');
});
Route::prefix('barang-habis')->name('barang_habis.')->group(function () {
    Route::get('/', [BarangHabisController::class, 'index'])->name('index');
    Route::get('/data', [BarangHabisController::class, 'data'])->name('data');
    Route::get('/products', [BarangHabisController::class, 'getAvailableProducts'])->name('products');
    Route::post('/manual', [BarangHabisController::class, 'storeManual'])->name('store_manual');
    Route::put('/{id}', [BarangHabisController::class, 'update'])->name('update');
    Route::delete('/{id}', [BarangHabisController::class, 'destroy'])->name('destroy');
    Route::get('/export-pdf', [BarangHabisController::class, 'exportPdf'])->name('export_pdf');

    // BULK ACTIONS - TAMBAHAN BARU
    Route::delete('/bulk/delete', [BarangHabisController::class, 'bulkDestroy'])->name('bulk_destroy');
    Route::get('/export-pdf-by-ids', [BarangHabisController::class, 'exportPdfByIds'])->name('export_pdf_by_ids');

    // SYNC TRIGGER - TAMBAHAN BARU
    Route::post('/sync', [BarangHabisController::class, 'syncAll'])->name('sync');
    Route::get('/sync-stats', [BarangHabisController::class, 'getSyncStats'])->name('sync_stats');
});

Route::middleware(['auth'])->group(function () {
    // Routes untuk Settings Favorit (hanya admin) - sesuaikan dengan struktur settings yang ada
    Route::middleware('can:admin')->prefix('setting')->name('setting.')->group(function () {
        Route::get('/favorites', [FavoriteProductController::class, 'index'])->name('favorites');
        Route::post('/favorites/add', [FavoriteProductController::class, 'add'])->name('favorites.add');
        Route::patch('/favorites/reorder', [FavoriteProductController::class, 'reorder'])->name('favorites.reorder');
        Route::patch('/favorites/toggle/{favorite}', [FavoriteProductController::class, 'toggle'])->name('favorites.toggle');
        Route::delete('/favorites/{favorite}', [FavoriteProductController::class, 'destroy'])->name('favorites.destroy');
    });

    // API untuk pencarian produk (admin only)
    Route::get('/api/products/search', [FavoriteProductController::class, 'searchProducts'])
        ->middleware('can:admin')
        ->name('api.products.search');

    // Route untuk panel transaksi (semua user yang login)
    Route::get('/transactions/favorites', [FavoriteProductController::class, 'forTransaction'])
        ->name('transactions.favorites');
});