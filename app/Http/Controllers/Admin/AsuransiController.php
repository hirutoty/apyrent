<?php

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Asuransi;

    class AsuransiController extends Controller
    {
        public function index()
        {
            $data = Asuransi::with('user')->latest()->paginate(15)->withQueryString();

            return view('admin.asuransi.index', compact('data'));
        }

        public function store(Request $request)
    {
        $request->validate([
            'nama_asuransi' => 'required|unique:asuransi,nama_asuransi',
            'alamat' => 'nullable',
            'nama_marketing' => 'required',
            'kontak_marketing' => 'required',
            'nama_bengkel' => 'nullable',
            'kontak_bengkel' => 'nullable',
        ], [
            'nama_asuransi.required' => 'Nama asuransi wajib diisi',
            'nama_asuransi.unique' => 'Nama asuransi sudah terdaftar, tidak boleh sama',
        ]);

        Asuransi::create([
            'user_id' => Auth::id(),
            'nama_asuransi' => $request->nama_asuransi,
            'alamat' => $request->alamat,
            'nama_marketing' => $request->nama_marketing,
            'kontak_marketing' => $request->kontak_marketing,
            'nama_bengkel' => $request->nama_bengkel,
            'kontak_bengkel' => $request->kontak_bengkel,
        ]);

        return back()->with('success', 'Data berhasil ditambahkan');
    }

        public function update(Request $request, $id)
    {
        $request->validate([
            'nama_asuransi' => 'required|unique:asuransi,nama_asuransi,' . $id,
            'alamat' => 'nullable',
            'nama_marketing' => 'required',
            'kontak_marketing' => 'required',
            'nama_bengkel' => 'nullable',
            'kontak_bengkel' => 'nullable',
        ], [
            'nama_asuransi.required' => 'Nama asuransi wajib diisi',
            'nama_asuransi.unique' => 'Nama asuransi sudah digunakan, tidak boleh sama',
        ]);

        $asuransi = Asuransi::findOrFail($id);

        $asuransi->update([
            'nama_asuransi' => $request->nama_asuransi,
            'alamat' => $request->alamat,
            'nama_marketing' => $request->nama_marketing,
            'kontak_marketing' => $request->kontak_marketing,
            'nama_bengkel' => $request->nama_bengkel,
            'kontak_bengkel' => $request->kontak_bengkel,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

        public function destroy($id)
        {
            Asuransi::findOrFail($id)->delete();

            return back()->with('success', 'Data berhasil dihapus');
        }
    }