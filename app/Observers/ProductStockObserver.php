<?php

namespace App\Observers;

use App\Models\Produk;
use App\Models\BarangHabis;
use Illuminate\Support\Facades\Log;

class ProductStockObserver
{
    const THRESHOLD_STOK = 5;

    /**
     * Handle the Produk "created" event.
     */
    public function created(Produk $produk)
    {
        $this->syncAutoFromStock($produk);
    }

    /**
     * Handle the Produk "updated" event.
     */
    public function updated(Produk $produk)
    {
        // Hanya sync jika stok berubah
        if ($produk->isDirty('stok')) {
            Log::info('Stock changed for product', [
                'id_produk' => $produk->id_produk,
                'nama_produk' => $produk->nama_produk,
                'old_stock' => $produk->getOriginal('stok'),
                'new_stock' => $produk->stok
            ]);

            $this->syncAutoFromStock($produk);
        }
    }

    /**
     * Handle the Produk "deleting" event.
     */
    public function deleting(Produk $produk)
    {
        try {
            // Hapus dari daftar barang habis jika produk dihapus
            BarangHabis::where('id_produk', $produk->id_produk)->delete();

            Log::info('Product deleted, removed from barang habis', [
                'id_produk' => $produk->id_produk
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing from barang habis on product deletion', [
                'product_id' => $produk->id_produk,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Sync auto detection based on stock
     */
    private function syncAutoFromStock(Produk $produk)
    {
        try {
            $isStokHabis = $produk->stok <= self::THRESHOLD_STOK;
            $existingEntry = BarangHabis::where('id_produk', $produk->id_produk)->first();

            Log::info('Syncing auto detection', [
                'id_produk' => $produk->id_produk,
                'stok' => $produk->stok,
                'is_stok_habis' => $isStokHabis,
                'existing_entry' => $existingEntry ? $existingEntry->tipe : 'none'
            ]);

            if ($isStokHabis) {
                // Jika stok habis dan belum ada di daftar, tambahkan dengan tipe auto
                if (!$existingEntry) {
                    BarangHabis::create([
                        'id_produk' => $produk->id_produk,
                        'tipe' => 'auto',
                        'keterangan' => 'Stok otomatis masuk daftar (≤ ' . self::THRESHOLD_STOK . ')'
                    ]);

                    Log::info('Added to barang habis (auto)', [
                        'id_produk' => $produk->id_produk,
                        'stok' => $produk->stok
                    ]);
                }
            } else {
                // Jika stok sudah aman (> 10) dan ada entry dengan tipe auto, hapus
                if ($existingEntry && $existingEntry->tipe === 'auto') {
                    $existingEntry->delete();

                    Log::info('Removed from barang habis (auto)', [
                        'id_produk' => $produk->id_produk,
                        'stok' => $produk->stok
                    ]);
                }
                // Entry manual tetap dipertahankan
            }
        } catch (\Exception $e) {
            Log::error('Error in syncAutoFromStock', [
                'product_id' => $produk->id_produk,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
