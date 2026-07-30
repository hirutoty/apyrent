<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\InvPenawaran;
use App\Models\InvPenawaranItem;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use App\Models\Setting;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class InvPenawaranController
{
    public function index(Request $request)
{
    // Update otomatis menjadi expired
    InvPenawaran::whereNotIn('status', ['approved', 'rejected', 'expired'])
        ->get()
        ->each(function ($penawaran) {

            $expiredDate = Carbon::parse($penawaran->tanggal_penawaran)
                ->addMonths($penawaran->periode)
                ->startOfDay();

            if (now()->startOfDay()->gt($expiredDate)) {
                $penawaran->update([
                    'status' => 'expired'
                ]);
            }
        });

    $query = InvPenawaran::with('items.kendaraan')->latest();

    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('no_penawaran', 'like', '%' . $request->search . '%')
                ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                ->orWhere('kepada', 'like', '%' . $request->search . '%');
        });
    }

    $penawarans = $query->paginate(10)->withQueryString();

    $kendaraans = Kendaraan::whereIn('status_kendaraan', ['tersedia', 'disewa'])
        ->orderBy('nopol')
        ->get();

    $kendaraanJson = $kendaraans->map(fn($k) => [
        'id'    => $k->id,
        'nama'  => $k->merk . ' - ' . $k->nopol,
        'tahun' => $k->tahun_pembuatan ?? '',
        'harga' => (int) ($k->harga_sewa_per_hari ?? 0),
    ])->values()->toArray();

    // Setting reminder
    $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

    $reminder = match ($setting->satuan_reminder) {
        'hari'   => $setting->batas_reminder,
        'minggu' => $setting->batas_reminder * 7,
        'bulan'  => $setting->batas_reminder * 30,
        'tahun'  => $setting->batas_reminder * 365,
        default  => $setting->batas_reminder,
    };

    // Hitung reminder tiap penawaran
    foreach ($penawarans as $p) {

        $p->tanggalBerakhir = Carbon::parse($p->tanggal_penawaran)
            ->startOfDay()
            ->addMonths($p->periode);

        $p->sisaHari = (int) now()->startOfDay()
            ->diffInDays($p->tanggalBerakhir, false);

        // Tidak dihitung jika sudah selesai
        if (in_array($p->status, ['approved', 'rejected', 'expired'])) {

            $p->isExpired = false;
            $p->isSoon = false;

        } else {

            $p->isExpired = $p->sisaHari < 0;
            $p->isSoon = !$p->isExpired && $p->sisaHari <= $reminder;

        }
    }

    return view('admin.penawaran.index', compact(
        'penawarans',
        'kendaraans',
        'kendaraanJson',
        'reminder'
    ));
}

    /**
     * AJAX endpoint: cari member berdasarkan nama (autosuggest)
     */
    public function customerSearch(Request $request)
    {
        $q = $request->input('q', '');

        $results = Pelanggan::when($q, fn($query) =>
                $query->where('nama_pelanggan', 'like', '%' . $q . '%')
            )
            ->orderBy('nama_pelanggan')
            ->limit(20)
            ->get(['id', 'nama_pelanggan', 'alamat', 'no_ktp', 'jenis_pelanggan', 'email_pelanggan', 'kontak_pelanggan']);

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $noPenawaran = $this->generateNoPenawaran();

        $request->validate([
            'tanggal_penawaran' => 'required',
            'customer_name'     => 'required',
            'kendaraan_id'      => 'required|array|min:1',
            'kendaraan_id.*'    => 'required|exists:kendaraan,id',
            'qty'               => 'required|array|min:1',
            'qty.*'             => 'required|numeric|min:1',
            'price'             => 'required|array|min:1',
            'price.*'           => 'required|numeric|min:0',
        ]);

        $total = 0;
        foreach ($request->price as $i => $price) {
            $total += ($request->qty[$i] * $price);
        }

        DB::transaction(function () use ($request, $total, $noPenawaran) {

            // Jika customer belum ada di tabel member, simpan otomatis
            Pelanggan::firstOrCreate(
                ['nama_pelanggan' => $request->customer_name],
                [
                    'kontak_pelanggan' => $request->contact_person,
                    'email_pelanggan'  => $request->email_person,
                    'alamat'           => $request->alamat,
                    'no_ktp'           => $request->no_ktp,
                    'jenis_pelanggan'  => $request->jenis_pelanggan ?: 'perorangan',
                ]
            );

            $penawaran = InvPenawaran::create([
                'no_penawaran'      => $noPenawaran,
                'tanggal_penawaran' => $request->tanggal_penawaran,
                'kepada'            => $request->kepada,
                'up'                => $request->up,
                'perihal'           => $request->perihal,
                'customer_name'     => $request->customer_name,
                'contact_person'    => $request->contact_person,
                'email_person'      => $request->email_person,
                'alamat'            => $request->alamat,
                'no_ktp'            => $request->no_ktp,
                'jenis_pelanggan'   => $request->jenis_pelanggan,
                'pengirim'          => $request->pengirim,
                'periode'           => $request->periode,
                'staff'             => $request->staff,
                'name_staff'        => $request->name_staff,
                'direktur'          => $request->direktur,
                'name_direktur'     => $request->name_direktur,
                'total'             => $total,
            ]);

            foreach ($request->kendaraan_id as $i => $kendaraan) {
                InvPenawaranItem::create([
                    'penawaran_id'  => $penawaran->id,
                    'kendaraan_id'  => $kendaraan,
                    'qty'           => $request->qty[$i],
                    'tahun_unit'    => $request->tahun_unit[$i] ?? null,
                    'price'         => $request->price[$i],
                    'durasi'        => $request->durasi[$i] ?? null,
                    'satuan_durasi' => $request->satuan_durasi[$i] ?? null,
                ]);
            }
        });

        // Set status pending dan generate draft PDF setelah transaksi commit
        $created = InvPenawaran::with('items.kendaraan')
            ->where('no_penawaran', $noPenawaran)->firstOrFail();
        $created->update(['status' => 'pending']);
        try {
            $pdfPath = $this->generatePenawaranPdf($created);
            $created->update(['file_penawaran' => $pdfPath]);
        } catch (\Exception $e) {
            \Log::warning('PDF generation failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Penawaran berhasil ditambahkan. Draft PDF sudah digenerate.');
    }

    public function edit($id)
    {
        $penawaran = InvPenawaran::with('items.kendaraan')->findOrFail($id);

        return response()->json([
            'id' => $penawaran->id,
            'no_penawaran' => $penawaran->no_penawaran,
            'tanggal_penawaran' => optional($penawaran->tanggal_penawaran)->format('Y-m-d'),
            'kepada' => $penawaran->kepada,
            'up' => $penawaran->up,
            'perihal' => $penawaran->perihal,
            'customer_name' => $penawaran->customer_name,
            'contact_person' => $penawaran->contact_person,
            'email_person' => $penawaran->email_person,
            'alamat' => $penawaran->alamat,
            'no_ktp' => $penawaran->no_ktp,
            'jenis_pelanggan' => $penawaran->jenis_pelanggan,
            'pengirim' => $penawaran->pengirim,
            'staff' => $penawaran->staff,
            'name_staff' => $penawaran->name_staff,
            'direktur' => $penawaran->direktur,
            'name_direktur' => $penawaran->name_direktur,
            'periode' => $penawaran->periode,
            'items' => $penawaran->items,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_penawaran' => 'required',
            'customer_name'     => 'required',
            'kendaraan_id'      => 'required|array|min:1',
            'kendaraan_id.*'    => 'required|exists:kendaraan,id',
            'qty'               => 'required|array|min:1',
            'qty.*'             => 'required|numeric|min:1',
            'price'             => 'required|array|min:1',
            'price.*'           => 'required|numeric|min:0',
        ]);

        $total = 0;
        foreach ($request->price as $i => $price) {
            $total += ($request->qty[$i] * $price);
        }

        DB::transaction(function () use ($request, $id, $total) {

            $penawaran = InvPenawaran::findOrFail($id);

            // Jika customer belum ada di tabel member, simpan otomatis
            Pelanggan::firstOrCreate(
                ['nama_pelanggan' => $request->customer_name],
                [
                    'kontak_pelanggan' => $request->contact_person,
                    'email_pelanggan'  => $request->email_person,
                    'alamat'           => $request->alamat,
                    'no_ktp'           => $request->no_ktp,
                    'jenis_pelanggan'  => $request->jenis_pelanggan ?: 'perorangan',
                ]
            );

            // Update data penawaran
            $penawaran->update([
                'no_penawaran'      => $request->no_penawaran,
                'tanggal_penawaran' => $request->tanggal_penawaran,
                'kepada'            => $request->kepada,
                'up'                => $request->up,
                'perihal'           => $request->perihal,
                'customer_name'     => $request->customer_name,
                'contact_person'    => $request->contact_person,
                'email_person'      => $request->email_person,
                'alamat'            => $request->alamat,
                'no_ktp'            => $request->no_ktp,
                'jenis_pelanggan'   => $request->jenis_pelanggan,
                'pengirim'          => $request->pengirim,
                'periode'           => $request->periode,
                'staff'             => $request->staff,
                'name_staff'        => $request->name_staff,
                'direktur'          => $request->direktur,
                'name_direktur'     => $request->name_direktur,
                'total'             => $total,
            ]);

            // Hapus item lama
            $penawaran->items()->delete();

            // Simpan item baru
            foreach ($request->kendaraan_id as $i => $kendaraan) {
                InvPenawaranItem::create([
                    'penawaran_id'  => $penawaran->id,
                    'kendaraan_id'  => $kendaraan,
                    'qty'           => $request->qty[$i],
                    'tahun_unit'    => $request->tahun_unit[$i] ?? null,
                    'price'         => $request->price[$i],
                    'durasi'        => $request->durasi[$i] ?? null,
                    'satuan_durasi' => $request->satuan_durasi[$i] ?? null,
                ]);
            }

            // Ubah status hanya jika belum approved atau rejected
            if (!in_array($penawaran->status, ['approved', 'rejected'])) {
                $periode = (int) $request->input('periode');

                $expired = Carbon::parse($request->tanggal_penawaran)
                    ->addMonths($periode);

                $penawaran->update([
                    'status' => now()->gt($expired) ? 'expired' : 'pending'
                ]);
            }
        });

        // Regenerate draft PDF setelah update
        $updated = InvPenawaran::with('items.kendaraan')->findOrFail($id);
        try {
            $pdfPath = $this->generatePenawaranPdf($updated);
            $updated->update(['file_penawaran' => $pdfPath]);
        } catch (\Exception $e) {
            \Log::warning('PDF regeneration failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Data berhasil diperbarui. Draft PDF sudah diperbarui.');
    }

    public function destroy($id)
    {
        $penawaran = InvPenawaran::findOrFail($id);
        $penawaran->delete();

        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function downloadDraft($id)
    {
        $penawaran = InvPenawaran::with('items.kendaraan')->findOrFail($id);

        // Regenerate on demand if file missing
        if (!$penawaran->file_penawaran || !file_exists(public_path($penawaran->file_penawaran))) {
            try {
                $path = $this->generatePenawaranPdf($penawaran);
                $penawaran->update(['file_penawaran' => $path]);
                $penawaran->refresh();
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
            }
        }

        return response()->file(public_path($penawaran->file_penawaran));
    }

    private function generatePenawaranPdf(InvPenawaran $penawaran): string
    {
        $penawaran->loadMissing('items.kendaraan');
        $setting  = Setting::first();
        $logoSrc  = '';
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $dir = public_path('uploads/penawaran');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = 'draft_' . $penawaran->no_penawaran . '.pdf';
        $path     = $dir . '/' . $filename;

        Pdf::loadView('admin.penawaran.penawaran_letter', compact('penawaran', 'setting', 'logoSrc'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi'                  => 96,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled'         => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'Times New Roman',
                'margin_top'           => 0,
                'margin_bottom'        => 0,
                'margin_left'          => 0,
                'margin_right'         => 0,
            ])
            ->save($path);

        return 'uploads/penawaran/' . $filename;
    }

    private function generateNoPenawaran(): string
    {
        $prefix = 'PNW-' . date('Ym') . '-';

        $last = InvPenawaran::where('no_penawaran', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $number = (int) substr($last->no_penawaran, -4);

        return $prefix . str_pad($number + 1, 4, '0', STR_PAD_LEFT);
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'file_penawaran' => 'required|file|mimes:pdf|max:10240',
        ], [
            'file_penawaran.required' => 'File penawaran yang sudah ditandatangani wajib diupload.',
            'file_penawaran.mimes'    => 'File harus berformat PDF.',
        ]);

        DB::beginTransaction();

        try {
            $penawaran = InvPenawaran::findOrFail($id);

            if ($penawaran->items->isEmpty()) {
                return back()->with('error', 'Item penawaran kosong');
            }

            // Simpan file yang sudah ditandatangani
            $file     = $request->file('file_penawaran');
            $filename = time() . '_signed_' . $penawaran->no_penawaran . '.' . $file->getClientOriginalExtension();
            $dir      = public_path('uploads/penawaran');
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $file->move($dir, $filename);

            $penawaran->update([
                'status'          => 'approved',
                'file_penawaran'  => 'uploads/penawaran/' . $filename,
            ]);

            DB::commit();

            return back()->with('success', 'Penawaran berhasil di-approve. File tersimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('APPROVE ERROR', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject($id)
    {
        DB::beginTransaction();

        try {

            $penawaran = InvPenawaran::findOrFail($id);

            // Jangan bisa reject jika sudah approved
            if ($penawaran->status == 'approved') {
                return back()->with('error', 'Penawaran yang sudah di-approve tidak dapat di-reject.');
            }

            $penawaran->update([
                'status' => 'rejected'
            ]);

            DB::commit();

            return back()->with('success', 'Penawaran berhasil ditolak.');
        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error('REJECT ERROR', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PenawaranExport($request->search),
            'Penawaran-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $query = InvPenawaran::with('items.kendaraan')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('no_penawaran', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('kepada', 'like', '%' . $request->search . '%');
            });
        }

        $penawarans = $query->get();
        $setting    = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.penawaran.pdf', [
            'penawarans' => $penawarans,
            'setting'    => $setting,
            'logoSrc'    => $logoSrc,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Daftar-Penawaran-' . now()->format('Y-m-d') . '.pdf');
    }
}

