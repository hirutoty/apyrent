<?php

namespace App\Http\Controllers\Admin;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Kendaraan;
use App\Models\Member;
use App\Models\BiayaTambahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Keuangan;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\Setting;

class RentalController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST + MODAL CREATE
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Rental::with(['kendaraan', 'member']);

        if ($request->search) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('nama_member', 'like', '%' . $request->search . '%');
            })->orWhereHas('kendaraan', function ($q) use ($request) {
                $q->where('merk', 'like', '%' . $request->search . '%')
                    ->orWhere('nopol', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $rentals = $query->latest()->get();

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        // 🔥 KONVERSI REMINDER DINAMIS
        $reminderHari = match ($setting->satuan_reminder ?? 'hari') {
            'hari'   => $setting->batas_reminder ?? 0,
            'minggu' => ($setting->batas_reminder ?? 0) * 7,
            'bulan'  => ($setting->batas_reminder ?? 0) * 30,
            'tahun'  => ($setting->batas_reminder ?? 0) * 365,
            default  => $setting->batas_reminder ?? 0,
        };

        foreach ($rentals as $r) {

            $r->reminder = false;
            $r->terlambat = false;
            $r->sisa = null;

            if ($r->tanggal_selesai && $r->tanggal_mulai) {

                $now = \Carbon\Carbon::now();
                $end = \Carbon\Carbon::parse($r->tanggal_selesai);

                $diffSeconds = $end->timestamp - $now->timestamp;

                if ($diffSeconds < 0) {
                    $r->terlambat = true;
                    $r->sisa = $this->formatSisa(abs($diffSeconds));
                } else {

                    if ($diffSeconds <= ($reminderHari * 86400)) {
                        $r->reminder = true;
                    }

                    $r->sisa = $this->formatSisa($diffSeconds);
                }
            }
        }

        $bookedDates = Rental::whereNotIn('status', ['batal'])
            ->select('kendaraan_id', 'tanggal_mulai', 'tanggal_selesai')
            ->get()
            ->groupBy('kendaraan_id')
            ->map(fn($rows) => $rows->map(fn($r) => [
                'mulai'   => $r->tanggal_mulai ? Carbon::parse($r->tanggal_mulai)->format('Y-m-d') : null,
                'selesai' => $r->tanggal_selesai ? Carbon::parse($r->tanggal_selesai)->format('Y-m-d') : null,
            ])->filter(fn($r) => $r['mulai'] && $r['selesai'])->values())
            ->toArray();

        return view('admin.rental.index', [
            'rentals'     => $rentals,
            'members'     => Member::all(),
            'membersJson' => Member::select('id', 'nama_member', 'kontak_member', 'email_member', 'jenis_member', 'alamat')->get(),
            'kendaraans'  => Kendaraan::all(),
            'bookedDates' => $bookedDates,
        ]);
    }

    private function formatSisa($seconds)
    {
        if ($seconds >= 86400) {
            return floor($seconds / 86400) . ' hari';
        }

        if ($seconds >= 3600) {
            return floor($seconds / 3600) . ' jam';
        }

        if ($seconds >= 60) {
            return floor($seconds / 60) . ' menit';
        }

        return $seconds . ' detik';
    }

    /*
    |--------------------------------------------------------------------------
    | STORE DATA
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'     => 'required|exists:kendaraan,id',
            'nama_member'      => 'required_without:member_id|string',
            'tanggal_mulai'    => 'required',
            'jenis_pembayaran' => 'required|in:lunas,dp',


            'tujuan'         => 'nullable|string|max:255',
            'nama_driver'    => 'nullable|string|max:255',
            'kontak_driver'  => 'nullable|string|max:50',
            'biaya_driver'   => 'nullable|numeric|min:0',

            'nominal_dp'  => [
                'nullable',
                'numeric',
                Rule::requiredIf(fn() => $request->jenis_pembayaran === 'dp' && $request->hasFile('bukti_dp')),
            ],

            'bukti_lunas'  => ['nullable', 'file'],
            'bukti_dp'     => ['nullable', 'file'],

            // ── DOKUMEN BARU ──
            'invoice'      => ['nullable', 'file'],
            'kelayakan'    => ['nullable', 'file'],
        ]);

        DB::beginTransaction();

        try {

            /*
            |------------------------------------------------------------------
            | MEMBER
            |------------------------------------------------------------------
            */
            if ($request->member_id) {
                $member = Member::find($request->member_id);
            } else {
                $member = Member::firstOrCreate(
                    ['nama_member' => $request->nama_member],
                    [
                        'email_member'  => $request->email,
                        'kontak_member' => $request->kontak_member,
                        'alamat'        => $request->alamat,
                        'jenis_member'  => $request->jenis_member,
                    ]
                );
            }

            /*
            |------------------------------------------------------------------
            | BUAT RENTAL
            |------------------------------------------------------------------
            */
            $rental                       = new Rental();
            $rental->user_id              = 1; // Auth::id()
            $rental->kendaraan_id         = $request->kendaraan_id;
            $rental->member_id            = $member->id;
            $rental->tanggal_mulai        = $request->tanggal_mulai;
            $rental->tanggal_selesai      = $request->tanggal_selesai
                ? Carbon::parse($request->tanggal_selesai)
                : null;
            // DRIVER
            $rental->tujuan          = $request->tujuan;
            $rental->nama_driver     = $request->nama_driver;
            $rental->kontak_driver   = $request->kontak_driver;
            $rental->biaya_driver    = $request->biaya_driver ?? 0;


            // ── DURASI (bulan / hari / tahun) ──
            $rental->durasi_bulan         = $request->durasi_bulan  ?: null;
            $rental->durasi_hari          = $request->durasi_hari   ?: null;
            $rental->durasi_tahun         = $request->durasi_tahun  ?: null;

            $rental->jenis_pembayaran     = $request->jenis_pembayaran;
            $rental->metode_pembayaran    = $request->metode_pembayaran;
            $rental->nominal_dp           = $request->nominal_dp ?? 0;
            $rental->status_pembayaran    = 'belum_bayar';
            $rental->biaya_dasar          = 0;
            $rental->biaya_tambahan_total = 0;
            $rental->total_biaya          = 0;

            /*
            |------------------------------------------------------------------
            | UPLOAD BUKTI PEMBAYARAN (ke uploads/pembayaran)
            |------------------------------------------------------------------
            */
            $sudahBayar = false;

            if ($request->hasFile('bukti_lunas')) {
                $file     = $request->file('bukti_lunas');
                $filename = time() . '_lunas_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pembayaran'), $filename);
                $rental->bukti_lunas = 'uploads/pembayaran/' . $filename;
                $sudahBayar = true;
            }

            if ($request->hasFile('bukti_dp')) {
                $file     = $request->file('bukti_dp');
                $filename = time() . '_dp_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pembayaran'), $filename);
                $rental->bukti_dp = 'uploads/pembayaran/' . $filename;
                $sudahBayar = true;
            }

            if ($request->hasFile('bukti_pelunasan')) {
                $file     = $request->file('bukti_pelunasan');
                $filename = time() . '_pelunasan_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pembayaran'), $filename);
                $rental->bukti_pelunasan = 'uploads/pembayaran/' . $filename;
                $sudahBayar = true;
            }

            /*
            |------------------------------------------------------------------
            | UPLOAD DOKUMEN BARU (ke uploads/dokumen)
            |------------------------------------------------------------------
            */
            // Pastikan folder ada
            if (!file_exists(public_path('uploads/dokumen'))) {
                mkdir(public_path('uploads/dokumen'), 0755, true);
            }

            if ($request->hasFile('invoice')) {
                $file     = $request->file('invoice');
                $filename = time() . '_invoice_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/dokumen'), $filename);
                $rental->invoice = 'uploads/dokumen/' . $filename;
            }

            if ($request->hasFile('kelayakan')) {
                $file     = $request->file('kelayakan');
                $filename = time() . '_kelayakan_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/dokumen'), $filename);
                $rental->kelayakan = 'uploads/dokumen/' . $filename;
            }

            // Status otomatis berdasarkan ada/tidaknya bukti pembayaran
            $rental->status = $sudahBayar ? 'booking' : 'Pending';
            $rental->save();

            /*
            |------------------------------------------------------------------
            | HITUNG BIAYA DASAR
            | Pola: bulan → bulan*30*hari | hari → hari*hari | tahun → tahun*12*30*hari
            |------------------------------------------------------------------
            */
            $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

            if ($request->durasi_tahun) {
                // 1 tahun = 12 bulan = 12 * 30 hari
                $biayaDasar = $request->durasi_tahun * 12 * 30 * $kendaraan->harga_sewa_per_hari;
            } elseif ($request->durasi_bulan) {
                $biayaDasar = $request->durasi_bulan * 30 * $kendaraan->harga_sewa_per_hari;
            } elseif ($request->durasi_hari) {
                $biayaDasar = $request->durasi_hari * $kendaraan->harga_sewa_per_hari;
            } else {
                $biayaDasar = 0;
            }

            $biayaDriver = $request->biaya_driver ?? 0;

            /*
            |------------------------------------------------------------------
            | UPDATE TOTAL
            |------------------------------------------------------------------
            */
            $rental->biaya_dasar          = $biayaDasar;
            $rental->total_biaya          = $biayaDasar + $biayaDriver;
            $rental->save();

            DB::commit();

            $pesan = $sudahBayar
                ? 'Rental berhasil ditambahkan & otomatis masuk status Booking'
                : 'Rental berhasil ditambahkan, silakan upload bukti pembayaran di halaman detail';

            return back()->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PELUNASAN
    |--------------------------------------------------------------------------
    */
    public function pelunasan(Request $request, $id)
    {
        $rental = Rental::findOrFail($id);

        $request->validate([
            'bukti_pelunasan'   => 'required|file',
            'nominal_pelunasan' => 'required|numeric',
        ]);

        if ($request->hasFile('bukti_pelunasan')) {
            $file     = $request->file('bukti_pelunasan');
            $filename = time() . '_pelunasan_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/pembayaran'), $filename);
            $rental->bukti_pelunasan = 'uploads/pembayaran/' . $filename;
        }

        $rental->status_pembayaran = 'lunas';
        $rental->status            = 'aktif';
        $rental->save();

        return back()->with('success', 'Pelunasan berhasil');
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $rental = Rental::with(['kendaraan', 'member', 'biayaTambahans', 'user'])
            ->findOrFail($id);

        return view('admin.rental.show', compact('rental'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    */
    // Di RentalController.php — method updateStatus()
    public function updateStatus(Request $request, $id)
    {
        try {
            $rental = Rental::findOrFail($id);
            $rental->status = $request->status;
            $rental->save();

            $kendaraan = Kendaraan::find($rental->kendaraan_id);
            if ($kendaraan) {
                if ($request->status === 'aktif') {
                    $kendaraan->status_kendaraan = 'disewa';
                }
                if (in_array($request->status, ['selesai', 'batal'])) {
                    $kendaraan->status_kendaraan = 'tersedia';
                }
                $kendaraan->save();
            }

            if ($request->status === 'selesai') {
                $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;
                Keuangan::create([
                    'tanggal'     => now()->toDateString(),
                    'reference'   => 'PAY-' . $rental->id . '-' . now()->format('His') . rand(100, 999),
                    'user_id'     => auth()->id(),
                    'kategori'    => 'Pemasukan',
                    'metode'      => 'auto',
                    'keterangan'  => 'Rental ' . $rental->kendaraan->merk . ' - ' . $rental->kendaraan->nopol,
                    'pemasukan'   => $rental->total_biaya,
                    'pengeluaran' => 0,
                    'saldo'       => $lastSaldo + $rental->total_biaya,
                ]);
            }

            // Kalau dari fetch (index) → return JSON
            // Kalau dari form (show) → redirect back
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil diubah menjadi ' . strtoupper($request->status),
                    'status'  => $request->status,
                ]);
            }

            return back()->with('success', 'Status berhasil diubah menjadi ' . strtoupper($request->status));
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $rental    = Rental::findOrFail($id);
        $kendaraan = Kendaraan::find($rental->kendaraan_id);
        if ($kendaraan) {
            $kendaraan->status_kendaraan = 'tersedia';
            $kendaraan->save();
        }
        $rental->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH BIAYA TAMBAHAN
    |--------------------------------------------------------------------------
    */
    public function tambahBiaya(Request $request, $id)
    {
        $rental = Rental::findOrFail($id);

        $biayaTambahan = BiayaTambahan::create([
            'kendaraan_id'  => $rental->kendaraan_id,
            'nama_tambahan' => $request->nama_tambahan,
            'biaya'         => $request->biaya,
        ]);

        $jumlah   = $request->jumlah;
        $subtotal = $request->biaya * $jumlah;

        $rental->biayaTambahans()->attach($biayaTambahan->id, [
            'jumlah'   => $jumlah,
            'subtotal' => $subtotal,
        ]);

        $rental->biaya_tambahan_total += $subtotal;
        $rental->total_biaya          = $rental->biaya_dasar + $rental->biaya_tambahan_total;
        $rental->save();

        return back()->with('success', 'Biaya tambahan berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | UPLOAD BUKTI TRANSFER (show page)
    |--------------------------------------------------------------------------
    */
    public function uploadBuktiTf(Request $request, $id)
    {
        $request->validate([
            'jenis_pembayaran' => 'required|in:lunas,dp',
            'bukti_lunas'      => [
                Rule::requiredIf($request->jenis_pembayaran == 'lunas'),
                'file',
                'mimes:jpg,jpeg,png,pdf',
            ],
            'nominal_dp'       => [
                Rule::requiredIf($request->jenis_pembayaran == 'dp'),
                'numeric',
            ],
            'bukti_dp'         => [
                Rule::requiredIf($request->jenis_pembayaran == 'dp'),
                'file',
                'mimes:jpg,jpeg,png,pdf',
            ],
        ]);

        $rental = Rental::findOrFail($id);

        if ($request->jenis_pembayaran == 'lunas') {
            if ($request->hasFile('bukti_lunas')) {
                $file     = $request->file('bukti_lunas');
                $filename = time() . '_lunas_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pembayaran'), $filename);
                $rental->bukti_lunas = 'uploads/pembayaran/' . $filename;
            }
            $rental->status_pembayaran = 'lunas';
            $rental->jenis_pembayaran  = 'lunas';
        }

        if ($request->jenis_pembayaran == 'dp') {
            if ($request->hasFile('bukti_dp')) {
                $file     = $request->file('bukti_dp');
                $filename = time() . '_dp_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pembayaran'), $filename);
                $rental->bukti_dp = 'uploads/pembayaran/' . $filename;
            }
            $rental->nominal_dp        = $request->nominal_dp ?? 0;
            $rental->status_pembayaran = 'dp';
            $rental->jenis_pembayaran  = 'dp';
        }

        $autoBooking = false;
        if (strtolower($rental->status) === 'pending') {
            $rental->status = 'booking';
            $autoBooking    = true;
        }

        $rental->save();

        $message = 'Bukti pembayaran berhasil diupload';
        if ($autoBooking) {
            $message .= ' & status otomatis berubah menjadi Booking.';
        }

        return back()->with('success', $message);
    }

    /*
    |--------------------------------------------------------------------------
    | PDF EXPORT
    |--------------------------------------------------------------------------
    */
    public function pdf(Request $request)
    {
        $query = Rental::with(['kendaraan', 'member']);

        if ($request->search) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('nama_member', 'like', '%' . $request->search . '%');
            })->orWhereHas('kendaraan', function ($q) use ($request) {
                $q->where('merk', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $rentals = $query->latest()->get();
        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.rental.pdf', compact('rentals', 'setting', 'logoSrc'));

        return $pdf->stream('data-rental.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | INVOICE
    |--------------------------------------------------------------------------
    */
    public function invoice($id)
    {
        $rental = Rental::with(['member', 'kendaraan'])->findOrFail($id);
        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.rental.invoice', compact('rental', 'setting', 'logoSrc'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('invoice.pdf');
    }

    public function toogletatus(Request $request, $id)
    {
        try {
            $rental = Rental::findOrFail($id);
            $rental->status = $request->status;
            $rental->save();

            $kendaraan = Kendaraan::find($rental->kendaraan_id);
            if ($kendaraan) {
                if ($request->status === 'aktif') {
                    $kendaraan->status_kendaraan = 'disewa';
                }
                if (in_array($request->status, ['selesai', 'batal'])) {
                    $kendaraan->status_kendaraan = 'tersedia';
                }
                $kendaraan->save();
            }

            if ($request->status === 'selesai') {
                $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;
                Keuangan::create([
                    'tanggal'     => now()->toDateString(),
                    'reference'   => 'PAY-' . $rental->id . '-' . now()->format('His') . rand(100, 999),
                    'user_id'     => auth()->id(),
                    'kategori'    => 'Pemasukan',
                    'metode'      => 'auto',
                    'keterangan'  => 'Rental ' . $rental->kendaraan->merk . ' - ' . $rental->kendaraan->nopol,
                    'pemasukan'   => $rental->total_biaya,
                    'pengeluaran' => 0,
                    'saldo'       => $lastSaldo + $rental->total_biaya,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah menjadi ' . strtoupper($request->status),
                'status'  => $request->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
