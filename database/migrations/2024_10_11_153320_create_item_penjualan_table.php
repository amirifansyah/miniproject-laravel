<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemPenjualanTable extends Migration
{
    public function up()
    {
        Schema::create('item_penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('nota');
            $table->string('kode_barang');
            $table->integer('qty');
            $table->timestamps();
            $table->softDeletes(); 

            $table->foreign('nota')->references('id_nota')->on('penjualan')->onDelete('cascade');
            $table->foreign('kode_barang')->references('kode')->on('barang')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_penjualan');
    }
}
