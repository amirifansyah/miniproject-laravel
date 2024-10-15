<?php

namespace App\Http\Controllers;

use App\Models\ItemPenjualan;
use Illuminate\Http\Request;

class ItemPenjualanController extends Controller
{
    public function index()
    {
        return ItemPenjualan::with(['penjualan', 'barang'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nota' => 'required|exists:penjualans,id_nota',
            'kode_barang' => 'required|exists:barangs,kode',
            'qty' => 'required|integer|min:1',
        ]);

        return ItemPenjualan::create($request->all());
    }

    public function show($id)
    {
        return ItemPenjualan::with(['penjualan', 'barang'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $itemPenjualan = ItemPenjualan::findOrFail($id);
        $itemPenjualan->update($request->all());
        return $itemPenjualan;
    }

    public function destroy($id)
    {
        $itemPenjualan = ItemPenjualan::findOrFail($id);
        $itemPenjualan->delete();
        return response()->noContent();
    }
}
