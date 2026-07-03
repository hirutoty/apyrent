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
            'email'        => 'nullable|string|max:50',

            'satuan' => 'nullable|string|max:100',

            'pengirim'      => 'nullable|string|max:255',
            'staff'         => 'nullable|string|max:255',
            'name_staff'    => 'nullable|string|max:255',
            'direktur'      => 'nullable|string|max:255',
            'name_direktur' => 'nullable|string|max:255',

            'status'         => 'required|in:draft,partial,overdue,lunas',
            'payment_status' => 'required|in:unpaid,paid',

            'ppn'   => 'nullable|numeric',
            'pph'   => 'nullable|numeric',
            'total' => 'nullable|numeric',
        ]);

        $validated['invoice_no'] = $this->generateInvoiceNo();

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
            'kendaraan'
        ])->findOrFail($id);

        return view('admin.invoices.show', compact('invoice'));
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
            'invoice_no'     => 'nullable|string|max:255',
            'penawaran_id' => 'nullable|exists:inv_penawarans,id',
            'kontrak_id'   => 'nullable|exists:inv_kontraks,id',
            'kendaraan_id' => 'nullable|exists:kendaraan,id',

            'type'         => 'required|in:perorangan,perusahaan',

            'invoice_date' => 'required|date',

            'customer_name'    => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'contact_person'   => 'nullable|string|max:255',
            'telephone'        => 'nullable|string|max:50',
            'email'        => 'nullable|string|max:50',

            'satuan' => 'nullable|string|max:100',

            'pengirim'      => 'nullable|string|max:255',
            'staff'         => 'nullable|string|max:255',
            'name_staff'    => 'nullable|string|max:255',
            'direktur'      => 'nullable|string|max:255',
            'name_direktur' => 'nullable|string|max:255',

            'status'         => 'required|in:draft,partial,overdue,lunas',
            'payment_status' => 'required|in:unpaid,paid',

            'ppn'   => 'nullable|numeric',
            'pph'   => 'nullable|numeric',
            'total' => 'nullable|numeric',
        ]);

        // invoice_no tidak boleh diubah
        unset($validated['invoice_no']);

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


    public function sendEmail($id)
{
    $invoice = Invoice::findOrFail($id);
    $setting = Setting::first();

    $toEmails = array_unique(array_filter([
        $invoice->email,
        $setting?->email
    ]));

    if (empty($toEmails)) {
        return back()->with('error', 'Email tujuan kosong.');
    }

    try {
        foreach ($toEmails as $email) {
            Mail::to($email)->send(new InvoiceMail($invoice, $setting));
        }

        $invoice->update([
            'last_email_sent_at' => now(),
        ]);

        return back()->with('success', 'Email + PDF berhasil dikirim ke ' . $invoice->customer_name . '.');
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
}
