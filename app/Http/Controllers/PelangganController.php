<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10); 
        $page = $request->input('page'); 
        $query = Pelanggan::orderBy('created_at', 'desc'); 
    
        if ($page) {
            $pelanggan = $query->paginate($perPage);
        } else {
            $pelanggan = $query->get(); 
        }
    
        return response()->json($pelanggan);
    }    

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'domisili' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
        ]);
    
        $maxId = Pelanggan::withTrashed()->max('id'); 
        $nextId = $maxId ? $maxId + 1 : 1; 
        $id_pelanggan = 'PELANGGAN_' . $nextId; 
    
        while (Pelanggan::withTrashed()->where('id_pelanggan', $id_pelanggan)->exists()) {
            $nextId++;
            $id_pelanggan = 'PELANGGAN_' . $nextId; 
        }
        
        try {
            $pelanggan = Pelanggan::create(array_merge($request->all(), ['id_pelanggan' => $id_pelanggan]));
            return response()->json($pelanggan, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error saving data: ' . $e->getMessage()], 500);
        }
    }
    

    public function show($id)
    {
        return Pelanggan::withTrashed()->findOrFail($id); 
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::withTrashed()->findOrFail($id);
        $pelanggan->update($request->all());
        return $pelanggan;
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::withTrashed()->findOrFail($id);
        $pelanggan->delete(); 
        return response()->noContent();
    }

    public function restore($id)
    {
        $pelanggan = Pelanggan::withTrashed()->findOrFail($id);
        $pelanggan->restore(); 
        return response()->json($pelanggan);
    }
}
