<?php

namespace App\Http\Controllers\Api;

use App\Models\Kategori;
use App\Models\Member;
use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Supplier;

class DashboardController extends ApiController
{
    public function stats()
    {
        $today = date('Y-m-d');

        $penjualan_hari_ini = Penjualan::whereDate('created_at', $today)->sum('bayar');
        $pembelian_hari_ini = Pembelian::whereDate('created_at', $today)->sum('bayar');
        $pengeluaran_hari_ini = Pengeluaran::whereDate('created_at', $today)->sum('nominal');

        $penjualan_bulan_ini = Penjualan::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('bayar');

        $transaksi_hari_ini = Penjualan::whereDate('created_at', $today)->count();

        return $this->successResponse([
            'total_kategori'        => Kategori::count(),
            'total_produk'          => Produk::count(),
            'total_supplier'        => Supplier::count(),
            'total_member'          => Member::count(),
            'penjualan_hari_ini'    => $penjualan_hari_ini,
            'pembelian_hari_ini'    => $pembelian_hari_ini,
            'pengeluaran_hari_ini'  => $pengeluaran_hari_ini,
            'pendapatan_hari_ini'   => $penjualan_hari_ini - $pembelian_hari_ini - $pengeluaran_hari_ini,
            'penjualan_bulan_ini'   => $penjualan_bulan_ini,
            'transaksi_hari_ini'    => $transaksi_hari_ini,
        ], 'Statistik dashboard berhasil diambil');
    }
}
