<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Index untuk tabel penjualan (TANPA kolom status)
        Schema::table('penjualan', function (Blueprint $table) {
            // Index untuk filter berdasarkan member dan tanggal
            $table->index(['id_member', 'created_at'], 'idx_member_date');

            // Index untuk filter berdasarkan tanggal saja
            $table->index(['created_at'], 'idx_created_at');

            // Index untuk member
            $table->index(['id_member'], 'idx_id_member');

            // Index untuk user
            $table->index(['id_user'], 'idx_id_user');

            // Index untuk total harga (untuk laporan)
            $table->index(['total_harga'], 'idx_total_harga');
        });

        // Index untuk tabel member (jika ada)
        if (Schema::hasTable('member')) {
            Schema::table('member', function (Blueprint $table) {
                // Cek kolom mana yang ada dulu
                if (Schema::hasColumn('member', 'kode_member')) {
                    $table->index(['kode_member'], 'idx_kode_member');
                }
                if (Schema::hasColumn('member', 'nama')) {
                    $table->index(['nama'], 'idx_nama');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropIndex('idx_member_date');
            $table->dropIndex('idx_created_at');
            $table->dropIndex('idx_id_member');
            $table->dropIndex('idx_id_user');
            $table->dropIndex('idx_total_harga');
        });

        if (Schema::hasTable('member')) {
            Schema::table('member', function (Blueprint $table) {
                if (Schema::hasColumn('member', 'kode_member')) {
                    $table->dropIndex('idx_kode_member');
                }
                if (Schema::hasColumn('member', 'nama')) {
                    $table->dropIndex('idx_nama');
                }
            });
        }
    }
};
