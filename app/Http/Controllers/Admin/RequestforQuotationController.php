<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequestforQuotation;
use Illuminate\Http\Request;

class RequestforQuotationController extends Controller
{
    public function index()
    {
        $data = RequestforQuotation::latest()->paginate(15)->withQueryString();

        $statusStats = RequestforQuotation::selectRaw('status_rfq, count(*) as total')->groupBy('status_rfq')->pluck('total', 'status_rfq');

        $totalRFQ      = RequestforQuotation::count();
        $totalOpen     = RequestforQuotation::where('status_rfq', 'Open')->count();
        $totalSent     = RequestforQuotation::where('status_rfq', 'Sent')->count();
        $totalClosed   = RequestforQuotation::where('status_rfq', 'Closed')->count();

        return view('admin.requestq.index', compact(
            'data', 'statusStats',
            'totalRFQ', 'totalOpen', 'totalSent', 'totalClosed'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        RequestforQuotation::create($validated);

        return redirect()->route('requestfor-quotation.index')
            ->with('success', 'Request for Quotation berhasil ditambahkan.');
    }

    public function update(Request $request, RequestforQuotation $requestforQuotation)
    {
        $validated = $this->validateData($request);

        $requestforQuotation->update($validated);

        return redirect()->route('requestfor-quotation.index')
            ->with('success', 'Request for Quotation berhasil diperbarui.');
    }

    public function destroy(RequestforQuotation $requestforQuotation)
    {
        $requestforQuotation->delete();

        return redirect()->route('requestfor-quotation.index')
            ->with('success', 'Request for Quotation berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tanggal_rfq'    => 'required|date',
            'vendor'         => 'required|string|max:255',
            'kode_barang'    => 'required|string|max:255',
            'nama_barang'    => 'required|string|max:255',
            'kuantitas'      => 'required|integer|min:1',
            'satuan'         => 'required|string|max:255',
            'harga_estimasi' => 'required|integer|min:0',
            'tanggal_kirim'  => 'required|date',
            'status_rfq'     => 'required|in:Open,Sent,Closed',
            'catatan'        => 'nullable|string',
        ]);
    }
}
