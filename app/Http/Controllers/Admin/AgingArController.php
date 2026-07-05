<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgingAr;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Setting;
use Carbon\Carbon;

class AgingArController extends Controller
{
    // =====================
    // INDEX
    // =====================
    public function index()
    {
        $data = AgingAr::with(['member', 'invoice'])->latest()->get();
        $setting = Setting::first();

        $batasReminder = $setting?->batas_reminder ?? 7;

        $reminder = match ($setting?->satuan_reminder) {
            'hari'    => $batasReminder,
            'minggu'  => $batasReminder * 7,
            'bulan'   => $batasReminder * 30,
            'tahun'   => $batasReminder * 365,
            default   => $batasReminder,
        };

        return view('admin.aging_ar.index', compact('data', 'setting', 'reminder'));
    }

    // =====================
    // STORE
    // =====================
    public function store(Request $request)
    {
        $request->validate([
            'member_id'   => 'required|exists:member,id',
            'invoice_id'  => 'required|exists:invoices,id',
            'jatuh_tempo' => 'required|date',
            'total'       => 'required|numeric',
        ]);

        AgingAr::create([
            'member_id'   => $request->member_id,
            'invoice_id'  => $request->invoice_id,
            'jatuh_tempo' => $request->jatuh_tempo,
            'total'       => $request->total,
            'kategori'    => $this->getKategori($request->jatuh_tempo),
        ]);

        return back()->with('success', 'Data berhasil ditambahkan');
    }

    // =====================
    // UPDATE
    // =====================
    public function update(Request $request, $id)
    {
        $request->validate([
            'member_id'   => 'required|exists:member,id',
            'invoice_id'  => 'required|exists:invoices,id',
            'jatuh_tempo' => 'required|date',
            'total'       => 'required|numeric',
        ]);

        $data = AgingAr::findOrFail($id);

        $data->update([
            'member_id'   => $request->member_id,
            'invoice_id'  => $request->invoice_id,
            'jatuh_tempo' => $request->jatuh_tempo,
            'total'       => $request->total,
            'kategori'    => $this->getKategori($request->jatuh_tempo),
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    // =====================
    // DELETE
    // =====================
    public function destroy($id)
    {
        AgingAr::findOrFail($id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    // =====================
    // HELPER KATEGORI
    // =====================
    private function getKategori($jatuhTempo)
    {
        $jatuhTempo = Carbon::parse($jatuhTempo);

        $umur = now()->diffInDays($jatuhTempo, false);
        $overdue = abs($umur);

        if ($umur > 0) {
            return 'Current';
        }

        if ($overdue <= 30) {
            return 'Overdue 1';
        } elseif ($overdue <= 60) {
            return 'Overdue 2';
        } elseif ($overdue <= 90) {
            return 'Overdue 3';
        }

        return 'Overdue 4';
    }

   
    // =====================
    // AUTOSUGGEST MEMBER
    // =====================
    public function searchMember(Request $request)
    {
        $data = Member::where('nama_member', 'like', "%{$request->q}%")
            ->limit(10)
            ->get(['id', 'nama_member', 'kontak_member', 'email_member'])
            ->map(function ($member) {
                return [
                    'id'    => $member->id,
                    'name'  => $member->nama_member,
                    'kontak' => $member->kontak_member,
                    'email'  => $member->email_member,
                ];
            });

        return response()->json($data);
    }
    // =====================
    // AUTOSUGGEST INVOICE
    // =====================
    public function searchInvoice(Request $request)
    {
        $data = Invoice::where('invoice_no', 'like', "%{$request->q}%")
            ->limit(10)
            ->get(['id', 'invoice_no'])
            ->map(function ($invoice) {
                return [
                    'id'         => $invoice->id,
                    'no_invoice' => $invoice->invoice_no,
                ];
            });

        return response()->json($data);
    }

    public function reminder()
{
    $data = AgingAr::with(['member', 'invoice'])
        ->where('status', 'Belum Bayar')
        ->latest()
        ->get();

    return view('admin.aging_ar.reminder', compact('data'));
}

   public function lunas()
{
    $data = AgingAr::where('status', 'Bayar')
                ->latest()
                ->get(); 

    return view('admin.aging_ar.lunas', compact('data'));
}


    public function bayar(Request $request, $id)
    {
        $request->validate([
            'bukti' => 'required|file|max:5021',
        ]);

        $data = AgingAr::findOrFail($id); // sesuaikan nama model

        // Nama folder tujuan di public
        $folder = public_path('bukti');

        // Buat folder kalau belum ada
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $file = $request->file('bukti');
        $filename = time() . '_' . $file->getClientOriginalName();

        // pindahkan file langsung ke public/bukti (BUKAN storage)
        $file->move($folder, $filename);

        $data->update([
            'bukti'  => $filename,
            'status' => 'Bayar',
        ]);

        // redirect ke halaman lunas
        return redirect()->route('keuangan.index')
            ->with('success', 'Bukti pembayaran berhasil diupload.')
            ->with('active_tab', 'lunas'); 
    }
}
