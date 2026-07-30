<?php

namespace App\Http\Controllers\Admin;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use App\Models\BiayaTambahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Keuangan;
use App\Models\Bukubesar;
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
                $q->where('nama_pelanggan', 'like', '%' . $request->search . '%');
            })->orWhereHas('kendaraan', function ($q) use ($request) {
                $q->where('merk', 'like', '%' . $request->search . '%')
                    ->orWhere('nopol', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $rentals = $query->latest()->paginate(15)->withQueryString();

        // ── SUMMARY AGGREGATE (dari seluruh data, bukan hanya halaman ini) ──
        $summaryQuery    = Rental::query();
        if ($request->search) {
            $summaryQuery->where(function ($q) use ($request) {
                $q->whereHas('member', function ($q2) use ($request) {
                    $q2->where('nama_pelanggan', 'like', '%' . $request->search . '%');
                })->orWhereHas('kendaraan', function ($q2) use ($request) {
                    $q2->where('merk', 'like', '%' . $request->search . '%')
                        ->orWhere('nopol', 'like', '%' . $request->search . '%');
                });
            });
        }
        if ($request->status) {
            $summaryQuery->where('status', $request->status);
        }
        $totalRental     = (clone $summaryQuery)->count();
        $totalPendapatan = (clone $summaryQuery)->sum('total_biaya');
        $countPending    = (clone $summaryQuery)->where('status', 'Pending')->count();
        $countBooking    = (clone $summaryQuery)->where('status', 'booking')->count();
        $countAktif      = (clone $summaryQuery)->where('status', 'aktif')->count();
        $countSelesai    = (clone $summaryQuery)->where('status', 'selesai')->count();
        $countBatal      = (clone $summaryQuery)->where('status', 'batal')->count();

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

        foreach ($rentals->getCollection() as $r) {

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
            'rentals'          => $rentals,
            'totalRental'      => $totalRental,
            'totalPendapatan'  => $totalPendapatan,
            'countPending'     => $countPending,
            'countBooking'     => $countBooking,
            'countAktif'       => $countAktif,
            'countSelesai'     => $countSelesai,
            'countBatal'       => $countBatal,
            'members'          => Pelanggan::all(),
            'pelangganJson'    => Pelanggan::select('id', 'nama_pelanggan', 'kontak_pelanggan', 'email_pelanggan', 'jenis_pelanggan', 'alamat')->get(),
            'kendaraans'       => Kendaraan::all(),
            'bookedDates'      => $bookedDates,
        ]);
    }

    private function formatSisa($seconds)
    {
        $seconds = (int) $seconds;
        if ($seconds <= 0)  return '0 jam';
        $hari = (int) floor($seconds / 86400);
        if ($hari >= 1)     return $hari . ' hari';
        $jam  = (int) floor($seconds / 3600);
        if ($jam  >= 1)     return $jam  . ' jam';
        return '< 1 jam';
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
            'nama_pelanggan'      => 'required_without:member_id|string',
            'tanggal_mulai'    => 'required',
            'jenis_pembayaran' => 'required|in:lunas,dp',


            'tujuan'         => 'nullable|string|max:255',
            'tujuan_perjalanan'   => 'nullable|in:dalam_kota,luar_kota',
            'nama_driver'    => 'nullable|string|max:255',
            'kontak_driver'  => 'nullable|string|max:50',
            'biaya_driver'   => 'nullable|numeric|min:0',
            'alamat_pengantaran'  => 'nullable|string|max:255',
            'alamat_penjemputan'  => 'nullable|string|max:255',

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
                $member = Pelanggan::find($request->member_id);
            } else {
                $member = Pelanggan::firstOrCreate(
                    ['nama_pelanggan' => $request->nama_pelanggan],
                    [
                        'email_pelanggan'  => $request->email_pelanggan,
                        'kontak_pelanggan' => $request->kontak_pelanggan,
                        'alamat'           => $request->alamat_pelanggan,
                        'jenis_pelanggan'  => $request->jenis_pelanggan,
                    ]
                );
            }

            /*
            |------------------------------------------------------------------
            | BUAT RENTAL
            |------------------------------------------------------------------
            */
            $rental                       = new Rental();
            $rental->user_id              = auth()->id();
            $rental->kendaraan_id         = $request->kendaraan_id;
            $rental->member_id            = $member->id;
            $rental->tanggal_mulai        = $request->tanggal_mulai;
            $rental->tanggal_selesai      = $request->tanggal_selesai
                ? Carbon::parse($request->tanggal_selesai)
                : null;
            // DRIVER & PERJALANAN
            $rental->tujuan               = $request->tujuan;
            $rental->tujuan_perjalanan    = $request->tujuan_perjalanan;
            $rental->nama_driver          = $request->nama_driver;
            $rental->kontak_driver        = $request->kontak_driver;
            $rental->biaya_driver         = $request->biaya_driver ?? 0;
            $rental->alamat_pengantaran   = $request->alamat_pengantaran;
            $rental->alamat_penjemputan   = $request->alamat_penjemputan;


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
            'nominal_pelunasan' => 'required|numeric|min:0',
        ]);

        // Validasi nominal: tidak boleh kurang dari sisa tagihan
        $sudahBayar = $rental->nominal_dp ?? 0;
        $sisa       = $rental->total_biaya - $sudahBayar;

        if ((float) $request->nominal_pelunasan < (float) $sisa) {
            return back()->with('error',
                'Nominal pelunasan (Rp ' . number_format($request->nominal_pelunasan, 0, ',', '.') .
                ') kurang dari sisa tagihan (Rp ' . number_format($sisa, 0, ',', '.') . ')'
            );
        }

        DB::beginTransaction();
        try {
            if ($request->hasFile('bukti_pelunasan')) {
                $file     = $request->file('bukti_pelunasan');
                $filename = time() . '_pelunasan_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pembayaran'), $filename);
                $rental->bukti_pelunasan = 'uploads/pembayaran/' . $filename;
            }

            $rental->nominal_pelunasan = $request->nominal_pelunasan;
            $rental->status_pembayaran = 'lunas';
            $rental->status            = 'aktif';
            $rental->save();

            // Auto-create entri Keuangan untuk pelunasan
            $kodeJurnal = 'PLS-' . $rental->id;
            if (!Keuangan::where('reference', $kodeJurnal)->exists()) {
                $lastSaldo = (float) DB::table('keuangans')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
                Keuangan::create([
                    'tanggal'     => now()->toDateString(),
                    'reference'   => $kodeJurnal,
                    'user_id'     => auth()->id(),
                    'kategori'    => 'Pemasukan',
                    'metode'      => 'Cash',
                    'keterangan'  => 'Pelunasan Rental #' . $rental->id . ' - ' . optional($rental->kendaraan)->merk . ' ' . optional($rental->kendaraan)->nopol,
                    'pemasukan'   => $request->nominal_pelunasan,
                    'pengeluaran' => 0,
                    'saldo'       => $lastSaldo + $request->nominal_pelunasan,
                    'sumber'      => 'auto',
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pelunasan berhasil dicatat');
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
            DB::transaction(function () use ($request, $id) {
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
                    // Gunakan lockForUpdate untuk mencegah race condition
                    $lastSaldo = (float) DB::table('keuangans')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
                    $kodeJurnal = 'RNT-' . $rental->id;

                    // Pastikan belum ada jurnal yang sama sebelumnya
                    if (!Keuangan::where('reference', $kodeJurnal)->exists()) {
                        Keuangan::create([
                            'tanggal'     => now()->toDateString(),
                            'reference'   => $kodeJurnal,
                            'user_id'     => auth()->id(),
                            'kategori'    => 'Pemasukan',
                            'metode'      => 'Cash',
                            'keterangan'  => 'Rental ' . $rental->kendaraan->merk . ' - ' . $rental->kendaraan->nopol,
                            'pemasukan'   => $rental->total_biaya,
                            'pengeluaran' => 0,
                            'saldo'       => $lastSaldo + $rental->total_biaya,
                            'sumber'      => 'auto',
                        ]);
                    }

                    // Auto-posting ke Buku Besar
                    if (!Bukubesar::where('kode_jurnal', $kodeJurnal)->exists()) {
                        $saldoBBTerakhir = (float) DB::table('bukubesars')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
                        Bukubesar::create([
                            'kode_jurnal' => $kodeJurnal,
                            'transaksi'   => 'Pendapatan Rental - ' . $rental->kendaraan->merk . ' ' . $rental->kendaraan->nopol,
                            'kategori'    => 'Pendapatan',
                            'tanggal'     => now()->toDateString(),
                            'debit'       => 0,
                            'kredit'      => $rental->total_biaya,
                            'saldo'       => $saldoBBTerakhir + $rental->total_biaya,
                            'aktivitas'   => 'Operasi',
                            'keterangan'  => 'Auto-posting: Rental selesai - ' . $rental->kendaraan->merk . ' (' . $rental->kendaraan->nopol . ')',
                        ]);
                    }
                }
            });

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
        $rental = Rental::findOrFail($id);

        if (in_array(strtolower($rental->status), ['aktif', 'booking'])) {
            return back()->with('error', 'Rental yang sedang aktif atau booking tidak dapat dihapus. Ubah status ke Batal terlebih dahulu.');
        }

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
                $q->where('nama_pelanggan', 'like', '%' . $request->search . '%');
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

        $pdf = Pdf::loadView('admin.rental.pdf', compact('rentals', 'setting', 'logoSrc'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('data-rental.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | INVOICE (PDF rental lama — cetak dari view rental.invoice)
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

    /*
    |--------------------------------------------------------------------------
    | Task 8: INVOICE PDF — style sama persis dengan admin.invoice.print
    |--------------------------------------------------------------------------
    */
    public function invoicePdf($id)
    {
        $rental = Rental::with(['member', 'kendaraan'])->findOrFail($id);

        // 1. Cek apakah rental punya file invoice langsung (upload manual)
        if ($rental->invoice && file_exists(public_path($rental->invoice))) {
            return response()->file(public_path($rental->invoice));
        }

        // 2. Cari Invoice record yang terhubung ke rental via customer_name
        $invoice = null;
        if ($rental->member) {
            $invoice = \App\Models\Invoice::with([
                'periodes.remaks', 'kendaraan', 'kendaraans',
                'penawaran', 'kontrak', 'penawarans', 'kontraks',
            ])
            ->where('customer_name', $rental->member->nama_pelanggan)
            ->latest()
            ->first();
        }

        if ($invoice) {
            // Delegate ke InvoicesController@print agar output identik
            $ctrl = new \App\Http\Controllers\Admin\InvoicesController();
            return $ctrl->print($invoice->id);
        }

        // 3. Generate PDF invoice langsung dari data rental
        $setting = \App\Models\Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Hitung terbilang
        $grandTotal = (int) ($rental->total_biaya ?? 0);
        $terbilang  = ucwords(trim($this->penyebutRental($grandTotal))) . ' Rupiah';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.rental.invoice-pdf', [
            'rental'    => $rental,
            'setting'   => $setting,
            'logoSrc'   => $logoSrc,
            'terbilang' => $terbilang,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'dpi'                  => 96,
            'isHtml5ParserEnabled' => true,
            'defaultFont'          => 'Times New Roman',
            'isPhpEnabled'         => true,
            'margin_top'           => 0,
            'margin_bottom'        => 0,
            'margin_left'          => 0,
            'margin_right'         => 0,
            'isRemoteEnabled'      => true,
        ]);

        return $pdf->stream('Invoice-Rental-' . $rental->id . '.pdf');
    }

    private function penyebutRental(int $n): string
    {
        $n = abs($n);
        $h = ['','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas'];
        if ($n < 12)                return ' ' . $h[$n];
        elseif ($n < 20)            return $this->penyebutRental($n - 10) . ' Belas';
        elseif ($n < 100)           return $this->penyebutRental(intdiv($n, 10)) . ' Puluh' . $this->penyebutRental($n % 10);
        elseif ($n < 200)           return ' Seratus' . $this->penyebutRental($n - 100);
        elseif ($n < 1000)          return $this->penyebutRental(intdiv($n, 100)) . ' Ratus' . $this->penyebutRental($n % 100);
        elseif ($n < 2000)          return ' Seribu' . $this->penyebutRental($n - 1000);
        elseif ($n < 1000000)       return $this->penyebutRental(intdiv($n, 1000)) . ' Ribu' . $this->penyebutRental($n % 1000);
        elseif ($n < 1000000000)    return $this->penyebutRental(intdiv($n, 1000000)) . ' Juta' . $this->penyebutRental($n % 1000000);
        elseif ($n < 1000000000000) return $this->penyebutRental(intdiv($n, 1000000000)) . ' Miliar' . $this->penyebutRental($n % 1000000000);
        return '';
    }

    public function toogletatus(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
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
                    $lastSaldo = (float) DB::table('keuangans')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
                    $kodeJurnal = 'RNT-' . $rental->id;

                    if (!Keuangan::where('reference', $kodeJurnal)->exists()) {
                        Keuangan::create([
                            'tanggal'     => now()->toDateString(),
                            'reference'   => $kodeJurnal,
                            'user_id'     => auth()->id(),
                            'kategori'    => 'Pemasukan',
                            'metode'      => 'Cash',
                            'keterangan'  => 'Rental ' . $rental->kendaraan->merk . ' - ' . $rental->kendaraan->nopol,
                            'pemasukan'   => $rental->total_biaya,
                            'pengeluaran' => 0,
                            'saldo'       => $lastSaldo + $rental->total_biaya,
                            'sumber'      => 'auto',
                        ]);
                    }
                    // Auto-posting ke Buku Besar
                    if (!Bukubesar::where('kode_jurnal', $kodeJurnal)->exists()) {
                        $saldoBBTerakhir = (float) DB::table('bukubesars')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
                        Bukubesar::create([
                            'kode_jurnal' => $kodeJurnal,
                            'transaksi'   => 'Pendapatan Rental - ' . $rental->kendaraan->merk . ' ' . $rental->kendaraan->nopol,
                            'kategori'    => 'Pendapatan',
                            'tanggal'     => now()->toDateString(),
                            'debit'       => 0,
                            'kredit'      => $rental->total_biaya,
                            'saldo'       => $saldoBBTerakhir + $rental->total_biaya,
                            'aktivitas'   => 'Operasi',
                            'keterangan'  => 'Auto-posting: Rental selesai - ' . $rental->kendaraan->merk . ' (' . $rental->kendaraan->nopol . ')',
                        ]);
                    }
                }
            });

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

