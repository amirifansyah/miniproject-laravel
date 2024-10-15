<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualanTable extends Migration
{
    public function up()
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('id_nota')->unique();
            $table->date('tanggal');
            $table->string('kode_pelanggan');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            $table->softDeletes(); 

            $table->foreign('kode_pelanggan')->references('id_pelanggan')->on('pelanggan')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penjualan');
    }
}
