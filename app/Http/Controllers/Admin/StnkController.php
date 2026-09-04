<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stnk;
use App\Models\StnkHistory;
use App\Models\Kendaraan;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class StnkController extends Controller
{
    public function index()
    {
        $data = Stnk::with('kendaraan')->latest()->paginate(15)->withQueryString();
        $kendaraan = Kendaraan::all();

        return view('admin.stnk.index', compact('data', 'kendaraan'));
    }

    public function store(Request $request, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'kendaraan_id'   => 'required|exists:kendaraan,id',
            'nama_pemilik'   => 'required',
            'jenis_model'    => 'required',
            'masa_berlaku'   => 'required|date',
            'biaya'          => 'required|numeric',
            'bukti'          => 'nullable|file|max:5120',  // Changed to nullable - upload saat approval
        ]);

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        // CEK DUPLIKAT NOPOL
        $exists = Stnk::whereHas('kendaraan', function ($q) use ($kendaraan) {
            $q->where('nopol', $kendaraan->nopol);
        })->exists();

        if ($exists) {
            return back()->with('error', 'Nopol ini sudah memiliki data STNK');
        }

        // ===========================================================================
        // APPROVAL WORKFLOW: Intercept dan kirim ke Purchasero
        // ===========================================================================
        
        try {
            // Check if this is a resubmit (from rejected purchasero)
            if ($request->filled('edit_purchasero')) {
                $purchaseroId = $request->input('edit_purchasero');
                
                // Resubmit: Update existing purchasero
                $purchasero = $interceptor->resubmitToPurchasero($purchaseroId, $request, 'stnk');
                
                return redirect()
                    ->route('purchasero.index', ['filter' => 'pengeluaran', 'source' => 'stnk'])
                    ->with('success', 'Pengajuan STNK berhasil diajukan ulang. Menunggu approval dari Superadmin.');
            }
            
            // Step 1: Intercept data dari form
            $interceptedData = $interceptor->intercept($request, 'stnk');
            
            // Step 2: Save ke Purchasero
            $purchasero = $interceptor->saveToPurchasero($interceptedData, 'stnk');
            
            // Step 3: Upload temporary files
            $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $purchasero->id);
            
            // Step 4: Update source_data dengan file info
            $sourceData = $purchasero->source_data;
            $sourceData['temp_files'] = $uploadedFiles;
            $purchasero->update(['source_data' => $sourceData]);
            
            return redirect()
                ->route('purchasero.index', ['filter' => 'pengeluaran', 'source' => 'stnk'])
                ->with('success', 'Pengajuan pengeluaran STNK berhasil dikirim. Menunggu approval dari Superadmin.');
                
        } catch (\Exception $e) {
            \Log::error('Error intercepting STNK submission: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan pengeluaran. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id'   => 'required|exists:kendaraan,id',
            'nama_pemilik'   => 'required',
            'jenis_model'    => 'required',
            'masa_berlaku'   => 'required|date',
            'biaya'          => 'required|numeric',
            'bukti'          => 'nullable|file|max:5120',
        ]);

        $stnk = Stnk::findOrFail($id);
        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        $bukti = $stnk->bukti;

        if ($request->hasFile('bukti')) {

            // hapus lama
            if ($bukti && file_exists(public_path($bukti))) {
                unlink(public_path($bukti));
            }

            $file = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $path = public_path('stnk/bukti');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file->move($path, $filename);
            $bukti = 'stnk/bukti/' . $filename;
        }

        $stnk->update([
            'kendaraan_id' => $request->kendaraan_id,
            'nopol'        => $kendaraan->nopol,
            'merk'         => $kendaraan->merk,
            'nama_pemilik' => $request->nama_pemilik,
            'jenis_model'  => $request->jenis_model,
            'masa_berlaku' => $request->masa_berlaku,
            'biaya'        => $request->biaya,
            'bukti'        => $bukti,
        ]);

        return back()->with('success', 'Data STNK berhasil diupdate');
    }

    /**
     * Perpanjang STNK.
     * Data lama dipindahkan ke history (stnk_histories),
     * lalu data aktif diperbarui dengan masa berlaku & biaya baru.
     */
    public function perpanjang(Request $request, $id, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'masa_berlaku' => 'required|date',
            'biaya'        => 'required|numeric',
            'bukti'        => 'nullable|file|max:5120',  // Changed to nullable - upload saat approval
            'nama_bank'      => 'nullable|string|max:255',
            'no_rekening'    => 'nullable|string|max:100',
            'nama_rekening'  => 'nullable|string|max:255',
            'informasi'      => 'nullable|string',
        ]);

        $stnk = Stnk::findOrFail($id);

        // ===========================================================================
        // APPROVAL WORKFLOW: Perpanjang melalui Purchasero untuk approval
        // ===========================================================================
        
        try {
            $purchasero = $interceptor->perpanjangViaPurchasero($request, 'stnk', $stnk);
            
            return redirect()
                ->route('purchasero.index', ['filter' => 'pengeluaran', 'source' => 'stnk'])
                ->with('success', 'Pengajuan perpanjangan STNK berhasil dikirim. Menunggu approval dari Superadmin.');
                
        } catch (\Exception $e) {
            \Log::error('Error perpanjang STNK via Purchasero: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan perpanjangan. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        $stnk = Stnk::findOrFail($id);

        if ($stnk->bukti && file_exists(public_path($stnk->bukti))) {
            unlink(public_path($stnk->bukti));
        }

        $stnk->delete();

        return back()->with('success', 'Data STNK berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        $search = $request->search;

        $query = Stnk::with('kendaraan');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nopol', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%")
                    ->orWhere('nama_pemilik', 'like', "%{$search}%")
                    ->orWhere('jenis_model', 'like', "%{$search}%");
            });
        }

        $data = $query->latest()->get();

        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.stnk.pdf', compact('data', 'search', 'setting', 'logoSrc'));

        return $pdf->stream('data-stnk.pdf');
    }
}
