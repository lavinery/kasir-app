<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Cek apakah kolom status sudah ada dengan nilai yang tepat
        if (!Schema::hasColumn('penjualan', 'status')) {
            Schema::table('penjualan', function (Blueprint $table) {
                // Tambah kolom status sebagai string dengan default 'posted'
                $table->string('status', 20)->default('posted')->after('id_user');
            });

            // Update semua record existing menjadi 'posted'
            DB::statement("UPDATE penjualan SET status = 'posted' WHERE status IS NULL OR status = ''");
        }

        // Pastikan semua nilai status yang ada adalah 'posted' (bukan 'PAID')
        DB::statement("UPDATE penjualan SET status = 'posted' WHERE status = 'PAID' OR status != 'posted'");

        // Index untuk tabel penjualan
        Schema::table('penjualan', function (Blueprint $table) {
            // Index untuk filter berdasarkan member, status, dan tanggal
            if (!$this->indexExists('penjualan', 'idx_member_status_date')) {
                $table->index(['id_member', 'status', 'created_at'], 'idx_member_status_date');
            }

            // Index untuk filter berdasarkan tanggal saja
            if (!$this->indexExists('penjualan', 'idx_created_at')) {
                $table->index(['created_at'], 'idx_created_at');
            }

            // Index untuk status transaksi
            if (!$this->indexExists('penjualan', 'idx_status')) {
                $table->index(['status'], 'idx_status');
            }

            // Index untuk member (untuk join performance)
            if (!$this->indexExists('penjualan', 'idx_id_member')) {
                $table->index(['id_member'], 'idx_id_member');
            }

            // Index untuk user (jika dibutuhkan untuk laporan kasir)
            if (!$this->indexExists('penjualan', 'idx_id_user')) {
                $table->index(['id_user'], 'idx_id_user');
            }

            // Index untuk total harga (untuk sorting dan filter amount)
            if (!$this->indexExists('penjualan', 'idx_total_harga')) {
                $table->index(['total_harga'], 'idx_total_harga');
            }
        });

        // Index untuk tabel member (untuk performance query member stats)
        if (Schema::hasTable('member')) {
            Schema::table('member', function (Blueprint $table) {
                // Index untuk kode member (sering dicari)
                if (Schema::hasColumn('member', 'kode_member') && !$this->indexExists('member', 'idx_kode_member')) {
                    $table->index(['kode_member'], 'idx_kode_member');
                }

                // Index untuk nama (untuk search)
                if (Schema::hasColumn('member', 'nama') && !$this->indexExists('member', 'idx_nama')) {
                    $table->index(['nama'], 'idx_nama');
                }

                // Index untuk telepon (jika ada)
                if (Schema::hasColumn('member', 'telepon') && !$this->indexExists('member', 'idx_telepon')) {
                    $table->index(['telepon'], 'idx_telepon');
                }

                // Index untuk created_at member (jika dibutuhkan untuk laporan member baru)
                if (Schema::hasColumn('member', 'created_at') && !$this->indexExists('member', 'idx_member_created_at')) {
                    $table->index(['created_at'], 'idx_member_created_at');
                }
            });
        }

        // Index untuk tabel users (jika ada kolom yang dibutuhkan untuk join kasir)
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Index untuk name (untuk performance saat join ke laporan kasir)
                if (Schema::hasColumn('users', 'name') && !$this->indexExists('users', 'idx_name')) {
                    $table->index(['name'], 'idx_name');
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
        // Drop indexes dari penjualan
        Schema::table('penjualan', function (Blueprint $table) {
            $indexes = [
                'idx_member_status_date',
                'idx_created_at',
                'idx_status',
                'idx_id_member',
                'idx_id_user',
                'idx_total_harga'
            ];

            foreach ($indexes as $index) {
                if ($this->indexExists('penjualan', $index)) {
                    $table->dropIndex($index);
                }
            }
        });

        // Drop indexes dari member
        if (Schema::hasTable('member')) {
            Schema::table('member', function (Blueprint $table) {
                $indexes = [
                    'idx_kode_member',
                    'idx_nama',
                    'idx_telepon',
                    'idx_member_created_at'
                ];

                foreach ($indexes as $index) {
                    if ($this->indexExists('member', $index)) {
                        $table->dropIndex($index);
                    }
                }
            });
        }

        // Drop indexes dari users
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if ($this->indexExists('users', 'idx_name')) {
                    $table->dropIndex('idx_name');
                }
            });
        }

        // Jangan drop kolom status karena mungkin dibutuhkan sistem lain
        // Tapi bisa mengembalikan ke nilai default jika diperlukan
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $index)
    {
        try {
            $indexes = Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableIndexes($table);

            return array_key_exists($index, $indexes);
        } catch (\Exception $e) {
            // Jika gagal cek index, assume tidak ada (untuk safety)
            return false;
        }
    }
};
