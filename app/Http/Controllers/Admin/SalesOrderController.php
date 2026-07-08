<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesOrder::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('order_no', 'like', "%$q%")
                   ->orWhere('pelanggan', 'like', "%$q%")
                   ->orWhere('produk_jasa', 'like', "%$q%")
                   ->orWhere('sales', 'like', "%$q%");
            });
        }

        $data         = $query->paginate(15)->withQueryString();
        $total        = SalesOrder::count();
        $totalProses  = SalesOrder::where('status_order', 'Diproses')->count();
        $totalSelesai = SalesOrder::where('status_order', 'Selesai')->count();
        $totalBatal   = SalesOrder::where('status_order', 'Dibatalkan')->count();

        return view('admin.sales.sales_order.index', compact(
            'data', 'total', 'totalProses', 'totalSelesai', 'totalBatal'
        ));
    }

    public function show($id)
    {
        return response()->json(SalesOrder::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_no'         => 'required|unique:sales_orders,order_no',
            'tanggal'          => 'required|date',
            'pelanggan'        => 'required',
            'produk_jasa'      => 'required',
            'qty'              => 'required|integer|min:1',
            'total_harga'      => 'required|numeric|min:0',
            'status_order'     => 'required',
            'metode_pembayaran'=> 'required',
            'sales'            => 'required',
        ]);

        SalesOrder::create($request->all());

        return back()->with('success', 'Sales Order berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = SalesOrder::findOrFail($id);

        $request->validate([
            'order_no'         => 'required|unique:sales_orders,order_no,' . $id,
            'tanggal'          => 'required|date',
            'pelanggan'        => 'required',
            'produk_jasa'      => 'required',
            'qty'              => 'required|integer|min:1',
            'total_harga'      => 'required|numeric|min:0',
            'status_order'     => 'required',
            'metode_pembayaran'=> 'required',
            'sales'            => 'required',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Sales Order berhasil diperbarui.');
    }

    public function destroy($id)
    {
        SalesOrder::findOrFail($id)->delete();
        return back()->with('success', 'Sales Order berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = SalesOrder::query();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('order_no', 'like', "%$s%")
                  ->orWhere('pelanggan', 'like', "%$s%")
                  ->orWhere('sales', 'like', "%$s%")
                  ->orWhere('status_order', 'like', "%$s%");
            });
        }

        $data    = $query->latest()->get();
        $setting = Setting::first();

        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.sales.sales_order.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-sales-order.pdf');
    }
}
