<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekonsiliasiExport;
use App\Exports\VirtualAccountExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RekonsiliasiBank;
use App\Models\VirtualAccount;
use App\Models\Member;
use App\Models\Invoice;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class IntegrasiController extends Controller
{
    public function index(Request $request)
    {
        $rekonsiliasi = RekonsiliasiBank::latest()->get();

        $vaSearch = $request->va_search;
        $vaQuery  = VirtualAccount::with('member', 'invoice');
        if ($vaSearch) {
            $vaQuery->where('va_number', 'like', "%$vaSearch%")
                ->orWhere('bank', 'like', "%$vaSearch%")
                ->orWhere('status', 'like', "%$vaSearch%");
        }
        $virtualAccounts = $vaQuery->latest()->get();

        $members  = Member::all();
        $invoices = Invoice::select('id', 'invoice_no', 'customer_name')->latest()->get();

        return view('admin.integrasi-bank.index', compact(
            'rekonsiliasi', 'virtualAccounts', 'members', 'invoices', 'vaSearch'
        ));
    }

    // ── REKONSILIASI ──────────────────────────────────────

    public function rekonsiliasiStore(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required',
            'deskripsi'        => 'required',
            'reference_no'     => 'required',
            'amount'           => 'required|numeric',
            'bukti_pembayaran' => 'required|file|max:5120',
        ]);

        $bukti = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $file     = $request->file('bukti_pembayaran');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path     = public_path('rekonsiliasi/bukti_pembayaran');
            if (!file_exists($path)) mkdir($path, 0777, true);
            $file->move($path, $filename);
            $bukti = 'rekonsiliasi/bukti_pembayaran/' . $filename;
        }

        RekonsiliasiBank::create([
            'tanggal'            => $request->tanggal,
            'deskripsi'          => $request->deskripsi,
            'reference_no'       => $request->reference_no,
            'amount'             => $request->amount,
            'currency'           => $request->currency,
            'status_rekonsiliasi'=> $request->status_rekonsiliasi,
            'invoice_id'         => $request->invoice_id,
            'va'                 => $request->va,
            'bukti_pembayaran'   => $bukti,
        ]);

        return back()->with('success', 'Data rekonsiliasi berhasil ditambahkan');
    }

    public function rekonsiliasiUpdate(Request $request, $id)
    {
        $data = RekonsiliasiBank::findOrFail($id);

        $updateData = [
            'tanggal'            => $request->tanggal,
            'deskripsi'          => $request->deskripsi,
            'reference_no'       => $request->reference_no,
            'amount'             => $request->amount,
            'currency'           => $request->currency,
            'status_rekonsiliasi'=> $request->status_rekonsiliasi,
            'invoice_id'         => $request->invoice_id,
            'va'                 => $request->va,
        ];

        if ($request->hasFile('bukti_pembayaran')) {
            $file     = $request->file('bukti_pembayaran');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path     = public_path('rekonsiliasi/bukti_pembayaran');
            if (!file_exists($path)) mkdir($path, 0777, true);
            $file->move($path, $filename);
            $updateData['bukti_pembayaran'] = 'rekonsiliasi/bukti_pembayaran/' . $filename;
        }

        $data->update($updateData);

        return back()->with('success', 'Data rekonsiliasi berhasil diupdate');
    }

    public function rekonsiliasiDestroy($id)
    {
        RekonsiliasiBank::findOrFail($id)->delete();
        return back()->with('success', 'Data rekonsiliasi berhasil dihapus');
    }

    public function rekonsiliasiPdf(Request $request)
    {
        $query = RekonsiliasiBank::query();
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
        $data    = $query->orderBy('tanggal', 'desc')->get();
        $pdf     = Pdf::loadView('admin.rekonsiliasi.pdf', compact('data', 'setting', 'logoSrc'));
        return $pdf->stream('rekonsiliasi-bank.pdf');
    }

    public function rekonsiliasiExcel(Request $request)
    {
        return Excel::download(
            new RekonsiliasiExport($request->search),
            'rekonsiliasi_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    // ── VIRTUAL ACCOUNT ───────────────────────────────────

    public function virtualStore(Request $request)
    {
        $request->validate([
            'member_id'      => 'required',
            'bank'           => 'required',
            'expected_amount'=> 'required|numeric',
        ]);

        $lastId   = VirtualAccount::max('id') + 1;
        $vaNumber = 'VA-' . date('Y') . '-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);

        $bukti = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $file        = $request->file('bukti_pembayaran');
            $filename    = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('virtual-account/bukti');
            if (!file_exists($destination)) mkdir($destination, 0777, true);
            $file->move($destination, $filename);
            $bukti = 'virtual-account/bukti/' . $filename;
        }

        VirtualAccount::create([
            'va_number'      => $vaNumber,
            'member_id'      => $request->member_id,
            'invoice_id'     => $request->invoice_id,
            'bukti_pembayaran'=> $bukti,
            'bank'           => $request->bank,
            'expected_amount'=> $request->expected_amount,
            'paid_amount'    => $request->paid_amount ?? 0,
            'status'         => $request->status,
            'expired_at'     => $request->expired_at ?: null,
        ]);

        return back()->with('success', 'Data virtual account berhasil ditambahkan');
    }

    public function virtualUpdate(Request $request, $id)
    {
        $data  = VirtualAccount::findOrFail($id);
        $bukti = $data->bukti_pembayaran;

        if ($request->hasFile('bukti_pembayaran')) {
            if ($data->bukti_pembayaran && file_exists(public_path($data->bukti_pembayaran))) {
                unlink(public_path($data->bukti_pembayaran));
            }
            $file        = $request->file('bukti_pembayaran');
            $filename    = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('virtual-account/bukti');
            if (!file_exists($destination)) mkdir($destination, 0777, true);
            $file->move($destination, $filename);
            $bukti = 'virtual-account/bukti/' . $filename;
        }

        $data->update([
            'member_id'       => $request->member_id,
            'invoice_id'      => $request->invoice_id,
            'bank'            => $request->bank,
            'expected_amount' => $request->expected_amount,
            'paid_amount'     => $request->paid_amount,
            'status'          => $request->status,
            'bukti_pembayaran'=> $bukti,
            'expired_at'      => $request->expired_at ?: null,
        ]);

        return back()->with('success', 'Data virtual account berhasil diupdate');
    }

    public function virtualDestroy($id)
    {
        $data = VirtualAccount::findOrFail($id);
        if ($data->bukti_pembayaran && file_exists(public_path($data->bukti_pembayaran))) {
            unlink(public_path($data->bukti_pembayaran));
        }
        $data->delete();
        return back()->with('success', 'Data virtual account berhasil dihapus');
    }

    public function virtualPdf(Request $request)
    {
        $search = $request->query('search');
        $query  = VirtualAccount::with('member', 'invoice');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('va_number', 'like', "%$search%")
                  ->orWhere('bank', 'like', "%$search%")
                  ->orWhere('status', 'like', "%$search%")
                  ->orWhereHas('member', fn($m) => $m->where('nama_member', 'like', "%$search%"));
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
        $data    = $query->get();
        $pdf     = Pdf::loadView('admin.virtual.pdf', compact('data', 'search', 'setting', 'logoSrc'));
        return $pdf->stream('virtual-account.pdf');
    }

    public function virtualExcel(Request $request)
    {
        return Excel::download(
            new VirtualAccountExport($request->search),
            'virtual_account_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
