<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangHabis extends Model
{
    use HasFactory;

    protected $table = 'barang_habis';
    protected $fillable = [
        'id_produk',
        'tipe',
        'keterangan'
    ];

    /**
     * Relasi dengan model Produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * Relasi dengan kategori melalui produk
     */
    public function kategori()
    {
        return $this->hasOneThrough(
            Kategori::class,
            Produk::class,
            'id_produk',        // Foreign key on produk table
            'id_kategori',      // Foreign key on kategori table  
            'id_produk',        // Local key on barang_habis table
            'id_kategori'       // Local key on produk table
        );
    }

    /**
     * Scope untuk filter berdasarkan kategori
     */
    public function scopeByKategori($query, $idKategori)
    {
        if ($idKategori) {
            return $query->whereHas('produk', function ($q) use ($idKategori) {
                $q->where('id_kategori', $idKategori);
            });
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan tipe (auto/manual)
     */
    public function scopeByTipe($query, $tipe)
    {
        if ($tipe) {
            return $query->where('tipe', $tipe);
        }
        return $query;
    }

    /**
     * Accessor untuk mendapatkan badge tipe
     */
    public function getTipeBadgeAttribute()
    {
        return $this->tipe === 'auto'
            ? '<span class="badge badge-warning">Auto</span>'
            : '<span class="badge badge-info">Manual</span>';
    }
}
