<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePelangganTable extends Migration
{
    public function up()
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id(); 
            $table->string('id_pelanggan')->unique(); 
            $table->string('nama'); 
            $table->string('domisili'); 
            $table->enum('jenis_kelamin', ['L', 'P']); 
            $table->timestamps(); 
            $table->softDeletes(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('pelanggan');
    }
}
