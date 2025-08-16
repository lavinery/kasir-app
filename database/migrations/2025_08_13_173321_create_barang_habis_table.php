<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangHabisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_habis', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_produk');
            $table->enum('tipe', ['auto', 'manual'])->default('auto');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');

            // Unique constraint untuk mencegah duplikasi
            $table->unique('id_produk');

            // Index untuk performa query
            $table->index(['tipe', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barang_habis');
    }
}
