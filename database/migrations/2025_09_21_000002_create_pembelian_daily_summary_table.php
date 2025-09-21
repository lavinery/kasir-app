<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembelianDailySummaryTable extends Migration
{
    public function up()
    {
        Schema::create('pembelian_daily_summary', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->integer('total_transaksi')->default(0);
            $table->integer('total_item')->default(0);
            $table->integer('total_pembelian')->default(0);
            $table->timestamps();

            $table->index(['tanggal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembelian_daily_summary');
    }
}