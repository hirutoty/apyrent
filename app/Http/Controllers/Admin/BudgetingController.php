<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BudgetingExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\AnggaranProyek;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BudgetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = AnggaranProyek::latest()->get();

        return view('admin.budgeting.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.budgeting.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'proyek' => 'required',
            'kategori' => 'required',
            'budget' => 'required|numeric',
            'realisasi' => 'required|numeric',
        ]);

        $budget = $request->budget;
        $realisasi = $request->realisasi;

        $sisa = $budget - $realisasi;

        $persen = 0;

        if ($budget > 0) {
            $persen = ($realisasi / $budget) * 100;
        }

        AnggaranProyek::create([
            'proyek' => $request->proyek,
            'kategori' => $request->kategori,
            'budget' => $budget,
            'realisasi' => $realisasi,
            'sisa' => $sisa,
            'persen_terpakai' => $persen,
        ]);

        return redirect('/admin/budgeting')
            ->with('success', 'Data budgeting berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = AnggaranProyek::findOrFail($id);

        return view('admin.budgeting.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'proyek' => 'required',
            'kategori' => 'required',
            'budget' => 'required|numeric',
            'realisasi' => 'required|numeric',
        ]);

        $data = AnggaranProyek::findOrFail($id);

        $budget = $request->budget;
        $realisasi = $request->realisasi;

        $sisa = $budget - $realisasi;

        $persen = 0;

        if ($budget > 0) {
            $persen = ($realisasi / $budget) * 100;
        }

        $data->update([
            'proyek' => $request->proyek,
            'kategori' => $request->kategori,
            'budget' => $budget,
            'realisasi' => $realisasi,
            'sisa' => $sisa,
            'persen_terpakai' => $persen,
        ]);

        return redirect('/admin/budgeting')
            ->with('success', 'Data budgeting berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = AnggaranProyek::findOrFail($id);

        $data->delete();

        return redirect('/admin/budgeting')
            ->with('success', 'Data budgeting berhasil dihapus');
    }



    public function pdf(Request $request)
    {
        $query = AnggaranProyek::query();

        // 🔥 pakai search yang sama seperti tabel
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('proyek', 'like', "%{$request->search}%")
                    ->orWhere('kategori', 'like', "%{$request->search}%");
            });
        }

        $data = $query->get();
          $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = PDF::loadView('admin.budgeting.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('budgeting-proyek.pdf');
    }

    

    public function exportExcel(Request $request)
    {
        $filename = 'budgeting_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new BudgetingExport($request->search),
            $filename
        );
    }
}
