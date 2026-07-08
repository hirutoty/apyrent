<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $data = Service::with('user')->latest()->paginate(15)->withQueryString();

        return view('admin.service.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_service' => 'required|unique:service,nama_service',
            'biaya_default' => 'required|numeric',
        ], [
            'nama_service.required' => 'Nama service wajib diisi',
            'nama_service.unique' => 'Nama service sudah digunakan, tidak boleh sama',
            'biaya_default.required' => 'Biaya default wajib diisi',
            'biaya_default.numeric' => 'Biaya default harus berupa angka',
        ]);

        Service::create([
            'user_id' => Auth::id(),
            'nama_service' => $request->nama_service,
            'biaya_default' => $request->biaya_default,
        ]);

        return back()->with('success', 'Service berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_service' => 'required|unique:service,nama_service,' . $id,
            'biaya_default' => 'required|numeric',
        ], [
            'nama_service.required' => 'Nama service wajib diisi',
            'nama_service.unique' => 'Nama service sudah digunakan, silakan gunakan nama lain',
            'biaya_default.required' => 'Biaya default wajib diisi',
            'biaya_default.numeric' => 'Biaya default harus berupa angka',
        ]);

        $data = Service::findOrFail($id);

        $data->update([
            'user_id' => Auth::id(),
            'nama_service' => $request->nama_service,
            'biaya_default' => $request->biaya_default,
        ]);

        return back()->with('success', 'Service berhasil diupdate');
    }

    public function destroy($id)
    {
        Service::findOrFail($id)->delete();

        return back()->with('success', 'Service berhasil dihapus');
    }
}