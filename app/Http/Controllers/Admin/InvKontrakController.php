<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvKontrak;
use App\Models\InvPenawaran;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class InvKontrakController extends Controller
{
    public function index()
    {

        $kontraks = InvKontrak::with('penawaran')
            ->latest()
            ->paginate(15)->withQueryString();
        $penawarans = InvPenawaran::latest()->get();
        $setting = Setting::first();

        $reminder = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        foreach ($kontraks->getCollection() as $k) {

            $perjanjian = Carbon::parse($k->perjanjian_pembayaran)->startOfDay();

            $k->sisaHari = (int) now()->startOfDay()
                ->diffInDays($perjanjian, false);

            $k->isExpired = $k->sisaHari < 0;

            $k->isSoon = !$k->isExpired && $k->sisaHari <= $reminder;
            $k->showReminder = !in_array($k->status, [

                'completed',
                'approved',
                'rejected',
                'active',
                'expired'
            ]);
        }

     


        return view('admin.kontrak.index', compact('kontraks', 'penawarans', 'kontraks', 'reminder'));
    }

    public function create()
    {
        $penawarans = InvPenawaran::latest()->get();

        return view('admin.kontrak.create', compact('penawarans'));
    }

    public function store(Request $request)
    {
        $now = Carbon::now();

        $prefix = 'KTR-' . now()->format('Ym');

        $last = InvKontrak::where('no_kontrak', 'like', $prefix . '-%')
            ->orderByRaw('CAST(RIGHT(no_kontrak,4) AS UNSIGNED) DESC')
            ->first();

        if ($last) {
            $nextNumber = (int) substr($last->no_kontrak, -4) + 1;
        } else {
            $nextNumber = 1;
        }

        $no_kontrak = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $request->validate([
            'penawaran_id'            => 'required|exists:inv_penawarans,id',
            'no_kontrak' =>         'unique:inv_kontraks,no_kontrak',
            'tanggal_kontrak'         => 'required|date',
            'perjanjian_pembayaran'   => 'nullable|date',

            'pihak_pertama'           => 'required|string|max:255',
            'contact_pertama'         => 'nullable|string|max:255',

            'pihak_kedua'             => 'required|string|max:255',
            'contact_kedua'           => 'nullable|string|max:255',

            'file_kontrak'            => 'nullable|file',
            'file_persyaratan'        => 'nullable|file',

            'status'                  => 'required',
        ]);

        $data = $request->except([
            'file_kontrak',
            'file_persyaratan'
        ]);
        $data['no_kontrak'] = $no_kontrak;

        if ($request->hasFile('file_kontrak')) {

            $file = $request->file('file_kontrak');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('uploads/kontrak'), $filename);

            $data['file_kontrak'] = 'uploads/kontrak/' . $filename;
        }

        if ($request->hasFile('file_persyaratan')) {

            $file = $request->file('file_persyaratan');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('uploads/kontrak'), $filename);

            $data['file_persyaratan'] = 'uploads/kontrak/' . $filename;
        }

        InvKontrak::create($data);

        return redirect()
            ->route('kontrak.index')
            ->with('success', 'Kontrak berhasil ditambahkan.');
    }

    public function show($id)
    {
        $kontrak = InvKontrak::with('penawaran')
            ->findOrFail($id);

        return view('admin.kontrak.show', compact('kontrak'));
    }

    public function edit($id)
    {
        $kontrak = InvKontrak::findOrFail($id);

        $penawarans = InvPenawaran::latest()->get();

        return view('admin.kontrak.edit', compact(
            'kontrak',
            'penawarans'
        ));
    }

    public function update(Request $request, $id)
    {
        $kontrak = InvKontrak::findOrFail($id);

        $request->validate([
            'penawaran_id'            => 'required|exists:inv_penawarans,id',
            'no_kontrak'              => 'required|unique:inv_kontraks,no_kontrak,' . $id,
            'tanggal_kontrak'         => 'required|date',
            'perjanjian_pembayaran'   => 'nullable|date',

            'pihak_pertama'           => 'required|string|max:255',
            'contact_pertama'         => 'nullable|string|max:255',

            'pihak_kedua'             => 'required|string|max:255',
            'contact_kedua'           => 'nullable|string|max:255',

            'file_kontrak'            => 'nullable|file',
            'file_persyaratan'        => 'nullable|file',

            'status'                  => 'required',
        ]);

        $data = $request->except([
            'file_kontrak',
            'file_persyaratan'
        ]);

        if ($request->hasFile('file_kontrak')) {

            $file = $request->file('file_kontrak');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('uploads/kontrak'), $filename);

            $data['file_kontrak'] = 'uploads/kontrak/' . $filename;
        }

        if ($request->hasFile('file_persyaratan')) {

            $file = $request->file('file_persyaratan');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('uploads/kontrak'), $filename);

            $data['file_persyaratan'] = 'uploads/kontrak/' . $filename;
        }

        $kontrak->update($data);

        return redirect()
            ->route('kontrak.index')
            ->with('success', 'Kontrak berhasil diupdate.');
    }

    public function destroy($id)
    {
        $kontrak = InvKontrak::findOrFail($id);

        $kontrak->delete();

        return back()->with('success', 'Kontrak berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\KontrakExport($request->search, $request->status),
            'Kontrak-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function pdf(Request $request)
    {
        $query = InvKontrak::with('penawaran')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('no_kontrak', 'like', '%' . $request->search . '%')
                    ->orWhere('pihak_pertama', 'like', '%' . $request->search . '%')
                    ->orWhere('pihak_kedua', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $kontraks = $query->get();
        $setting  = Setting::first();

        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.kontrak.pdf', compact(
            'kontraks',
            'setting',
            'logoSrc'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Kontrak-' . now()->format('Y-m-d') . '.pdf');
    }
}
