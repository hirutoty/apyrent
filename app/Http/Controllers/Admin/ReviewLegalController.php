<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewLegal;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReviewLegalController extends Controller
{
    public function index()
    {
        $data         = ReviewLegal::latest()->get();
        $total        = $data->count();
        $totalSelesai = $data->where('status_review', 'Selesai')->count();
        $totalProses  = $data->where('status_review', 'Proses')->count();
        $totalPending = $data->where('status_review', 'Pending')->count();

        return view('admin.legal.review_legal.index', compact('data', 'total', 'totalSelesai', 'totalProses', 'totalPending'));
    }

    public function show($id)
    {
        return response()->json(ReviewLegal::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'       => 'required|date',
            'pemohon'       => 'required',
            'dokumen'       => 'required',
            'status_review' => 'required',
            'pic_legal'     => 'required',
        ]);

        ReviewLegal::create($request->all());
        return back()->with('success', 'Review Legal berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = ReviewLegal::findOrFail($id);

        $request->validate([
            'tanggal'       => 'required|date',
            'pemohon'       => 'required',
            'dokumen'       => 'required',
            'status_review' => 'required',
            'pic_legal'     => 'required',
        ]);

        $item->update($request->all());
        return back()->with('success', 'Review Legal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        ReviewLegal::findOrFail($id)->delete();
        return back()->with('success', 'Review Legal berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = ReviewLegal::query();
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('pemohon', 'like', "%$s%")
                  ->orWhere('dokumen', 'like', "%$s%")
                  ->orWhere('status_review', 'like', "%$s%");
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
        $pdf = Pdf::loadView('admin.legal.review_legal.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('review-legal.pdf');
    }
}
