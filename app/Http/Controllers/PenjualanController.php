<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\ItemPenjualan; // Tambahkan model ItemPenjualan
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
    // Validasi input dari request
    $request->validate([
        'tanggal' => 'required|date',
        'kode_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
        'items' => 'required|array',
        'items.*.kode_barang' => 'required|exists:barang,kode',
        'items.*.qty' => 'required|integer|min:1',
    ]);

    // Hitung subtotal berdasarkan items
    $subtotal = 0;

    foreach ($request->items as $item) {
        $barang = Barang::where('kode', $item['kode_barang'])->first();
        if ($barang) {
            $subtotal += $barang->harga * $item['qty'];
        }
    }

    // Mulai transaksi
    DB::beginTransaction();

    try {
        // Mengambil ID terbesar dari penjualan
        $maxId = Penjualan::max('id'); // Ambil ID terbesar
        $nextId = $maxId ? $maxId + 1 : 1; // Jika tidak ada penjualan, mulai dari 1
        $newIdNota = 'NOTA_' . $nextId; // Buat id_nota baru

        // Buat penjualan
        $penjualanData = [
            'id_nota' => $newIdNota,
            'tanggal' => $request->tanggal,
            'kode_pelanggan' => $request->kode_pelanggan,
            'subtotal' => $subtotal,
        ];

        $penjualan = Penjualan::create($penjualanData);

        // Simpan item penjualan
        foreach ($request->items as $item) {
            ItemPenjualan::create([
                'nota' => $penjualan->id_nota,
                'kode_barang' => $item['kode_barang'],
                'qty' => $item['qty'],
            ]);
        }

        // Commit transaksi
        DB::commit();

        return response()->json($penjualan, 201);
    } catch (\Exception $e) {
        // Rollback jika ada kesalahan
        DB::rollBack();
        return response()->json(['error' => 'Gagal menyimpan data penjualan. ' . $e->getMessage()], 500);
    }
}

    

    public function show($id)
    {
        return Penjualan::with(['pelanggan', 'itemPenjualans'])->findOrFail($id); // Ambil penjualan lengkap
    }

    public function update(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update($request->only(['tanggal', 'kode_pelanggan', 'subtotal']));

        // Update item penjualan jika perlu
        if (isset($request->items)) {
            // Hapus item lama dan simpan item baru
            $penjualan->itemPenjualans()->delete(); // Hapus item lama
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
        $penjualan->itemPenjualans()->delete(); // Hapus item penjualan terkait
        $penjualan->delete(); // Hapus penjualan
        return response()->noContent();
    }
}
