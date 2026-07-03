<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JenisAsuransi;

class JenisAsuransiController extends Controller
{
    public function index()
    {
        $data = JenisAsuransi::latest()->get();
        return view('admin.asuransi.jenis_asuransi', compact('data'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_jenis' => 'required|string|max:255|unique:jenis_asuransi,nama_jenis',
    ], [
        'nama_jenis.required' => 'Nama jenis wajib diisi',
        'nama_jenis.unique' => 'Nama jenis sudah ada, tidak boleh sama',
    ]);

    JenisAsuransi::create($request->all());

    return back()->with('success', 'Data berhasil ditambahkan');
}

    public function update(Request $request, $id)
{
    $data = JenisAsuransi::findOrFail($id);

    $request->validate([
        'nama_jenis' => 'required|string|max:255|unique:jenis_asuransi,nama_jenis,' . $id,
    ], [
        'nama_jenis.required' => 'Nama jenis wajib diisi',
        'nama_jenis.unique' => 'Nama jenis sudah digunakan',
    ]);

    $data->update($request->all());

    return back()->with('success', 'Data berhasil diupdate');
}

    public function destroy($id)
    {
        JenisAsuransi::findOrFail($id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }
}