<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10); 
        $page = $request->input('page'); 
        $query = Barang::orderBy('created_at', 'desc'); 
    
        if ($page) {
            $barang = $query->paginate($perPage);
        } else {
            $barang = $query->get(); 
        }
    
        return response()->json($barang);
    }    

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'harga' => 'required|numeric',
        ]);
        
        $maxId = Barang::withTrashed()->max('id'); 
        $nextId = $maxId ? $maxId + 1 : 1; 
        $kode_barang = 'BRG_' . $nextId; 
        
        while (Barang::withTrashed()->where('kode', $kode_barang)->exists()) {
            $nextId++;
            $kode_barang = 'BRG_' . $nextId; 
        }

        try {
            $barang = Barang::create(array_merge($request->all(), ['kode' => $kode_barang]));
            return response()->json($barang, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error saving data: ' . $e->getMessage()], 500);
        }
    }



    public function show($id)
    {
        return Barang::withTrashed()->findOrFail($id); 
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
        $barang->delete(); 
        return response()->noContent();
    }

    public function restore($id)
    {
        $barang = Barang::withTrashed()->findOrFail($id);
        $barang->restore(); 
        return response()->json($barang);
    }
}
