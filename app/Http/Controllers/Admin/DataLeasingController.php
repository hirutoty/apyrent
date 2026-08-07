<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataLeasing;
use App\Models\InvKontrak;
use App\Models\Asuransi;
use Illuminate\Http\Request;

class DataLeasingController extends Controller
{
    /* ─────────────────────────────────────────────
       INDEX
    ───────────────────────────────────────────── */
    public function index()
    {
        $leasings = DataLeasing::with('kontrak')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $kontraks = InvKontrak::with('penawaran')
            ->latest()
            ->get();

        $asuransis = Asuransi::orderBy('nama_asuransi')->get();

        return view('admin.data_leasing.index', compact('leasings', 'kontraks', 'asuransis'));
    }

    /* ─────────────────────────────────────────────
       GET KONTRAK DETAIL (AJAX – untuk auto-fill form)
    ───────────────────────────────────────────── */
    public function getKontrakDetail($id)
    {
        $kontrak   = InvKontrak::with('penawaran.items.kendaraan')->findOrFail($id);
        $penawaran = $kontrak->penawaran;

        // Ambil angka tanggal dari perjanjian_pembayaran (jatuh tempo)
        $jatuhTempo = $kontrak->perjanjian_pembayaran
            ? (int) \Carbon\Carbon::parse($kontrak->perjanjian_pembayaran)->format('d')
            : null;

        // Periode mulai = tanggal_kontrak, periode selesai = tanggal_selesai
        $periodeMulai   = $kontrak->tanggal_kontrak
            ? \Carbon\Carbon::parse($kontrak->tanggal_kontrak)->format('Y-m-d')
            : null;
        $periodeSelesai = $kontrak->tanggal_selesai
            ? \Carbon\Carbon::parse($kontrak->tanggal_selesai)->format('Y-m-d')
            : null;

        // Kumpulkan semua kendaraan dari items
        $kendaraans = $penawaran?->items->map(function ($item) {
            $k = $item->kendaraan;
            return [
                'mobil' => $k ? trim($k->merk) : '',
                'tahun' => $k ? (string) $k->tahun_pembuatan : ($item->tahun_unit ?? ''),
                'nopol' => $k ? $k->nopol : '',
            ];
        })->values() ?? collect();

        return response()->json([
            'no_kontrak'      => $kontrak->no_kontrak,
            'user_leasing'    => $penawaran?->kepada ?? '',
            'kendaraans'      => $kendaraans,
            'jatuh_tempo'     => $jatuhTempo,
            'periode_mulai'   => $periodeMulai,
            'periode_selesai' => $periodeSelesai,
        ]);
    }

    /* ─────────────────────────────────────────────
       STORE
    ───────────────────────────────────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'no_kontrak'         => 'nullable|string|max:255',
            'mobil'              => 'nullable|string|max:500',
            'tahun'              => 'nullable|string|max:255',
            'nopol'              => 'nullable|string|max:500',
            'user_leasing'       => 'nullable|string|max:255',
            'angsuran_per_bulan' => 'nullable|numeric|min:0',
            'jatuh_tempo'        => 'nullable|integer|min:1|max:31',
            'periode_mulai'      => 'nullable|date',
            'periode_selesai'    => 'nullable|date',
            'personal_account'   => 'nullable|string|max:255',
            'sumber_dana_debit'  => 'nullable|string|max:255',
            'cara_bayar'         => 'nullable|string|max:255',
            'asuransi_leasing'   => 'nullable|string|max:255',
        ]);

        DataLeasing::create([
            'kontrak_id'         => $request->kontrak_id ?: null,
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
            'no_kontrak'         => 'nullable|string|max:255',
            'mobil'              => 'nullable|string|max:500',
            'tahun'              => 'nullable|string|max:255',
            'nopol'              => 'nullable|string|max:500',
            'user_leasing'       => 'nullable|string|max:255',
            'angsuran_per_bulan' => 'nullable|numeric|min:0',
            'jatuh_tempo'        => 'nullable|integer|min:1|max:31',
            'periode_mulai'      => 'nullable|date',
            'periode_selesai'    => 'nullable|date',
            'personal_account'   => 'nullable|string|max:255',
            'sumber_dana_debit'  => 'nullable|string|max:255',
            'cara_bayar'         => 'nullable|string|max:255',
            'asuransi_leasing'   => 'nullable|string|max:255',
        ]);

        $leasing->update([
            'kontrak_id'         => $request->kontrak_id ?: null,
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
       DESTROY
    ───────────────────────────────────────────── */
    public function destroy($id)
    {
        DataLeasing::findOrFail($id)->delete();

        return redirect()->route('data-leasing.index')
            ->with('success', 'Data Leasing berhasil dihapus.');
    }
}
