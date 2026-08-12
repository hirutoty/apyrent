<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DataLeasingExport;
use App\Exports\DataLeasingFullExport;
use App\Http\Controllers\Controller;
use App\Imports\DataLeasingImport;
use App\Models\DataKontrak;
use App\Models\DataLeasing;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DataLeasingController extends Controller
{
    /* ─────────────────────────────────────────────
       INDEX
    ───────────────────────────────────────────── */
    public function index(Request $request)
    {
        // ── Data Leasing ──────────────────────────────────────────
        $leasingQuery = DataLeasing::with('dataKontrak')->latest();

        if ($request->filled('leasing_search')) {
            $s = $request->leasing_search;
            $leasingQuery->where(function ($q) use ($s) {
                $q->where('no_kontrak',  'like', "%{$s}%")
                  ->orWhere('mobil',      'like', "%{$s}%")
                  ->orWhere('nopol',      'like', "%{$s}%")
                  ->orWhere('user_leasing','like', "%{$s}%");
            });
        }

        // Filter status cicilan berdasarkan tanggal (DB-level)
        if ($request->filled('leasing_status')) {
            $now = now()->startOfMonth()->toDateString();
            if ($request->leasing_status === 'Lunas') {
                // periode_selesai sudah lewat dan sudah mulai
                $leasingQuery->whereNotNull('periode_mulai')
                             ->whereNotNull('periode_selesai')
                             ->where('periode_mulai', '<=', $now)
                             ->where('periode_selesai', '<=', $now);
            } elseif ($request->leasing_status === 'Partial') {
                // sudah mulai, belum selesai
                $leasingQuery->whereNotNull('periode_mulai')
                             ->whereNotNull('periode_selesai')
                             ->where('periode_mulai', '<=', $now)
                             ->where('periode_selesai', '>', $now);
            }
        }

        $leasings = $leasingQuery->paginate(15, ['*'], 'leasing_page')->withQueryString();
        // ── Data Kontrak ──────────────────────────────────────────
        $dataKontraks = DataKontrak::with('kendaraan')->latest()->get();
        $kendaraans   = Kendaraan::orderBy('merk')->get();

        $kontrakQuery = DataKontrak::with(['kendaraan', 'attachments'])->latest();

        if ($request->filled('kontrak_search')) {
            $s = $request->kontrak_search;
            $kontrakQuery->where(function ($q) use ($s) {
                $q->where('no_kontrak',   'like', "%{$s}%")
                  ->orWhere('mobil',       'like', "%{$s}%")
                  ->orWhere('nopol',       'like', "%{$s}%")
                  ->orWhere('user_kontrak','like', "%{$s}%");
            });
        }

        $kontraks = $kontrakQuery->paginate(15, ['*'], 'kontrak_page')->withQueryString();

        return view('admin.data_leasing.index', compact(
            'leasings',
            'dataKontraks',
            'kendaraans',
            'kontraks'
        ));
    }

    /* ─────────────────────────────────────────────
       GET DATA KONTRAK DETAIL (AJAX – auto-fill form)
    ───────────────────────────────────────────── */
    public function getDataKontrakDetail($id)
    {
        $kontrak = DataKontrak::with('kendaraan')->findOrFail($id);

        return response()->json([
            'no_kontrak'         => $kontrak->no_kontrak,
            'user_leasing'       => $kontrak->user_kontrak,
            'mobil'              => $kontrak->mobil,
            'nopol'              => $kontrak->nopol,
            'tahun'              => $kontrak->tahun,
            'angsuran_per_bulan' => $kontrak->angsuran_per_bulan,
            'jatuh_tempo'        => $kontrak->jatuh_tempo,
            'periode_mulai'      => $kontrak->periode_mulai
                ? $kontrak->periode_mulai->format('Y-m-d') : null,
            'periode_selesai'    => $kontrak->periode_selesai
                ? $kontrak->periode_selesai->format('Y-m-d') : null,
            'personal_account'   => $kontrak->personal_account,
            'sumber_dana_debit'  => $kontrak->sumber_dana_debit,
            'cara_bayar'         => $kontrak->cara_bayar,
            'nama_asuransi'      => $kontrak->nama_asuransi,
        ]);
    }

    /* ─────────────────────────────────────────────
       STORE
    ───────────────────────────────────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'data_kontrak_id'    => 'nullable|exists:data_kontraks,id',
            'no_kontrak'         => 'nullable|string|max:255',
            'mobil'              => 'nullable|string|max:500',
            'tahun'              => 'nullable|string|max:255',
            'nopol'              => 'nullable|string|max:500',
            'user_leasing'       => 'nullable|string|max:255',
            'angsuran_per_bulan' => 'nullable|numeric|min:0',
            'jatuh_tempo'        => 'nullable|integer|min:1|max:31',
            'periode_mulai'      => 'nullable|date',
            'periode_selesai'    => 'nullable|date|after_or_equal:periode_mulai',
            'personal_account'   => 'nullable|string|max:255',
            'sumber_dana_debit'  => 'nullable|string|max:255',
            'cara_bayar'         => 'nullable|string|max:255',
            'asuransi_leasing'   => 'nullable|string|max:255',
        ]);

        // Cek duplikat: no_kontrak sudah ada di data_leasings
        if ($request->filled('no_kontrak')) {
            $exists = DataLeasing::where('no_kontrak', $request->no_kontrak)->exists();
            if ($exists) {
                return redirect()->route('data-leasing.index', ['tab' => 'leasing'])
                    ->with('error', 'Data sudah ada — Nomor Kontrak "' . $request->no_kontrak . '" sudah terdaftar di Data Leasing.');
            }
        }

        DataLeasing::create([
            'data_kontrak_id'    => $request->data_kontrak_id ?: null,
            'no_kontrak'         => $request->no_kontrak,
            'mobil'              => $request->mobil,
            'tahun'              => $request->tahun,
            'nopol'              => $request->nopol,
            'user_leasing'       => $request->user_leasing,
            'angsuran_per_bulan' => $request->angsuran_per_bulan ?? 0,
            'jatuh_tempo'        => $request->jatuh_tempo ?: null,
            'periode_mulai'      => $request->periode_mulai ?: null,
            'periode_selesai'    => $request->periode_selesai ?: null,
            'personal_account'   => $request->personal_account,
            'sumber_dana_debit'  => $request->sumber_dana_debit,
            'cara_bayar'         => $request->cara_bayar,
            'asuransi_leasing'   => $request->asuransi_leasing,
        ]);

        return redirect()->route('data-leasing.index')
            ->with('success', 'Data Leasing berhasil ditambahkan.');
    }

    /* ─────────────────────────────────────────────
       UPDATE
    ───────────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        $leasing = DataLeasing::findOrFail($id);

        $request->validate([
            'data_kontrak_id'    => 'nullable|exists:data_kontraks,id',
            'no_kontrak'         => 'nullable|string|max:255',
            'mobil'              => 'nullable|string|max:500',
            'tahun'              => 'nullable|string|max:255',
            'nopol'              => 'nullable|string|max:500',
            'user_leasing'       => 'nullable|string|max:255',
            'angsuran_per_bulan' => 'nullable|numeric|min:0',
            'jatuh_tempo'        => 'nullable|integer|min:1|max:31',
            'periode_mulai'      => 'nullable|date',
            'periode_selesai'    => 'nullable|date|after_or_equal:periode_mulai',
            'personal_account'   => 'nullable|string|max:255',
            'sumber_dana_debit'  => 'nullable|string|max:255',
            'cara_bayar'         => 'nullable|string|max:255',
            'asuransi_leasing'   => 'nullable|string|max:255',
        ]);

        $leasing->update([
            'data_kontrak_id'    => $request->data_kontrak_id ?: null,
            'no_kontrak'         => $request->no_kontrak,
            'mobil'              => $request->mobil,
            'tahun'              => $request->tahun,
            'nopol'              => $request->nopol,
            'user_leasing'       => $request->user_leasing,
            'angsuran_per_bulan' => $request->angsuran_per_bulan ?? 0,
            'jatuh_tempo'        => $request->jatuh_tempo ?: null,
            'periode_mulai'      => $request->periode_mulai ?: null,
            'periode_selesai'    => $request->periode_selesai ?: null,
            'personal_account'   => $request->personal_account,
            'sumber_dana_debit'  => $request->sumber_dana_debit,
            'cara_bayar'         => $request->cara_bayar,
            'asuransi_leasing'   => $request->asuransi_leasing,
        ]);

        return redirect()->route('data-leasing.index')
            ->with('success', 'Data Leasing berhasil diupdate.');
    }

    /* ─────────────────────────────────────────────
       EXPORT — Download Data Leasing Lengkap
    ───────────────────────────────────────────── */
    public function exportLeasing()
    {
        $filename = 'data-leasing-' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new DataLeasingFullExport(), $filename);
    }

    /* ─────────────────────────────────────────────
       EXPORT — Download Template Excel
    ───────────────────────────────────────────── */
    public function exportTemplate()
    {
        return Excel::download(
            new DataLeasingExport(),
            'template-data-leasing.xlsx'
        );
    }

    /* ─────────────────────────────────────────────
       EXPORT DATA — Download Data Aktual Excel
    ───────────────────────────────────────────── */
    public function exportData()
    {
        return Excel::download(
            new \App\Exports\DataLeasingDataExport(),
            'data-leasing-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /* ─────────────────────────────────────────────
       IMPORT — Upload Excel
    ───────────────────────────────────────────── */
    public function import(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new DataLeasingImport();

        Excel::import($import, $request->file('file_import'));

        $imported = $import->importedCount;
        $skipped  = $import->skippedRows;

        // Pisahkan warning (bukan fatal) dari error murni
        $errors   = array_filter($skipped, fn($r) => empty($r['warn']));
        $warnings = array_filter($skipped, fn($r) => !empty($r['warn']));

        // Bangun pesan hasil
        $msg = "Import selesai: {$imported} baris berhasil diimport.";

        if (!empty($errors)) {
            $msg .= ' ' . count($errors) . ' baris diskip karena error.';
        }
        if (!empty($warnings)) {
            $msg .= ' ' . count($warnings) . ' baris diimport dengan peringatan (no_kontrak tidak ditemukan).';
        }

        // Simpan detail skipped ke session untuk ditampilkan di view
        if (!empty($skipped)) {
            session()->flash('import_skipped', $skipped);
        }

        return redirect()->route('data-leasing.index')
            ->with('success', $msg);
    }

    /* ─────────────────────────────────────────────
       DESTROY
    ───────────────────────────────────────────── */
    public function destroy($id)
    {
        DataLeasing::findOrFail($id)->delete();

        return redirect()->route('data-leasing.index')
            ->with('success', 'Data Leasing berhasil dihapus.');
    }
}
