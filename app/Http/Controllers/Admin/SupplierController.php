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
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Supplier::with(['user', 'purchaseros.items']);
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_supplier', 'like', '%' . $search . '%')
                  ->orWhere('no_telp', 'like', '%' . $search . '%');
            });
        }
        
        $data = $query->latest()->paginate(15)->withQueryString();

        // Summary dihitung dari seluruh tabel (tidak terpengaruh search)
        $totalSupplier  = Supplier::count();

        return view('admin.supplier.index', compact('data', 'totalSupplier', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required',
            'no_telp' => 'required',
            'alamat' => 'nullable|string',
            'nama_marketing' => 'nullable|string',
            'kontak_marketing' => 'nullable|string',
        ]);

        // CEK DUPLIKAT
        $exists = Supplier::where('nama_supplier', $request->nama_supplier)->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Supplier dengan nama yang sama sudah ada!');
        }

        Supplier::create([
            'user_id' => Auth::id(),
            'nama_supplier' => $request->nama_supplier,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'nama_marketing' => $request->nama_marketing,
            'kontak_marketing' => $request->kontak_marketing,
        ]);

        return back()->with('success', 'Supplier berhasil ditambahkan');
    }

    /**
     * Store supplier via AJAX (untuk modal create on-the-fly)
     */
    public function storeApi(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|unique:supplier,nama_supplier',
            'no_telp' => 'required',
            'alamat' => 'nullable|string',
            'nama_marketing' => 'nullable|string',
            'kontak_marketing' => 'nullable|string',
        ]);

        $supplier = Supplier::create([
            'user_id' => Auth::id(),
            'nama_supplier' => $request->nama_supplier,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'nama_marketing' => $request->nama_marketing,
            'kontak_marketing' => $request->kontak_marketing,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Supplier berhasil ditambahkan',
            'data' => $supplier
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_supplier' => 'required',
            'no_telp' => 'required',
            'alamat' => 'nullable|string',
            'nama_marketing' => 'nullable|string',
            'kontak_marketing' => 'nullable|string',
        ]);

        $supplier = Supplier::findOrFail($id);

        // CEK DUPLIKAT (kecuali data sendiri)
        $exists = Supplier::where('nama_supplier', $request->nama_supplier)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Supplier dengan nama yang sama sudah digunakan!');
        }

        $supplier->update([
            'nama_supplier' => $request->nama_supplier,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'nama_marketing' => $request->nama_marketing,
            'kontak_marketing' => $request->kontak_marketing,
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