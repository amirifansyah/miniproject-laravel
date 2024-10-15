<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\ItemPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $page = $request->input('page');

        $query = Penjualan::with(['pelanggan', 'itemPenjualans.barang'])
            ->orderBy('created_at', 'desc');

        if ($page) {
            $penjualan = $query->paginate($perPage);
        } else {
            $penjualan = $query->get();
        }

        return response()->json($penjualan);
    }


    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kode_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'items' => 'required|array',
            'items.*.kode_barang' => 'required|exists:barang,kode',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $barang = Barang::where('kode', $item['kode_barang'])->first();
            if ($barang) {
                $subtotal += $barang->harga * $item['qty'];
            }
        }

        DB::beginTransaction();
        try {
            $maxId = Penjualan::max('id');
            $nextId = $maxId ? $maxId + 1 : 1;
            $newIdNota = 'NOTA_' . $nextId;
            $penjualanData = [
                'id_nota' => $newIdNota,
                'tanggal' => $request->tanggal,
                'kode_pelanggan' => $request->kode_pelanggan,
                'subtotal' => $subtotal,
            ];
            $penjualan = Penjualan::create($penjualanData);
            foreach ($request->items as $item) {
                ItemPenjualan::create([
                    'nota' => $penjualan->id_nota,
                    'kode_barang' => $item['kode_barang'],
                    'qty' => $item['qty'],
                ]);
            }
            DB::commit();
            return response()->json($penjualan, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menyimpan data penjualan. ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return Penjualan::with(['pelanggan', 'itemPenjualans'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update($request->only(['tanggal', 'kode_pelanggan', 'subtotal']));
        if (isset($request->items)) {

            $penjualan->itemPenjualans()->delete();
            foreach ($request->items as $item) {
                ItemPenjualan::create([
                    'nota' => $penjualan->id_nota,
                    'kode_barang' => $item['kode_barang'],
                    'qty' => $item['qty'],
                ]);
            }
        }
        return response()->json($penjualan);
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->itemPenjualans()->delete();
        $penjualan->delete();
        return response()->noContent();
    }
}
