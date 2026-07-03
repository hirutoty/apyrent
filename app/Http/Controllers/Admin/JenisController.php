<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jenis;
use Illuminate\Support\Facades\Auth;

class JenisController extends Controller
{
    public function index()
    {
        $data = Jenis::with('user')->latest()->get();

        return view('admin.jenis.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
    'nama_jenis' => 'required|unique:jenis,nama_jenis',
], [
    'nama_jenis.required' => 'Nama jenis wajib diisi.',
    'nama_jenis.unique' => 'Nama jenis sudah digunakan, silakan gunakan nama lain.',
]);

        Jenis::create([
            'user_id' => Auth::id(),
            'nama_jenis' => $request->nama_jenis
        ]);

        return back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
       $request->validate([
    'nama_jenis' => 'required|unique:jenis,nama_jenis,' . $id,
], [
    'nama_jenis.required' => 'Nama jenis wajib diisi.',
    'nama_jenis.unique' => 'Nama jenis sudah digunakan, silakan gunakan nama lain.',
]);

        $data = Jenis::findOrFail($id);

        $data->update([
            'user_id' => Auth::id(),
            'nama_jenis' => $request->nama_jenis
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        Jenis::findOrFail($id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }
}