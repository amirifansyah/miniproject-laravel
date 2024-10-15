<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penjualan extends Model
{
    use HasFactory, SoftDeletes; 

    protected $table = 'penjualan'; 
    protected $fillable = ['id_nota', 'tanggal', 'kode_pelanggan', 'subtotal'];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'kode_pelanggan', 'id_pelanggan');
    }

    public function itemPenjualans()
    {
        return $this->hasMany(ItemPenjualan::class, 'nota', 'id_nota');
    }
}
