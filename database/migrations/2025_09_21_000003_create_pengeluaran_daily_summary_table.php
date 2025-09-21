<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengeluaranDailySummaryTable extends Migration
{
    public function up()
    {
        Schema::create('pengeluaran_daily_summary', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->integer('total_transaksi')->default(0);
            $table->integer('total_pengeluaran')->default(0);
            $table->timestamps();

            $table->index(['tanggal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengeluaran_daily_summary');
    }
}