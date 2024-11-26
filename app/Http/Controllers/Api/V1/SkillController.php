<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    public function index()
    {
        // Mengambil semua data skill dari tabel
        $skills = Skill::all();

        // Mengembalikan data skill dalam format JSON
        return response()->json([
            'message' => 'Skills retrieved successfully',
            'data' => $skills,
        ], 200);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|min:3',
        ]);

        // Membuat slug otomatis dari name yang dimasukkan
        $slug = Str::slug($validated['name']);

        // Mengecek apakah slug sudah ada di database
        if (Skill::where('slug', $slug)->exists()) {
            // Jika slug sudah ada, tambahkan angka atau string untuk membuatnya unik
            $slug = $slug . '-' . uniqid();
        }

        // Menambahkan slug ke data yang akan disimpan
        $validated['slug'] = $slug;

        // Menyimpan data ke dalam database
        $skill = Skill::create($validated);

        // Mengembalikan respons dengan data skill yang baru dibuat
        return response()->json(['message' => 'Skill created successfully', 'data' => $skill], 201);
    }

    public function update(Request $request, $id)
    {
        // Mencari skill berdasarkan ID
        $skill = Skill::find($id);

        // Jika skill tidak ditemukan, kembalikan pesan error
        if (!$skill) {
            return response()->json([
                'message' => 'Skill not found',
            ], 404);
        }

        // Validasi input
        $validated = $request->validate([
            'name' => 'required|min:3',
        ]);

        // Membuat slug otomatis dari name yang dimasukkan
        $slug = Str::slug($validated['name']);

        // Mengecek apakah slug sudah ada di database (kecuali skill yang sedang diupdate)
        if (Skill::where('slug', $slug)->where('id', '!=', $skill->id)->exists()) {
            // Jika slug sudah ada, tambahkan angka atau string untuk membuatnya unik
            $slug = $slug . '-' . uniqid();
        }

        // Update slug di data yang akan disimpan
        $validated['slug'] = $slug;

        // Update data skill
        $skill->update($validated);

        // Mengembalikan respons dengan data skill yang baru diperbarui
        return response()->json([
            'message' => 'Skill updated successfully',
            'data' => $skill,
        ], 200);
    }

    public function show($id)
    {
        // Mencari skill berdasarkan ID
        $skill = Skill::find($id);

        // Jika skill tidak ditemukan, kembalikan pesan error
        if (!$skill) {
            return response()->json([
                'message' => 'Skill not found',
            ], 404);
        }

        // Jika skill ditemukan, kembalikan data skill
        return response()->json([
            'message' => 'Skill retrieved successfully',
            'data' => $skill,
        ], 200);
    }

    public function destroy($id)
    {
        // Menemukan skill berdasarkan ID
        $skill = Skill::findOrFail($id);

        // Menghapus skill
        $skill->delete();

        // Mengembalikan respons dengan pesan sukses
        return response()->json([
            'message' => 'Skill deleted successfully'
        ], 200);
    }
}
