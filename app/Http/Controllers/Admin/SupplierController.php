<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierController extends Controller
{
    public function index()
    {
        $data = Supplier::with('user')->latest()->paginate(15)->withQueryString();

        return view('admin.supplier.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required',
            'no_telp' => 'required',
            'nama_barang' => 'required',
            'jumlah_barang' => 'required|numeric',
            'harga_barang' => 'required|numeric',
        ]);

        // 🔥 CEK DUPLIKAT
        $exists = Supplier::where('nama_supplier', $request->nama_supplier)
            ->where('nama_barang', $request->nama_barang)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Supplier dengan nama dan barang yang sama sudah ada!');
        }

        Supplier::create([
            'user_id' => Auth::id(),
            'nama_supplier' => $request->nama_supplier,
            'no_telp' => $request->no_telp,
            'nama_barang' => $request->nama_barang,
            'jumlah_barang' => $request->jumlah_barang,
            'harga_barang' => $request->harga_barang,
        ]);

        return back()->with('success', 'Supplier berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_supplier' => 'required',
            'no_telp' => 'required',
            'nama_barang' => 'required',
            'jumlah_barang' => 'required|numeric',
            'harga_barang' => 'required|numeric',
        ]);

        $supplier = Supplier::findOrFail($id);

        // 🔥 CEK DUPLIKAT (kecuali data sendiri)
        $exists = Supplier::where('nama_supplier', $request->nama_supplier)
            ->where('nama_barang', $request->nama_barang)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Supplier dengan nama dan barang yang sama sudah digunakan!');
        }

        $supplier->update([
            'user_id' => Auth::id(),
            'nama_supplier' => $request->nama_supplier,
            'no_telp' => $request->no_telp,
            'nama_barang' => $request->nama_barang,
            'jumlah_barang' => $request->jumlah_barang,
            'harga_barang' => $request->harga_barang,
        ]);

        return back()->with('success', 'Supplier berhasil diupdate');
    }

    public function destroy($id)
    {
        Supplier::findOrFail($id)->delete();

        return back()->with('success', 'Supplier berhasil dihapus');
    }

    

public function pdf(Request $request)
{
    $query = Supplier::with('user');

    // ambil keyword search dari input
    if ($request->search) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('nama_supplier', 'like', "%$search%")
              ->orWhere('no_telp', 'like', "%$search%")
              ->orWhere('nama_barang', 'like', "%$search%")
              ->orWhereHas('user', function ($u) use ($search) {
                  $u->where('name', 'like', "%$search%");
              });
        });
    }

    $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    $data = $query->get();

    $pdf = PDF::loadView('admin.supplier.pdf', compact('data', 'setting', 'logoSrc'))
        ->setPaper('a4', 'landscape');

    return $pdf->stream('data-supplier.pdf');
}
}