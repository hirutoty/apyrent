<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kir;
use App\Models\Kendaraan;
use App\Models\Setting;
use App\Models\KirHistory;
use App\Models\Attachment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class KirController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $reminder = match ($setting->satuan_reminder) {
            'hari'    => $setting->batas_reminder,
            'minggu'  => $setting->batas_reminder * 7,
            'bulan'   => $setting->batas_reminder * 30,
            'tahun'   => $setting->batas_reminder * 365,
            default   => $setting->batas_reminder,
        };

        return view('admin.kir.index', [
            'data' => Kir::with(['kendaraan', 'attachments'])->latest()->get(),
            'kendaraan' => Kendaraan::all(),
            'reminder' => $reminder,
        ]);
    }

    /**
     * Helper: simpan banyak attachment sekaligus untuk 1 KIR
     * (konsisten dengan Pajak, Asuransi, GPS)
     */
    private function simpanAttachments($files, $kirId)
    {
        $pathDir = public_path('kir/attachments');
        if (!file_exists($pathDir)) mkdir($pathDir, 0777, true);

        foreach ($files as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // ambil data SEBELUM file dipindah
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $size         = $file->getSize();

            $file->move($pathDir, $filename);

            Attachment::create([
                'relation_type' => 'kir',
                'relation_id'   => $kirId,
                'file_name'     => $originalName,
                'file_path'     => 'kir/attachments/' . $filename,
                'file_type'     => $extension,
                'file_size'     => $size,
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'no_uji' => 'required',
            'masa_berlaku' => 'required|date',
            'biaya' => 'required|numeric|min:0',
            'image' => 'nullable|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',

        ], [
            'kendaraan_id.required' => 'Kendaraan wajib dipilih',
            'no_uji.required' => 'Nomor uji wajib diisi',
            'masa_berlaku.required' => 'Masa berlaku wajib diisi',
            'biaya.required' => 'Biaya KIR wajib diisi',
        ]);

        // 🔥 CEK DUPLIKAT BERDASARKAN NOPOL (RELASI)
        $kendaraan = \App\Models\Kendaraan::findOrFail($request->kendaraan_id);

        $exists = Kir::whereHas('kendaraan', function ($q) use ($kendaraan) {
            $q->where('nopol', $kendaraan->nopol);
        })->exists();

        if ($exists) {
            return back()->with('error', 'Kendaraan dengan nopol ini sudah memiliki data KIR');
        }

        $data = $request->except(['bukti_attachment', '_token']); // ✅ jangan ikut masuk mass-assign

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('kir/dokumen');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $data['image'] = 'kir/dokumen/' . $filename;
        }

        $kir = Kir::create($data);

        // upload attachment tambahan (SETELAH ADA ID)
        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $kir->id);
        }

        return back()->with('success', 'Data KIR berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $kir = Kir::findOrFail($id);

        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'no_uji' => 'required',
            'masa_berlaku' => 'required|date',
            'biaya' => 'required|numeric|min:0',
            'image' => 'nullable|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $kendaraan = \App\Models\Kendaraan::findOrFail($request->kendaraan_id);

        // 🔥 CEK DUPLIKAT (EXCLUDE DATA SENDIRI)
        $exists = Kir::where('id', '!=', $id)
            ->whereHas('kendaraan', function ($q) use ($kendaraan) {
                $q->where('nopol', $kendaraan->nopol);
            })
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kendaraan dengan nopol ini sudah memiliki data KIR');
        }

        $data = $request->except(['bukti_attachment', '_token', '_method']); // ✅ jangan ikut masuk mass-assign

        if ($request->hasFile('image')) {

            // hapus file lama
            if ($kir->image && file_exists(public_path($kir->image))) {
                unlink(public_path($kir->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('kir/dokumen');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $data['image'] = 'kir/dokumen/' . $filename;
        }

        $kir->update($data);

        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $kir->id);
        }

        return back()->with('success', 'Data KIR berhasil diupdate');
    }

    public function destroy($id)
    {
        $kir = Kir::findOrFail($id);

        if ($kir->image && file_exists(public_path($kir->image))) {
            unlink(public_path($kir->image));
        }

        // hapus semua file attachment terkait
        foreach ($kir->attachments as $att) {
            if (file_exists(public_path($att->file_path))) {
                unlink(public_path($att->file_path));
            }
            $att->delete();
        }

        $kir->delete();

        return back()->with(
            'success',
            'Data KIR berhasil dihapus'
        );
    }

    /**
     * Hapus 1 attachment tertentu
     */
    public function destroyAttachment($id)
    {
        $attachment = Attachment::where('relation_type', 'kir')->findOrFail($id);

        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }

        $attachment->delete();

        return back()->with('success', 'Lampiran berhasil dihapus');
    }




    public function pdf(Request $request)
    {
        $search = $request->search;

        $data = Kir::with(['kendaraan', 'attachments'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('kendaraan', function ($q) use ($search) {
                    $q->where('nopol', 'like', "%$search%")
                        ->orWhere('merk', 'like', "%$search%");
                })
                    ->orWhere('no_uji', 'like', "%$search%");
            })
            ->get();

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.kir.pdf', compact('data', 'search', 'setting', 'logoSrc'));

        return $pdf->stream('data-kir.pdf');
    }

    public function perpanjang(Request $request, $id)
    {
        $request->validate([
            'no_uji'       => 'required',
            'masa_berlaku' => 'required|date',
            'biaya'        => 'required|numeric|min:0',
            'image'        => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $kir = Kir::findOrFail($id);

        // default pakai gambar lama
        $image = $kir->image;

        $destination = public_path('kir/dokumen');
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        // upload file dulu (kalau ada)
        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($destination, $filename);

            // ini gambar BARU
            $image = 'kir/dokumen/' . $filename;
        }

        // ✅ SIMPAN HISTORY PAKAI GAMBAR BARU
        KirHistory::create([
            'kir_id'            => $kir->id,
            'kendaraan_id'      => $kir->kendaraan_id,
            'no_uji'            => $kir->no_uji,
            'masa_berlaku'      => $kir->masa_berlaku,
            'biaya'             => $kir->biaya,
            'image'             => $image,
            'diperpanjang_pada' => now(),
        ]);

        // update data aktif
        $kir->update([
            'no_uji'       => $request->no_uji,
            'masa_berlaku' => $request->masa_berlaku,
            'biaya'        => $request->biaya,
            'image'        => $image,
        ]);

        // upload attachment tambahan (bukti pendukung perpanjangan)
        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $kir->id);
        }

        return back()->with('success', 'KIR berhasil diperpanjang!');
    }
}