<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PenjualanController;

Route::apiResource('pelanggan', PelangganController::class);
Route::apiResource('barang', BarangController::class);
Route::apiResource('penjualan', PenjualanController::class);

Route::get('/test', function () {
    return response()->json(['message' => 'berhasil']);
});