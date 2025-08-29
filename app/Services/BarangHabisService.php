<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\BarangHabis;
use Illuminate\Support\Facades\DB;
use Exception;

class BarangHabisService
{
    const THRESHOLD_STOK = 5;

    /**
     * Sinkronisasi otomatis berdasarkan stok produk
     * Dipanggil setiap kali stok produk berubah
     */
    public function syncAutoFromStock(Produk $produk)
    {
        try {
            DB::beginTransaction();

            $isStokHabis = $produk->stok <= self::THRESHOLD_STOK;
            $existingEntry = BarangHabis::where('id_produk', $produk->id_produk)->first();

            if ($isStokHabis) {
                // Jika stok habis dan belum ada di daftar, tambahkan dengan tipe auto
                if (!$existingEntry) {
                    BarangHabis::create([
                        'id_produk' => $produk->id_produk,
                        'tipe' => 'auto',
                        'keterangan' => 'Stok otomatis masuk daftar (≤ ' . self::THRESHOLD_STOK . ')'
                    ]);
                }
            } else {
                // Jika stok sudah aman (> 10) dan ada entry dengan tipe auto, hapus
                if ($existingEntry && $existingEntry->tipe === 'auto') {
                    $existingEntry->delete();
                }
                // Entry manual tetap dipertahankan
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Tambah produk ke daftar barang habis secara manual
     */
    public function addManual(int $idProduk, ?string $keterangan = null)
    {
        $produk = Produk::find($idProduk);
        if (!$produk) {
            throw new Exception('Produk tidak ditemukan');
        }

        // Check jika sudah ada di daftar
        $existing = BarangHabis::where('id_produk', $idProduk)->first();
        if ($existing) {
            throw new Exception('Produk sudah ada dalam daftar barang habis');
        }

        return BarangHabis::create([
            'id_produk' => $idProduk,
            'tipe' => 'manual',
            'keterangan' => $keterangan ?: 'Ditambahkan manual oleh operator'
        ]);
    }

    /**
     * Update keterangan barang habis
     */
    public function updateKeterangan(int $id, string $keterangan)
    {
        $barangHabis = BarangHabis::find($id);
        if (!$barangHabis) {
            throw new Exception('Data barang habis tidak ditemukan');
        }

        $barangHabis->update(['keterangan' => $keterangan]);
        return $barangHabis;
    }

    /**
     * Hapus dari daftar barang habis
     */
    public function remove(int $id)
    {
        $barangHabis = BarangHabis::find($id);
        if (!$barangHabis) {
            throw new Exception('Data barang habis tidak ditemukan');
        }

        return $barangHabis->delete();
    }

    /**
     * Get data untuk DataTables dengan filter
     */
    public function getDataForDataTables($request)
    {
        $query = BarangHabis::with(['produk.kategori'])
            ->select('barang_habis.*');

        // Filter berdasarkan kategori
        if ($request->has('kategori') && $request->kategori != '') {
            $query->byKategori($request->kategori);
        }

        // Filter berdasarkan tipe
        if ($request->has('tipe') && $request->tipe != '') {
            $query->byTipe($request->tipe);
        }

        // Search
        if ($request->has('search') && $request->search['value'] != '') {
            $search = $request->search['value'];
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
        if ($request->has('order')) {
            $columns = ['id', 'kategori', 'nama_produk', 'stok', 'keterangan', 'tipe'];
            $orderColumn = $columns[$request->order[0]['column']] ?? 'created_at';
            $orderDirection = $request->order[0]['dir'] ?? 'desc';

            if ($orderColumn === 'kategori') {
                $query->join('produk', 'barang_habis.id_produk', '=', 'produk.id_produk')
                    ->join('kategori', 'produk.id_kategori', '=', 'kategori.id_kategori')
                    ->orderBy('kategori.nama_kategori', $orderDirection);
            } elseif ($orderColumn === 'nama_produk' || $orderColumn === 'stok') {
                $query->join('produk', 'barang_habis.id_produk', '=', 'produk.id_produk')
                    ->orderBy("produk.{$orderColumn}", $orderDirection);
            } else {
                $query->orderBy($orderColumn, $orderDirection);
            }
        }

        $data = $query->get();

        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalData,
            'data' => $data
        ];
    }

    /**
     * Sinkronisasi massal - untuk maintenance/cron job
     */
    public function syncAll()
    {
        $allProducts = Produk::all();
        $processed = 0;

        foreach ($allProducts as $produk) {
            $this->syncAutoFromStock($produk);
            $processed++;
        }

        return $processed;
    }
}
