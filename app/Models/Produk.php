<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $guarded = [];

    /**
     * Relasi dengan model Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Relasi dengan BarangHabis
     */
    public function barangHabis()
    {
        return $this->hasOne(BarangHabis::class, 'id_produk', 'id_produk');
    }

    /**
     * Check apakah produk masuk kategori stok habis (≤ 10)
     */
    public function isStokHabis()
    {
        return $this->stok <= 10;
    }

    /**
     * Check apakah produk sudah ada di daftar barang habis
     */
    public function isInDaftarHabis()
    {
        return $this->barangHabis()->exists();
    }

    /**
     * Accessor untuk format harga
     */
    public function getHargaJualFormatAttribute()
    {
        return number_format($this->harga_jual, 0, ',', '.');
    }

    /**
     * Accessor untuk format harga beli
     */
    public function getHargaBeliFormatAttribute()
    {
        return number_format($this->harga_beli, 0, ',', '.');
    }
}
