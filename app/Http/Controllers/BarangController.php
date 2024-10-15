<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10); // Default to 10 items per page
        $page = $request->input('page'); // Get the current page
    
        // Sort the items in descending order
        $query = Barang::orderBy('created_at', 'desc'); // Change 'created_at' to the appropriate column for sorting
    
        if ($page) {
            $barang = $query->paginate($perPage);
        } else {
            $barang = $query->get(); // Get all items without pagination
        }
    
        return response()->json($barang);
    }    

    public function store(Request $request)
    {
        // Validasi hanya untuk field yang diperlukan untuk barang
        $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'harga' => 'required|numeric',
        ]);

        // Mengambil ID terbesar
        $maxId = Barang::withTrashed()->max('id'); // Termasuk yang dihapus
        $nextId = $maxId ? $maxId + 1 : 1; // Jika tidak ada barang, mulai dari 1
        $kode_barang = 'BRG_' . $nextId; // Membuat kode barang baru

        // Mencari kode yang unik
        while (Barang::withTrashed()->where('kode', $kode_barang)->exists()) {
            $nextId++;
            $kode_barang = 'BRG_' . $nextId; // Membuat kode baru jika sudah ada
        }

        // Menyimpan data barang dengan kode unik
        try {
            $barang = Barang::create(array_merge($request->all(), ['kode' => $kode_barang]));
            return response()->json($barang, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error saving data: ' . $e->getMessage()], 500);
        }
    }



    public function show($id)
    {
        return Barang::withTrashed()->findOrFail($id); // Mendapatkan item termasuk yang terhapus
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::withTrashed()->findOrFail($id);
        $barang->update($request->all());
        return $barang;
    }

    public function destroy($id)
    {
        $barang = Barang::withTrashed()->findOrFail($id);
        $barang->delete(); // Menggunakan soft delete
        return response()->noContent();
    }

    public function restore($id)
    {
        $barang = Barang::withTrashed()->findOrFail($id);
        $barang->restore(); // Mengembalikan item yang dihapus
        return response()->json($barang);
    }
}
