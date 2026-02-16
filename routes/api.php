<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\PembelianController;
use App\Http\Controllers\Api\PengeluaranController;
use App\Http\Controllers\Api\PenjualanController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Legacy route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Auth (Public)
|--------------------------------------------------------------------------
*/
Route::post('/auth/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes (auth:sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Produk (read - semua user)
    Route::get('/produk', [ProdukController::class, 'index']);
    Route::get('/produk/{id}', [ProdukController::class, 'show']);

    // Kategori (read - semua user)
    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::get('/kategori/{id}', [KategoriController::class, 'show']);

    // Member (read - semua user)
    Route::get('/member', [MemberController::class, 'index']);
    Route::get('/member/{id}', [MemberController::class, 'show']);

    // Supplier (read - semua user)
    Route::get('/supplier', [SupplierController::class, 'index']);
    Route::get('/supplier/{id}', [SupplierController::class, 'show']);

    // Penjualan (read-only)
    Route::get('/penjualan', [PenjualanController::class, 'index']);
    Route::get('/penjualan/{id}', [PenjualanController::class, 'show']);

    // Pembelian (read-only)
    Route::get('/pembelian', [PembelianController::class, 'index']);
    Route::get('/pembelian/{id}', [PembelianController::class, 'show']);

    // Pengeluaran (read-only)
    Route::get('/pengeluaran', [PengeluaranController::class, 'index']);
    Route::get('/pengeluaran/{id}', [PengeluaranController::class, 'show']);

    /*
    |----------------------------------------------------------------------
    | Admin Only (level:1)
    |----------------------------------------------------------------------
    */
    Route::middleware('level:1')->group(function () {
        // Produk CUD
        Route::post('/produk', [ProdukController::class, 'store']);
        Route::put('/produk/{id}', [ProdukController::class, 'update']);
        Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);

        // Kategori CUD
        Route::post('/kategori', [KategoriController::class, 'store']);
        Route::put('/kategori/{id}', [KategoriController::class, 'update']);
        Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);

        // Supplier CUD
        Route::post('/supplier', [SupplierController::class, 'store']);
        Route::put('/supplier/{id}', [SupplierController::class, 'update']);
        Route::delete('/supplier/{id}', [SupplierController::class, 'destroy']);

        // Member delete (admin only)
        Route::delete('/member/{id}', [MemberController::class, 'destroy']);
    });

    /*
    |----------------------------------------------------------------------
    | Admin & Kasir (level:1,2)
    |----------------------------------------------------------------------
    */
    Route::middleware('level:1,2')->group(function () {
        // Member CU
        Route::post('/member', [MemberController::class, 'store']);
        Route::put('/member/{id}', [MemberController::class, 'update']);
    });
});
