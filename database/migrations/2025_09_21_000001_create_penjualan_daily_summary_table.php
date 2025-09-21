<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualanDailySummaryTable extends Migration
{
    public function up()
    {
        Schema::create('penjualan_daily_summary', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->integer('total_transaksi')->default(0);
            $table->integer('total_item')->default(0);
            $table->integer('total_penjualan')->default(0);
            $table->integer('total_keuntungan')->default(0);
            $table->timestamps();

            $table->index(['tanggal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('penjualan_daily_summary');
    }
}