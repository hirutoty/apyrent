<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Aging_aps;
use App\Models\Setting;
use Carbon\Carbon;

class Aging_ApsController extends Controller
{
    // =====================
    // INDEX
    // =====================
    public function index()
    {
        $data = Aging_aps::latest()->paginate(15)->withQueryString();

        $setting = Setting::first();
        $reminder = $setting->satuan_reminder ?? 30;

        return view('admin.aging_ap.index', compact('data', 'reminder'));
    }

    // =====================
    // STORE
    // =====================
    public function store(Request $request)
    {
        $request->validate([
            'vendor' => 'required',
            'jatuh_tempo' => 'required|date',
            'jumlah' => 'required|numeric',
        ]);

        // auto no tagihan
        $last = Aging_aps::orderBy('id', 'desc')->first();

        $next = $last
            ? (int) str_replace('TAG-', '', $last->no_tagihan) + 1
            : 1;

        $noTagihan = 'TAG-' . str_pad($next, 3, '0', STR_PAD_LEFT);

        // kategori otomatis
        $kategori = $this->getKategori($request->jatuh_tempo);

        Aging_aps::create([
            'vendor' => $request->vendor,
            'no_tagihan' => $noTagihan,
            'jatuh_tempo' => $request->jatuh_tempo,
            'jumlah' => $request->jumlah,
            'kategori' => $kategori,
        ]);

        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    // =====================
    // UPDATE
    // =====================
    public function update(Request $request, $id)
    {
        $request->validate([
            'vendor' => 'required',
            'jatuh_tempo' => 'required|date',
            'jumlah' => 'required|numeric',
        ]);

        $data = Aging_aps::findOrFail($id);

        $kategori = $this->getKategori($request->jatuh_tempo);

        $data->update([
            'vendor' => $request->vendor,
            'jatuh_tempo' => $request->jatuh_tempo,
            'jumlah' => $request->jumlah,
            'kategori' => $kategori,
        ]);

        return redirect()->back()->with('success', 'Data berhasil diupdate');
    }

    // =====================
    // DESTROY
    // =====================
    public function destroy($id)
    {
        Aging_aps::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    // =====================
    // HELPER (KATEGORI + UMUR)
    // =====================
    private function getKategori($jatuhTempo)
    {
        $jatuhTempo = Carbon::parse($jatuhTempo);
        $hariIni = now()->startOfDay();

        $umur = $hariIni->diffInDays($jatuhTempo, false);

        if ($umur > 0) {
            return 'Current'; // belum jatuh tempo
        }

        $overdue = abs($umur); // ubah jadi positif

        if ($overdue <= 30) {
            return 'Overdue 1';
        } elseif ($overdue <= 60) {
            return 'Overdue 2';
        } elseif ($overdue <= 90) {
            return 'Overdue 3';
        }

        return 'Overdue 4';
    }
}
