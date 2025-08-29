<?php

namespace App\Observers;

use App\Models\Produk;
use App\Models\BarangHabis;

class ProdukStockObserver
{
    /**
     * Handle the Produk "created" event.
     */
    public function created(Produk $produk): void
    {
        // Check if stock is low and add to barang habis
        $this->checkAndSyncBarangHabis($produk);
    }

    /**
     * Handle the Produk "updated" event.
     */
    public function updated(Produk $produk): void
    {
        // Check if stock changed and sync barang habis
        if ($produk->wasChanged('stok')) {
            $this->checkAndSyncBarangHabis($produk);
        }
    }

    /**
     * Handle the Produk "deleted" event.
     */
    public function deleted(Produk $produk): void
    {
        // Remove from barang habis if exists
        BarangHabis::where('id_produk', $produk->id_produk)->delete();
    }

    /**
     * Check and sync barang habis based on stock threshold
     */
    private function checkAndSyncBarangHabis(Produk $produk): void
    {
        $threshold = config('app.stock_threshold', 5); // Default threshold 5
        
        // Check if product exists in barang habis
        $barangHabis = BarangHabis::where('id_produk', $produk->id_produk)->first();
        
        if ($produk->stok <= $threshold) {
            // Stock is low, add to barang habis if not exists
            if (!$barangHabis) {
                BarangHabis::create([
                    'id_produk' => $produk->id_produk,
                    'tipe' => 'auto',
                    'keterangan' => "Auto sync - stok {$produk->stok} ≤ {$threshold} (" . now()->format('Y-m-d H:i:s') . ")"
                ]);
                
                \Log::info("Auto added to barang habis: {$produk->nama_produk} (stok: {$produk->stok})");
            }
        } else {
            // Stock is sufficient, remove from barang habis if exists and is auto
            if ($barangHabis && $barangHabis->tipe === 'auto') {
                $barangHabis->delete();
                
                \Log::info("Auto removed from barang habis: {$produk->nama_produk} (stok: {$produk->stok})");
            }
        }
    }
}
