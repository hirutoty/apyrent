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
            'kendaraan'
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
            'penawaran_id' => 'nullable|exists:inv_penawarans,id',
            'kontrak_id'   => 'nullable|exists:inv_kontraks,id',
            'kendaraan_id' => 'nullable|exists:kendaraan,id',

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

        $validated['invoice_no'] = $this->generateInvoiceNo();

        $removeBg = new RemoveBgService();

        if ($request->hasFile('ttd_staff')) {
            // Upload baru → remove bg → simpan
            $validated['ttd_staff'] = $removeBg->uploadAndRemoveBg($request->file('ttd_staff'));
        } elseif ($request->filled('ttd_staff_path')) {
            // Pilih dari library
            $validated['ttd_staff'] = $request->input('ttd_staff_path');
        }

        if ($request->hasFile('ttd_direktur')) {
            $validated['ttd_direktur'] = $removeBg->uploadAndRemoveBg($request->file('ttd_direktur'));
        } elseif ($request->filled('ttd_direktur_path')) {
            $validated['ttd_direktur'] = $request->input('ttd_direktur_path');
        }

        Invoice::create($validated);

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
        $invoice = Invoice::findOrFail($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($invoice);
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
            'invoice_no'   => 'nullable|string|max:255',
            'penawaran_id' => 'nullable|exists:inv_penawarans,id',
            'kontrak_id'   => 'nullable|exists:inv_kontraks,id',
            'kendaraan_id' => 'nullable|exists:kendaraan,id',

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

        // invoice_no tidak boleh diubah
        unset($validated['invoice_no']);

        $removeBg = new RemoveBgService();

        // Upload TTD Staff — hapus file lama jika ada
        if ($request->hasFile('ttd_staff')) {
            if ($invoice->ttd_staff && \Storage::disk('public')->exists($invoice->ttd_staff)) {
                \Storage::disk('public')->delete($invoice->ttd_staff);
            }
            $validated['ttd_staff'] = $removeBg->uploadAndRemoveBg($request->file('ttd_staff'));
        } elseif ($request->filled('ttd_staff_path')) {
            // Pilih dari library — tidak perlu hapus file lama karena file library tidak dihapus
            $validated['ttd_staff'] = $request->input('ttd_staff_path');
        } else {
            unset($validated['ttd_staff']);
        }

        // Upload TTD Direktur — hapus file lama jika ada
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
     * Dipakai oleh modal invoice via AJAX.
     */
    public function ttdLibrary()
    {
        $files = \Storage::disk('public')->files('uploads/ttd');

        $result = collect($files)->map(function ($path) {
            return [
                'path' => $path,
                'url'  => asset('storage/' . $path),
                'name' => basename($path),
            ];
        })->values();

        return response()->json($result);
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
     * Siapkan variabel base64 untuk logo dan TTD agar bisa dipakai di Blade DomPDF.
     */
    private function resolveImageBase64(Invoice $invoice, ?Setting $setting): array
    {
        // Logo: pakai logo dari setting, fallback ke images/icon.png
        $logoPath = '';
        if ($setting?->logo) {
            // Logo setting disimpan relatif ke public/ (misal: uploads/setting/xxx.png)
            $logoPath = public_path($setting->logo);
        }
        if (!$logoPath || !file_exists($logoPath)) {
            $logoPath = public_path('images/icon.png');
        }

        // TTD disimpan via Storage::disk('public') → storage/app/public/uploads/ttd/xxx
        // Harus pakai storage_path bukan public_path
        $ttdStaffPath    = $invoice->ttd_staff
            ? storage_path('app/public/' . $invoice->ttd_staff)
            : '';
        $ttdDirekturPath = $invoice->ttd_direktur
            ? storage_path('app/public/' . $invoice->ttd_direktur)
            : '';

        return [
            'logoSrc'        => $this->imgToBase64($logoPath),
            'ttdStaffSrc'    => $ttdStaffPath    ? $this->imgToBase64($ttdStaffPath)    : '',
            'ttdDirekturSrc' => $ttdDirekturPath ? $this->imgToBase64($ttdDirekturPath) : '',
        ];
    }

    public function print($id)
    {
        $invoice = Invoice::with(['periodes.remaks', 'kendaraan', 'penawaran', 'kontrak'])->findOrFail($id);
        $setting = Setting::first();

        $images = $this->resolveImageBase64($invoice, $setting);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.invoice.print', array_merge([
            'invoice' => $invoice,
            'setting' => $setting,
        ], $images))->setPaper('a4', 'portrait');

        $filename = 'Invoice-' . $invoice->invoice_no . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function sendEmail($id)
    {
        $invoice = Invoice::with(['periodes.remaks', 'kendaraan', 'penawaran', 'kontrak'])->findOrFail($id);
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
