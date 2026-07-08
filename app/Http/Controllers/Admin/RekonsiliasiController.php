<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekonsiliasiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RekonsiliasiBank;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class RekonsiliasiController extends Controller
{
    public function index()
    {
        $data = RekonsiliasiBank::latest()->paginate(15)->withQueryString();

        return view('admin.rekonsiliasi.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'deskripsi' => 'required',
            'reference_no' => 'required',
            'amount' => 'required|numeric',
            'bukti_pembayaran' => 'required|file|max:5120'
        ]);

        $bukti_pembayaran = null;

         if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $path = public_path('rekonsiliasi/bukti_pembayaran');
            if (!file_exists($path)) mkdir($path, 0777, true);

            $file->move($path, $filename);
            $bukti_pembayaran = 'rekonsiliasi/bukti_pembayaran/' . $filename;
        }
    

        RekonsiliasiBank::create([
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
            'reference_no' => $request->reference_no,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status_rekonsiliasi' => $request->status_rekonsiliasi,
            'invoice_id' => $request->invoice_id,
            'va' => $request->va,
            'bukti_pembayaran' => $bukti_pembayaran
            
        ]);
        
        return back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $data = RekonsiliasiBank::findOrFail($id);

        $updateData = [
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
            'reference_no' => $request->reference_no,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status_rekonsiliasi' => $request->status_rekonsiliasi,
            'invoice_id' => $request->invoice_id,
            'va' => $request->va,
        ];

        // Hanya update bukti_pembayaran jika ada file baru
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = public_path('rekonsiliasi/bukti_pembayaran');
            if (!file_exists($path)) mkdir($path, 0777, true);
            $file->move($path, $filename);
            $updateData['bukti_pembayaran'] = 'rekonsiliasi/bukti_pembayaran/' . $filename;
        }

        $data->update($updateData);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        RekonsiliasiBank::findOrFail($id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

   
public function pdf(Request $request)
{
    $query = RekonsiliasiBank::query();

    // 🔎 FILTER SEARCH (SAMA SEPERTI TABLE)
    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('deskripsi', 'like', "%{$request->search}%")
              ->orWhere('reference_no', 'like', "%{$request->search}%")
              ->orWhere('currency', 'like', "%{$request->search}%")
              ->orWhere('status_rekonsiliasi', 'like', "%{$request->search}%");
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

    $data = $query->orderBy('tanggal', 'desc')->get();

    $pdf = PDF::loadView('admin.rekonsiliasi.pdf', compact('data', 'setting', 'logoSrc'));

    return $pdf->stream('rekonsiliasi-bank.pdf');
}



public function exportExcel(Request $request)
{
    $filename = 'rekonsiliasi_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(
        new RekonsiliasiExport($request->search),
        $filename
    );
}
}