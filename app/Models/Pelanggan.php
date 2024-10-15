<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use HasFactory, SoftDeletes; 

    protected $table = 'pelanggan'; 
    protected $fillable = ['id_pelanggan', 'nama', 'domisili', 'jenis_kelamin'];

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'kode_pelanggan', 'id_pelanggan');
    }
}
