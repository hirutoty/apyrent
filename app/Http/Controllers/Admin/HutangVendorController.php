<?php

namespace App\Http\Controllers\Admin;

use App\Exports\HutangVendorExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HutangVendor;
use App\Models\Keuangan;
use App\Models\Setting;

class HutangVendorController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        $query = HutangVendor::query();

        if ($request->search) {
            $query->where('nama_vendor', 'like', '%' . $request->search . '%')
                ->orWhere('kategori', 'like', '%' . $request->search . '%')
                ->orWhere('keterangan', 'like', '%' . $request->search . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $reminder = match ($setting->satuan_reminder) {
            'hari'    => $setting->batas_reminder,
            'minggu'  => $setting->batas_reminder * 7,
            'bulan'   => $setting->batas_reminder * 30,
            'tahun'   => $setting->batas_reminder * 365,
            default   => $setting->batas_reminder,
        };


        return view('admin.hutang_vendor.index', [
            'data' => $query->latest()->get(),
            'reminder' => $reminder,
        ]);
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required',
            'kategori' => 'required',
            'nominal' => 'required|numeric',
            'dibayar' => 'nullable|numeric',
            'jatuh_tempo' => 'required|date',
            'status' => 'required',
        ]);

        $dibayar = $request->dibayar ?? 0;

        $sisa = $request->nominal - $dibayar;

        $data = HutangVendor::create([
            'nama_vendor' => $request->nama_vendor,
            'kategori' => $request->kategori,
            'nominal' => $request->nominal,
            'dibayar' => $dibayar,
            'sisa' => $sisa,
            'jatuh_tempo' => $request->jatuh_tempo,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);
        /*
|--------------------------------------------------------------------------
| MASUK KE KEUANGAN JIKA LUNAS
|--------------------------------------------------------------------------
*/

        if ($request->status == 'lunas') {

            $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;

            $saldoBaru = $lastSaldo - $request->nominal;

            Keuangan::create([

                'tanggal' => now()->toDateString(),

                'reference' => 'HTG-' . time(),

                'user_id' => auth()->id(),

                'kategori' => 'Pengeluaran',

                'metode' => 'Cash',

                'keterangan' => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,

                'pemasukan' => 0,

                'pengeluaran' => $request->nominal,

                'saldo' => $saldoBaru,


            ]);
        }

        return back()->with(
            'success',
            'Data hutang vendor berhasil ditambahkan'
        );
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $data = HutangVendor::findOrFail($id);

        /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS CEPAT DARI TABLE
    |--------------------------------------------------------------------------
    */

        if ($request->has('status') && count($request->all()) <= 3) {

            $statusLama = $data->status;

            $data->update([
                'status' => $request->status
            ]);

            // AUTO MASUK KE KEUANGAN
            // AUTO MASUK KE KEUANGAN
            if ($request->status == 'lunas' && $statusLama != 'lunas') {

                $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;

                $saldoBaru = $lastSaldo - $data->nominal;

                Keuangan::create([

                    'tanggal' => now()->toDateString(),

                    'reference' => 'HTG-' . time(),

                    'user_id' => auth()->id(),

                    'kategori' => 'Pengeluaran',

                    'metode' => 'Cash',

                    'keterangan' => 'Pelunasan Hutang Vendor - ' . $data->nama_vendor,

                    'pemasukan' => 0,

                    'pengeluaran' => $data->nominal,

                    'saldo' => $saldoBaru,
                ]);
            }

            return back()->with('success', 'Status berhasil diubah');
        }

        /*
    |--------------------------------------------------------------------------
    | UPDATE FULL FORM
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'nama_vendor' => 'required',
            'kategori' => 'required',
            'nominal' => 'required|numeric',
            'dibayar' => 'nullable|numeric',
            'jatuh_tempo' => 'required|date',
            'status' => 'required',
        ]);

        $dibayar = $request->dibayar ?? 0;

        $sisa = $request->nominal - $dibayar;

        $data->update([

            'nama_vendor' => $request->nama_vendor,

            'kategori' => $request->kategori,

            'nominal' => $request->nominal,

            'dibayar' => $dibayar,

            'sisa' => $sisa,

            'jatuh_tempo' => $request->jatuh_tempo,

            'status' => $request->status,

            'keterangan' => $request->keterangan,

        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    /**
     * DELETE
     */
    public function destroy($id)
    {
        HutangVendor::findOrFail($id)->delete();

        return back()->with(
            'success',
            'Data berhasil dihapus'
        );
    }





    public function pdf(Request $request)
    {
        $query = HutangVendor::query();

        // 🔥 pakai filter yang sama seperti di table
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_vendor', 'like', "%{$request->search}%")
                    ->orWhere('kategori', 'like', "%{$request->search}%")
                    ->orWhere('keterangan', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
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

        $pdf = PDF::loadView('admin.hutang_vendor.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('hutang-vendor.pdf');
    }



    public function exportExcel(Request $request)
    {
        $filename = 'hutang_vendor_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new HutangVendorExport(
                $request->search,
                $request->status
            ),
            $filename
        );
    }
}
