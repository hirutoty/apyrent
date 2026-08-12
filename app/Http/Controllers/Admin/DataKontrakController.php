<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DataKontrakFullExport;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\DataKontrak;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DataKontrakController extends Controller
{
    /* ─────────────────────────────────────────────
       INDEX
    ───────────────────────────────────────────── */
    public function index(Request $request)
    {
        $query = DataKontrak::with(['kendaraan', 'attachments'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('serial_number', 'like', "%{$s}%")
                  ->orWhere('no_kontrak',   'like', "%{$s}%")
                  ->orWhere('mobil',        'like', "%{$s}%")
                  ->orWhere('nopol',        'like', "%{$s}%")
                  ->orWhere('user_kontrak', 'like', "%{$s}%");
            });
        }

        $kontraks   = $query->paginate(15, ['*'], 'kontrak_page')->withQueryString();
        $kendaraans = Kendaraan::orderBy('merk')->get();

        return view('admin.data_leasing.index', compact('kontraks', 'kendaraans'));
    }

    /* ─────────────────────────────────────────────
       EXPORT — Download Data Kontrak Lengkap
    ───────────────────────────────────────────── */
    public function exportKontrak()
    {
        $filename = 'data-kontrak-' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new DataKontrakFullExport(), $filename);
    }

    /* ─────────────────────────────────────────────
       GENERATE SERIAL NUMBER (AJAX)
       Format: KTR-YYYYMM-XXXX  →  ke kolom serial_number
    ───────────────────────────────────────────── */
    public function generateNoKontrak()
    {
        return response()->json([
            'serial_number' => $this->buatSerialNumber(),
        ]);
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
            'no_kontrak'         => 'required|string|max:255|unique:data_kontraks,no_kontrak',
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
            'nama_asuransi'      => 'required|string|max:255',
            'alamat_asuransi'    => 'nullable|string|max:1000',
            'nama_marketing'     => 'nullable|string|max:255',
            'kontak_marketing'   => 'nullable|string|max:50',
            'nama_bengkel'       => 'nullable|string|max:255',
            'kontak_bengkel'     => 'nullable|string|max:50',
            'bukti'              => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'attachments.*'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'no_kontrak.required'    => 'Nomor kontrak wajib diisi.',
            'no_kontrak.unique'      => 'Nomor kontrak sudah ada, gunakan nomor yang berbeda.',
            'nama_asuransi.required' => 'Nama asuransi wajib diisi.',
            'bukti.required'         => 'File bukti wajib diupload.',
            'bukti.mimes'            => 'Bukti harus berformat JPG, PNG, atau PDF.',
            'bukti.max'              => 'Ukuran bukti maksimal 5MB.',
        ]);

        // Serial number selalu auto-generate
        $serialNumber = $this->buatSerialNumber();

        // No kontrak dari input manual — sudah divalidasi required + unique
        $noKontrak = $request->no_kontrak;

        // Pastikan folder ada
        if (!is_dir(public_path('uploads/data-kontrak'))) {
            mkdir(public_path('uploads/data-kontrak'), 0777, true);
        }

        // Handle bukti (single file) — metadata diambil SEBELUM move()
        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $file         = $request->file('bukti');
            $originalName = $file->getClientOriginalName();
            $ext          = $file->getClientOriginalExtension();
            $filename     = time() . '_' . uniqid() . '.' . $ext;
            $file->move(public_path('uploads/data-kontrak'), $filename);
            $buktiPath = 'uploads/data-kontrak/' . $filename;
        }

        $kontrak = DataKontrak::create([
            'serial_number'      => $serialNumber,
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

        // Handle attachments (multiple) — metadata diambil SEBELUM move()
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $mimeType     = $file->getMimeType();
                $fileSize     = $file->getSize();
                $filename     = time() . '_' . $originalName;
                $file->move(public_path('uploads/data-kontrak'), $filename);

                Attachment::create([
                    'relation_type' => 'data_kontrak',
                    'relation_id'   => $kontrak->id,
                    'file_name'     => $originalName,
                    'file_path'     => 'uploads/data-kontrak/' . $filename,
                    'file_type'     => $mimeType,
                    'file_size'     => $fileSize,
                ]);
            }
        }

        return redirect()->route('data-leasing.index', ['tab' => 'kontrak'])
            ->with('success', 'Data Kontrak berhasil ditambahkan. Serial: ' . $serialNumber);
    }

    /* ─────────────────────────────────────────────
       UPDATE
    ───────────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        $kontrak = DataKontrak::findOrFail($id);

        $request->validate([
            'no_kontrak'         => 'required|string|max:255|unique:data_kontraks,no_kontrak,' . $id,
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
        ], [
            'no_kontrak.required' => 'Nomor kontrak wajib diisi.',
            'no_kontrak.unique'   => 'Nomor kontrak sudah ada, gunakan nomor yang berbeda.',
        ]);

        if (!is_dir(public_path('uploads/data-kontrak'))) {
            mkdir(public_path('uploads/data-kontrak'), 0777, true);
        }

        // Handle bukti baru — metadata diambil SEBELUM move()
        $buktiPath = $kontrak->bukti;
        if ($request->hasFile('bukti')) {
            if ($buktiPath && file_exists(public_path($buktiPath))) {
                unlink(public_path($buktiPath));
            }
            $file      = $request->file('bukti');
            $ext       = $file->getClientOriginalExtension();
            $filename  = time() . '_' . uniqid() . '.' . $ext;
            $file->move(public_path('uploads/data-kontrak'), $filename);
            $buktiPath = 'uploads/data-kontrak/' . $filename;
        }

        $kontrak->update([
            'no_kontrak'         => $request->no_kontrak ?: null,
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
                $originalName = $file->getClientOriginalName();
                $mimeType     = $file->getMimeType();
                $fileSize     = $file->getSize();
                $filename     = time() . '_' . $originalName;
                $file->move(public_path('uploads/data-kontrak'), $filename);

                Attachment::create([
                    'relation_type' => 'data_kontrak',
                    'relation_id'   => $kontrak->id,
                    'file_name'     => $originalName,
                    'file_path'     => 'uploads/data-kontrak/' . $filename,
                    'file_type'     => $mimeType,
                    'file_size'     => $fileSize,
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
        $attachment = Attachment::where('relation_type', 'data_kontrak')->findOrFail($id);

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

        foreach ($kontrak->attachments as $att) {
            if ($att->file_path && file_exists(public_path($att->file_path))) {
                unlink(public_path($att->file_path));
            }
            $att->delete();
        }

        if ($kontrak->bukti && file_exists(public_path($kontrak->bukti))) {
            unlink(public_path($kontrak->bukti));
        }

        $kontrak->delete();

        return redirect()->route('data-leasing.index', ['tab' => 'kontrak'])
            ->with('success', 'Data Kontrak berhasil dihapus.');
    }

    /* ─────────────────────────────────────────────
       PRIVATE — Generate Serial Number
       Format: KTR-YYYYMM-XXXX
    ───────────────────────────────────────────── */
    private function buatSerialNumber(): string
    {
        $prefix = 'KTR-' . now()->format('Ym');
        $last   = DataKontrak::where('serial_number', 'like', $prefix . '-%')
            ->orderByRaw('CAST(RIGHT(serial_number, 4) AS UNSIGNED) DESC')
            ->first();

        $next = $last ? (int) substr($last->serial_number, -4) + 1 : 1;

        return $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
