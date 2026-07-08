<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\InvPenawaran;
use App\Models\InvKontrak;
use App\Models\Kendaraan;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use App\Services\RemoveBgService;


class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with([
            'penawaran',
            'kontrak',
            'kendaraan',
            'penawarans',
            'kontraks',
            'kendaraans',
        ])->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_no', 'like', '%' . $request->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                    ->orWhere('order_no', 'like', '%' . $request->search . '%');
            });
        }

        $invoices = $query->paginate(10)->withQueryString();

        // dropdown untuk modal tambah/edit
        $penawarans = InvPenawaran::latest()->get();
        $kontraks   = InvKontrak::latest()->get();
        $kendaraans = Kendaraan::orderBy('merk')->get();

        return view('admin.invoice.index', compact(
            'invoices',
            'penawarans',
            'kontraks',
            'kendaraans'
        ));
    }

    /**
     * Generate nomor invoice otomatis: INV-YYYYMM-0001
     */
    private function generateInvoiceNo(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';

        $last = Invoice::where('invoice_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if ($last) {
            $lastNumber = (int) substr($last->invoice_no, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . sprintf('%04d', $nextNumber);
    }

    /**
     * Show the form for creating a new resource.
     * (tetap disediakan kalau dibutuhkan halaman terpisah)
     */
    public function create()
    {
        $penawarans = InvPenawaran::latest()->get();
        $kontraks   = InvKontrak::latest()->get();
        $kendaraans = Kendaraan::orderBy('merk')->get();

        return view('admin.invoices.create', compact(
            'penawarans',
            'kontraks',
            'kendaraans'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'penawaran_ids'  => 'nullable|array',
            'penawaran_ids.*'=> 'nullable|exists:inv_penawarans,id',
            'kontrak_ids'    => 'nullable|array',
            'kontrak_ids.*'  => 'nullable|exists:inv_kontraks,id',
            'kendaraan_ids'  => 'nullable|array',
            'kendaraan_ids.*'=> 'nullable|exists:kendaraan,id',

            'type'         => 'required|in:perorangan,perusahaan',
            'order_no'     => 'nullable|string|max:255',

            'invoice_date' => 'required|date',

            'customer_name'    => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'contact_person'   => 'nullable|string|max:255',
            'telephone'        => 'nullable|string|max:50',
            'email'            => 'nullable|string|max:50',

            'satuan'   => 'nullable|string|max:100',
            'pengirim' => 'nullable|string|max:255',

            'staff'         => 'nullable|string|max:255',
            'name_staff'    => 'nullable|string|max:255',
            'direktur'      => 'nullable|string|max:255',
            'name_direktur' => 'nullable|string|max:255',

            'ttd_staff'    => 'nullable|image|max:2048',
            'ttd_direktur' => 'nullable|image|max:2048',

            'status'         => 'required|in:draft,partial,overdue,lunas',
            'payment_status' => 'required|in:unpaid,paid',

            'ppn'   => 'nullable|numeric',
            'pph'   => 'nullable|numeric',
            'total' => 'nullable|numeric',
        ]);

        // Filter nilai kosong dari array relasi
        $penawaranIds = array_filter($request->input('penawaran_ids', []), fn($v) => !empty($v));
        $kontrakIds   = array_filter($request->input('kontrak_ids',   []), fn($v) => !empty($v));
        $kendaraanIds = array_filter($request->input('kendaraan_ids', []), fn($v) => !empty($v));

        // Bersihkan dari validated array (pivot ditangani terpisah)
        unset($validated['penawaran_ids'], $validated['kontrak_ids'], $validated['kendaraan_ids']);

        // Backward-compat: isi kolom FK lama dengan nilai pertama jika ada
        $validated['penawaran_id'] = !empty($penawaranIds) ? $penawaranIds[0] : null;
        $validated['kontrak_id']   = !empty($kontrakIds)   ? $kontrakIds[0]   : null;
        $validated['kendaraan_id'] = !empty($kendaraanIds) ? $kendaraanIds[0] : null;

        $validated['invoice_no'] = $this->generateInvoiceNo();

        $removeBg = new RemoveBgService();

        if ($request->hasFile('ttd_staff')) {
            $validated['ttd_staff'] = $removeBg->uploadAndRemoveBg($request->file('ttd_staff'));
        } elseif ($request->filled('ttd_staff_path')) {
            $validated['ttd_staff'] = $request->input('ttd_staff_path');
        }

        if ($request->hasFile('ttd_direktur')) {
            $validated['ttd_direktur'] = $removeBg->uploadAndRemoveBg($request->file('ttd_direktur'));
        } elseif ($request->filled('ttd_direktur_path')) {
            $validated['ttd_direktur'] = $request->input('ttd_direktur_path');
        }

        $invoice = Invoice::create($validated);

        // Sync pivot relations
        $invoice->penawarans()->sync($penawaranIds);
        $invoice->kontraks()->sync($kontrakIds);
        $invoice->kendaraans()->sync($kendaraanIds);

        // Jika request AJAX/JSON (dari modal), kembalikan JSON dengan invoice_id
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Invoice berhasil ditambahkan.',
                'invoice_id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
            ], 201);
        }

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice berhasil ditambahkan.');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with([
            'penawaran',
            'kontrak',
            'kendaraan',
            'periodes.remaks',
        ])->findOrFail($id);

        return view('admin.invoice.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     * Mendukung response JSON untuk modal edit (fetch AJAX).
     */
    public function edit(Request $request, string $id)
    {
        $invoice = Invoice::with(['penawarans', 'kontraks', 'kendaraans'])->findOrFail($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'invoice'       => $invoice,
                'penawaran_ids' => $invoice->penawarans->pluck('id'),
                'kontrak_ids'   => $invoice->kontraks->pluck('id'),
                'kendaraan_ids' => $invoice->kendaraans->pluck('id'),
            ]);
        }

        $penawarans = InvPenawaran::latest()->get();
        $kontraks   = InvKontrak::latest()->get();
        $kendaraans = Kendaraan::orderBy('merk')->get();

        return view('admin.invoices.edit', compact(
            'invoice',
            'penawarans',
            'kontraks',
            'kendaraans'
        ));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'invoice_no'     => 'nullable|string|max:255',
            'penawaran_ids'  => 'nullable|array',
            'penawaran_ids.*'=> 'nullable|exists:inv_penawarans,id',
            'kontrak_ids'    => 'nullable|array',
            'kontrak_ids.*'  => 'nullable|exists:inv_kontraks,id',
            'kendaraan_ids'  => 'nullable|array',
            'kendaraan_ids.*'=> 'nullable|exists:kendaraan,id',

            'type'         => 'required|in:perorangan,perusahaan',

            'invoice_date' => 'required|date',

            'customer_name'    => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'contact_person'   => 'nullable|string|max:255',
            'telephone'        => 'nullable|string|max:50',
            'email'            => 'nullable|string|max:50',

            'satuan'   => 'nullable|string|max:100',
            'pengirim' => 'nullable|string|max:255',

            'staff'         => 'nullable|string|max:255',
            'name_staff'    => 'nullable|string|max:255',
            'direktur'      => 'nullable|string|max:255',
            'name_direktur' => 'nullable|string|max:255',

            'ttd_staff'    => 'nullable|image|max:2048',
            'ttd_direktur' => 'nullable|image|max:2048',

            'status'         => 'required|in:draft,partial,overdue,lunas',
            'payment_status' => 'required|in:unpaid,paid',

            'ppn'   => 'nullable|numeric',
            'pph'   => 'nullable|numeric',
            'total' => 'nullable|numeric',
        ]);

        // Filter nilai kosong dari array relasi
        $penawaranIds = array_filter($request->input('penawaran_ids', []), fn($v) => !empty($v));
        $kontrakIds   = array_filter($request->input('kontrak_ids',   []), fn($v) => !empty($v));
        $kendaraanIds = array_filter($request->input('kendaraan_ids', []), fn($v) => !empty($v));

        unset($validated['penawaran_ids'], $validated['kontrak_ids'], $validated['kendaraan_ids']);

        // Backward-compat: kolom FK lama diisi nilai pertama
        $validated['penawaran_id'] = !empty($penawaranIds) ? $penawaranIds[0] : null;
        $validated['kontrak_id']   = !empty($kontrakIds)   ? $kontrakIds[0]   : null;
        $validated['kendaraan_id'] = !empty($kendaraanIds) ? $kendaraanIds[0] : null;

        // invoice_no tidak boleh diubah
        unset($validated['invoice_no']);

        $removeBg = new RemoveBgService();

        // Upload TTD Staff
        if ($request->hasFile('ttd_staff')) {
            if ($invoice->ttd_staff && \Storage::disk('public')->exists($invoice->ttd_staff)) {
                \Storage::disk('public')->delete($invoice->ttd_staff);
            }
            $validated['ttd_staff'] = $removeBg->uploadAndRemoveBg($request->file('ttd_staff'));
        } elseif ($request->filled('ttd_staff_path')) {
            $validated['ttd_staff'] = $request->input('ttd_staff_path');
        } else {
            unset($validated['ttd_staff']);
        }

        // Upload TTD Direktur
        if ($request->hasFile('ttd_direktur')) {
            if ($invoice->ttd_direktur && \Storage::disk('public')->exists($invoice->ttd_direktur)) {
                \Storage::disk('public')->delete($invoice->ttd_direktur);
            }
            $validated['ttd_direktur'] = $removeBg->uploadAndRemoveBg($request->file('ttd_direktur'));
        } elseif ($request->filled('ttd_direktur_path')) {
            $validated['ttd_direktur'] = $request->input('ttd_direktur_path');
        } else {
            unset($validated['ttd_direktur']);
        }

        $invoice->update($validated);

        // Sync pivot relations
        $invoice->penawarans()->sync($penawaranIds);
        $invoice->kontraks()->sync($kontrakIds);
        $invoice->kendaraans()->sync($kendaraanIds);

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }


    /**
     * List file TTD yang sudah ada di storage sebagai library.
     * Gabungkan file dari Storage::disk('public') dan dari public/uploads/ttd/
     * Dipakai oleh modal invoice via AJAX.
     */
    public function ttdLibrary()
    {
        $seen   = [];
        $result = [];

        // Sumber 1: Storage::disk('public') → storage/app/public/uploads/ttd/
        $storageFiles = \Storage::disk('public')->files('uploads/ttd');
        foreach ($storageFiles as $path) {
            $name = basename($path);
            if (isset($seen[$name])) continue;
            $seen[$name] = true;
            $result[] = [
                'path' => $path,
                'url'  => asset('storage/' . $path),
                'name' => $name,
            ];
        }

        // Sumber 2: public/uploads/ttd/ (upload langsung, tidak via Storage)
        $publicDir = public_path('uploads/ttd');
        if (is_dir($publicDir)) {
            foreach (glob($publicDir . '/*.{png,jpg,jpeg,webp,gif}', GLOB_BRACE) as $absPath) {
                $name = basename($absPath);
                if (isset($seen[$name])) continue;
                $seen[$name] = true;
                $result[] = [
                    'path' => 'uploads/ttd/' . $name,
                    'url'  => asset('uploads/ttd/' . $name),
                    'name' => $name,
                ];
            }
        }

        return response()->json(array_values($result));
    }

    /**
     * Konversi path gambar ke data URI base64 untuk DomPDF.
     * DomPDF tidak bisa akses file via path biasa, harus embed base64.
     */
    private function imgToBase64(string $path): string
    {
        if (!file_exists($path) || !is_readable($path)) {
            return '';
        }
        $mime = mime_content_type($path) ?: 'image/png';
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }

    /**
     * Resolve path gambar TTD/logo — coba beberapa kemungkinan lokasi.
     * File bisa tersimpan di:
     *   1. storage/app/public/... (via Storage::disk('public'))
     *   2. public/... (upload langsung ke public folder)
     */
    private function resolveFilePath(string $relativePath): string
    {
        if (empty($relativePath)) {
            return '';
        }

        // Kemungkinan 1: via Storage::disk('public') → storage/app/public/
        $storagePath = storage_path('app/public/' . ltrim($relativePath, '/'));
        if (file_exists($storagePath) && is_readable($storagePath)) {
            return $storagePath;
        }

        // Kemungkinan 2: langsung di public/
        $publicPath = public_path(ltrim($relativePath, '/'));
        if (file_exists($publicPath) && is_readable($publicPath)) {
            return $publicPath;
        }

        return '';
    }

    /**
     * Siapkan variabel base64 untuk logo dan TTD agar bisa dipakai di Blade DomPDF.
     */
    private function resolveImageBase64(Invoice $invoice, ?Setting $setting): array
    {
        // Logo: pakai logo dari setting, fallback ke images/icon.png
        $logoPath = '';
        if ($setting?->logo) {
            $logoPath = $this->resolveFilePath($setting->logo);
        }
        if (!$logoPath) {
            $logoPath = public_path('images/icon.png');
        }

        // TTD Staff
        $ttdStaffPath = $invoice->ttd_staff
            ? $this->resolveFilePath($invoice->ttd_staff)
            : '';

        // TTD Direktur
        $ttdDirekturPath = $invoice->ttd_direktur
            ? $this->resolveFilePath($invoice->ttd_direktur)
            : '';

        return [
            'logoSrc'        => $this->imgToBase64($logoPath),
            'ttdStaffSrc'    => $ttdStaffPath    ? $this->imgToBase64($ttdStaffPath)    : '',
            'ttdDirekturSrc' => $ttdDirekturPath ? $this->imgToBase64($ttdDirekturPath) : '',
        ];
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\InvoiceExport($request->search),
            'Invoice-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function print($id)
    {
        $invoice = Invoice::with(['periodes.remaks', 'kendaraan', 'kendaraans', 'penawaran', 'kontrak', 'penawarans', 'kontraks'])->findOrFail($id);
        $setting = Setting::first();

        // Hitung grand total dari remaks
        $grandTotal = 0;
        foreach ($invoice->periodes as $periode) {
            foreach ($periode->remaks as $item) {
                $grandTotal += $item->subtotal ?? ($item->qty * ($item->price ?? 0));
            }
        }

        // Tambahkan ppn - pph (nominal)
        $grandTotal = $grandTotal + floatval($invoice->ppn ?? 0) - floatval($invoice->pph ?? 0);

        $terbilang = ucwords(trim($this->penyebut((int) $grandTotal))) . ' Rupiah';

        $images = $this->resolveImageBase64($invoice, $setting);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.invoice.print', [
            'invoice'        => $invoice,
            'setting'        => $setting,
            'grand_total'    => $grandTotal,
            'terbilang'      => $terbilang,
            'ttdStaffSrc'    => $images['ttdStaffSrc'],
            'ttdDirekturSrc' => $images['ttdDirekturSrc'],
            'logoSrc'        => $images['logoSrc'],
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'dpi'                  => 96,
            'isHtml5ParserEnabled' => true,
            'defaultFont'          => 'Times New Roman',
            'isPhpEnabled'         => true,
            'margin_top'           => 0,
            'margin_bottom'        => 0,
            'margin_left'          => 0,
            'margin_right'         => 0,
            'isRemoteEnabled'      => true,
        ]);

        return $pdf->stream('Invoice-' . $invoice->invoice_no . '.pdf');
    }

    /**
     * Helper terbilang.
     */
    private function penyebut(int $n): string
    {
        $n = abs($n);
        $h = ['','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas'];
        if ($n < 12)                return ' ' . $h[$n];
        elseif ($n < 20)            return $this->penyebut($n - 10) . ' Belas';
        elseif ($n < 100)           return $this->penyebut(intdiv($n, 10)) . ' Puluh' . $this->penyebut($n % 10);
        elseif ($n < 200)           return ' Seratus' . $this->penyebut($n - 100);
        elseif ($n < 1000)          return $this->penyebut(intdiv($n, 100)) . ' Ratus' . $this->penyebut($n % 100);
        elseif ($n < 2000)          return ' Seribu' . $this->penyebut($n - 1000);
        elseif ($n < 1000000)       return $this->penyebut(intdiv($n, 1000)) . ' Ribu' . $this->penyebut($n % 1000);
        elseif ($n < 1000000000)    return $this->penyebut(intdiv($n, 1000000)) . ' Juta' . $this->penyebut($n % 1000000);
        elseif ($n < 1000000000000) return $this->penyebut(intdiv($n, 1000000000)) . ' Miliar' . $this->penyebut($n % 1000000000);
        return '';
    }

    public function sendEmail($id)
    {
        $invoice = Invoice::with(['periodes.remaks', 'kendaraan', 'kendaraans', 'penawaran', 'kontrak'])->findOrFail($id);
        $setting = Setting::first();

        $toEmails = array_unique(array_filter([
            $invoice->email,
            $setting?->email,
        ]));

        if (empty($toEmails)) {
            return back()->with('error', 'Email tujuan kosong.');
        }

        $images = $this->resolveImageBase64($invoice, $setting);

        try {
            foreach ($toEmails as $email) {
                Mail::to($email)->send(new InvoiceMail($invoice, $setting, $images));
            }

            $invoice->update(['last_email_sent_at' => now()]);

            return back()->with('success', 'Email + PDF berhasil dikirim ke ' . $invoice->customer_name . '.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
