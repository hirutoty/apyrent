<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\DataKontrak;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DataKontrakController extends Controller
{
    /* ─────────────────────────────────────────────
       INDEX
    ───────────────────────────────────────────── */
    public function index()
    {
        $kontraks   = DataKontrak::with(['kendaraan', 'attachments'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $kendaraans = Kendaraan::orderBy('merk')->get();

        return view('admin.data_leasing.index', compact('kontraks', 'kendaraans'));
    }

    /* ─────────────────────────────────────────────
       GENERATE NO KONTRAK (AJAX)
       Format: KTR-YYYYMM-XXXX
    ───────────────────────────────────────────── */
    public function generateNoKontrak()
    {
        $no = $this->buatNoKontrak();

        return response()->json(['no_kontrak' => $no]);
    }

    /* ─────────────────────────────────────────────
       GET KENDARAAN DETAIL (AJAX — auto-fill form)
    ───────────────────────────────────────────── */
    public function getKendaraanDetail($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        return response()->json([
            'mobil' => trim($kendaraan->merk),
            'nopol' => $kendaraan->nopol,
            'tahun' => (string) $kendaraan->tahun_pembuatan,
        ]);
    }

    /* ─────────────────────────────────────────────
       STORE
    ───────────────────────────────────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'       => 'nullable|exists:kendaraan,id',
            'mobil'              => 'nullable|string|max:500',
            'nopol'              => 'nullable|string|max:255',
            'tahun'              => 'nullable|string|max:10',
            'user_kontrak'       => 'nullable|string|max:255',
            'angsuran_per_bulan' => 'nullable|numeric|min:0',
            'jatuh_tempo'        => 'nullable|integer|min:1|max:31',
            'periode_mulai'      => 'nullable|date',
            'periode_selesai'    => 'nullable|date|after_or_equal:periode_mulai',
            'personal_account'   => 'nullable|string|max:255',
            'sumber_dana_debit'  => 'nullable|string|max:255',
            'cara_bayar'         => 'nullable|string|max:255',
            'nama_asuransi'      => 'nullable|string|max:255',
            'alamat_asuransi'    => 'nullable|string|max:1000',
            'nama_marketing'     => 'nullable|string|max:255',
            'kontak_marketing'   => 'nullable|string|max:50',
            'nama_bengkel'       => 'nullable|string|max:255',
            'kontak_bengkel'     => 'nullable|string|max:50',
            'bukti'              => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'attachments.*'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Generate no kontrak (ambil dari request — sudah di-generate saat modal dibuka)
        $noKontrak = $request->no_kontrak ?: $this->buatNoKontrak();

        // Handle bukti (single file)
        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $file      = $request->file('bukti');
            $filename  = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/data-kontrak'), $filename);
            $buktiPath = 'uploads/data-kontrak/' . $filename;
        }

        $kontrak = DataKontrak::create([
            'no_kontrak'         => $noKontrak,
            'kendaraan_id'       => $request->kendaraan_id ?: null,
            'mobil'              => $request->mobil,
            'nopol'              => $request->nopol,
            'tahun'              => $request->tahun,
            'user_kontrak'       => $request->user_kontrak,
            'angsuran_per_bulan' => $request->angsuran_per_bulan ?? 0,
            'jatuh_tempo'        => $request->jatuh_tempo ?: null,
            'periode_mulai'      => $request->periode_mulai ?: null,
            'periode_selesai'    => $request->periode_selesai ?: null,
            'personal_account'   => $request->personal_account,
            'sumber_dana_debit'  => $request->sumber_dana_debit,
            'cara_bayar'         => $request->cara_bayar,
            'nama_asuransi'      => $request->nama_asuransi,
            'alamat_asuransi'    => $request->alamat_asuransi,
            'nama_marketing'     => $request->nama_marketing,
            'kontak_marketing'   => $request->kontak_marketing,
            'nama_bengkel'       => $request->nama_bengkel,
            'kontak_bengkel'     => $request->kontak_bengkel,
            'bukti'              => $buktiPath,
        ]);

        // Handle attachments (multiple)
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/data-kontrak'), $filename);

                Attachment::create([
                    'relation_type' => 'data_kontrak',
                    'relation_id'   => $kontrak->id,
                    'file_name'     => $file->getClientOriginalName(),
                    'file_path'     => 'uploads/data-kontrak/' . $filename,
                    'file_type'     => $file->getMimeType(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('data-leasing.index', ['tab' => 'kontrak'])
            ->with('success', 'Data Kontrak berhasil ditambahkan. No: ' . $noKontrak);
    }

    /* ─────────────────────────────────────────────
       UPDATE
    ───────────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        $kontrak = DataKontrak::findOrFail($id);

        $request->validate([
            'kendaraan_id'       => 'nullable|exists:kendaraan,id',
            'mobil'              => 'nullable|string|max:500',
            'nopol'              => 'nullable|string|max:255',
            'tahun'              => 'nullable|string|max:10',
            'user_kontrak'       => 'nullable|string|max:255',
            'angsuran_per_bulan' => 'nullable|numeric|min:0',
            'jatuh_tempo'        => 'nullable|integer|min:1|max:31',
            'periode_mulai'      => 'nullable|date',
            'periode_selesai'    => 'nullable|date|after_or_equal:periode_mulai',
            'personal_account'   => 'nullable|string|max:255',
            'sumber_dana_debit'  => 'nullable|string|max:255',
            'cara_bayar'         => 'nullable|string|max:255',
            'nama_asuransi'      => 'nullable|string|max:255',
            'alamat_asuransi'    => 'nullable|string|max:1000',
            'nama_marketing'     => 'nullable|string|max:255',
            'kontak_marketing'   => 'nullable|string|max:50',
            'nama_bengkel'       => 'nullable|string|max:255',
            'kontak_bengkel'     => 'nullable|string|max:50',
            'bukti'              => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'attachments.*'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Handle bukti baru
        $buktiPath = $kontrak->bukti;
        if ($request->hasFile('bukti')) {
            // Hapus file lama
            if ($buktiPath && file_exists(public_path($buktiPath))) {
                unlink(public_path($buktiPath));
            }
            $file      = $request->file('bukti');
            $filename  = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/data-kontrak'), $filename);
            $buktiPath = 'uploads/data-kontrak/' . $filename;
        }

        $kontrak->update([
            'kendaraan_id'       => $request->kendaraan_id ?: null,
            'mobil'              => $request->mobil,
            'nopol'              => $request->nopol,
            'tahun'              => $request->tahun,
            'user_kontrak'       => $request->user_kontrak,
            'angsuran_per_bulan' => $request->angsuran_per_bulan ?? 0,
            'jatuh_tempo'        => $request->jatuh_tempo ?: null,
            'periode_mulai'      => $request->periode_mulai ?: null,
            'periode_selesai'    => $request->periode_selesai ?: null,
            'personal_account'   => $request->personal_account,
            'sumber_dana_debit'  => $request->sumber_dana_debit,
            'cara_bayar'         => $request->cara_bayar,
            'nama_asuransi'      => $request->nama_asuransi,
            'alamat_asuransi'    => $request->alamat_asuransi,
            'nama_marketing'     => $request->nama_marketing,
            'kontak_marketing'   => $request->kontak_marketing,
            'nama_bengkel'       => $request->nama_bengkel,
            'kontak_bengkel'     => $request->kontak_bengkel,
            'bukti'              => $buktiPath,
        ]);

        // Handle attachments baru
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/data-kontrak'), $filename);

                Attachment::create([
                    'relation_type' => 'data_kontrak',
                    'relation_id'   => $kontrak->id,
                    'file_name'     => $file->getClientOriginalName(),
                    'file_path'     => 'uploads/data-kontrak/' . $filename,
                    'file_type'     => $file->getMimeType(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('data-leasing.index', ['tab' => 'kontrak'])
            ->with('success', 'Data Kontrak berhasil diupdate.');
    }

    /* ─────────────────────────────────────────────
       DESTROY ATTACHMENT
    ───────────────────────────────────────────── */
    public function destroyAttachment($id)
    {
        $attachment = Attachment::where('relation_type', 'data_kontrak')
            ->findOrFail($id);

        if ($attachment->file_path && file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }

        $attachment->delete();

        return response()->json(['success' => true]);
    }

    /* ─────────────────────────────────────────────
       DESTROY
    ───────────────────────────────────────────── */
    public function destroy($id)
    {
        $kontrak = DataKontrak::with('attachments')->findOrFail($id);

        // Hapus semua attachment
        foreach ($kontrak->attachments as $att) {
            if ($att->file_path && file_exists(public_path($att->file_path))) {
                unlink(public_path($att->file_path));
            }
            $att->delete();
        }

        // Hapus bukti single
        if ($kontrak->bukti && file_exists(public_path($kontrak->bukti))) {
            unlink(public_path($kontrak->bukti));
        }

        $kontrak->delete();

        return redirect()->route('data-leasing.index', ['tab' => 'kontrak'])
            ->with('success', 'Data Kontrak berhasil dihapus.');
    }

    /* ─────────────────────────────────────────────
       PRIVATE — Generate No Kontrak
    ───────────────────────────────────────────── */
    private function buatNoKontrak(): string
    {
        $prefix = 'KTR-' . now()->format('Ym');
        $last   = DataKontrak::where('no_kontrak', 'like', $prefix . '-%')
            ->orderByRaw('CAST(RIGHT(no_kontrak, 4) AS UNSIGNED) DESC')
            ->first();

        $nextNumber = $last ? (int) substr($last->no_kontrak, -4) + 1 : 1;

        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
