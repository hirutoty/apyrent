<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Kehilangan;
use App\Models\LaporanKehilangan;
use App\Models\Kendaraan;
use App\Models\Pelanggan;

class LaporanKehilanganController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — tampil per tab status
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'sementara');
        $search = $request->get('search');

        $validTabs = ['sementara', 'permanen', 'ditemukan'];
        if (!in_array($tab, $validTabs)) {
            $tab = 'sementara';
        }

        $query = Kehilangan::with(['kendaraan', 'pelanggan', 'laporans'])
            ->where('status', $tab)
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->whereHas('kendaraan', function ($q) use ($search) {
                $q->where('nopol', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        $data = $query->paginate(15)->withQueryString();

        // Hitung jumlah per tab untuk badge
        $countSementara = Kehilangan::where('status', 'sementara')->count();
        $countPermanen  = Kehilangan::where('status', 'permanen')->count();
        $countDitemukan = Kehilangan::where('status', 'ditemukan')->count();

        // Data untuk modal tambah laporan
        $kendaraans = Kendaraan::orderBy('merk')
            ->get(['id', 'nopol', 'merk', 'status_kendaraan']);
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get(['id', 'nama_pelanggan', 'kontak_pelanggan']);

        return view('admin.laporan_kehilangan.index', compact(
            'data', 'tab', 'search',
            'countSementara', 'countPermanen', 'countDitemukan',
            'kendaraans', 'pelanggans'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE — form buat laporan baru (fallback page)
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $kendaraans = Kendaraan::orderBy('merk')
            ->get(['id', 'nopol', 'merk', 'status_kendaraan']);
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get(['id', 'nama_pelanggan', 'kontak_pelanggan']);

        return view('admin.laporan_kehilangan.create', compact('kendaraans', 'pelanggans'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE — simpan laporan kehilangan baru
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'tanggal'         => 'required|date',
            'keterangan'      => 'nullable|string|max:2000',
            'lokasi_kejadian' => 'nullable|string|max:500',
            'pelanggan_id'    => 'nullable|exists:member,id',
            'pengurus'        => 'nullable|string|max:255',
            'informasi_gps'   => 'nullable|string|max:1000',
            'file'            => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        DB::transaction(function () use ($request) {
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $this->uploadFile($request->file('file'));
            }

            $kehilangan = Kehilangan::create([
                'kendaraan_id'    => $request->kendaraan_id,
                'status'          => 'sementara',
                'tanggal'         => $request->tanggal,
                'keterangan'      => $request->keterangan,
                'lokasi_kejadian' => $request->lokasi_kejadian,
                'pelanggan_id'    => $request->pelanggan_id ?: null,
                'pengurus'        => $request->pengurus,
                'informasi_gps'   => $request->informasi_gps,
                'file'            => $filePath,
            ]);

            // Log pertama otomatis
            LaporanKehilangan::create([
                'kehilangan_id'    => $kehilangan->id,
                'laporan'          => 'Laporan kehilangan dibuat oleh ' . Auth::user()->name . '.',
                'tanggal_laporan'  => $request->tanggal,
                'keterangan'       => $request->keterangan,
                'lokasi_pencarian' => $request->lokasi_kejadian,
                'file'             => $filePath,
            ]);
        });

        return redirect()->route('laporan-kehilangan.index', ['tab' => 'sementara'])
            ->with('success', 'Laporan kehilangan berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — detail + timeline laporan
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $kehilangan = Kehilangan::with(['kendaraan', 'pelanggan', 'laporans'])
            ->findOrFail($id);

        return view('admin.laporan_kehilangan.show', compact('kehilangan'));
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH LAPORAN INDEX — tambah update dari halaman index (pilih kasus sendiri)
    |--------------------------------------------------------------------------
    */
    public function tambahLaporanIndex(Request $request)
    {
        $request->validate([
            'kehilangan_id'    => 'required|exists:kehilangans,id',
            'laporan'          => 'required|string|max:1000',
            'tanggal_laporan'  => 'required|date',
            'keterangan'       => 'nullable|string|max:1000',
            'lokasi_pencarian' => 'nullable|string|max:500',
            'file'             => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $kehilangan = Kehilangan::findOrFail($request->kehilangan_id);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $this->uploadFile($request->file('file'));
        }

        LaporanKehilangan::create([
            'kehilangan_id'    => $kehilangan->id,
            'laporan'          => $request->laporan,
            'tanggal_laporan'  => $request->tanggal_laporan,
            'keterangan'       => $request->keterangan,
            'lokasi_pencarian' => $request->lokasi_pencarian,
            'file'             => $filePath,
        ]);

        return redirect()->route('laporan-kehilangan.show', $kehilangan->id)
            ->with('success', 'Laporan perkembangan berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH LAPORAN — tambah entri update/progress manual ke timeline
    |--------------------------------------------------------------------------
    */
    public function tambahLaporan(Request $request, $id)
    {
        $request->validate([
            'laporan'          => 'required|string|max:1000',
            'tanggal_laporan'  => 'required|date',
            'keterangan'       => 'nullable|string|max:1000',
            'lokasi_pencarian' => 'nullable|string|max:500',
            'file'             => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $kehilangan = Kehilangan::findOrFail($id);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $this->uploadFile($request->file('file'));
        }

        LaporanKehilangan::create([
            'kehilangan_id'    => $kehilangan->id,
            'laporan'          => $request->laporan,
            'tanggal_laporan'  => $request->tanggal_laporan,
            'keterangan'       => $request->keterangan,
            'lokasi_pencarian' => $request->lokasi_pencarian,
            'file'             => $filePath,
        ]);

        return redirect()->route('laporan-kehilangan.show', $id)
            ->with('success', 'Update laporan berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS — ganti status + efek ke status_kendaraan
    |--------------------------------------------------------------------------
    */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_baru'       => 'required|in:permanen,ditemukan',
            'keterangan_update' => 'nullable|string|max:1000',
            'lokasi_pencarian'  => 'nullable|string|max:500',
            'file'              => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $kehilangan = Kehilangan::with('kendaraan')->findOrFail($id);

        $allowed = $kehilangan->allowedTransitions();
        if (!in_array($request->status_baru, $allowed)) {
            return back()->withErrors(['status_baru' => 'Transisi status tidak diizinkan dari "' . $kehilangan->status . '" ke "' . $request->status_baru . '"']);
        }

        DB::transaction(function () use ($request, $kehilangan) {
            $statusLama = $kehilangan->status;
            $statusBaru = $request->status_baru;

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $this->uploadFile($request->file('file'));
            }

            $kehilangan->update(['status' => $statusBaru]);

            if ($statusBaru === 'permanen') {
                $kehilangan->kendaraan->update(['status_kendaraan' => 'hilang']);
            } elseif ($statusBaru === 'ditemukan') {
                $kehilangan->kendaraan->update(['status_kendaraan' => 'tersedia']);
            }

            $pesanLog = match($statusBaru) {
                'permanen'  => 'Status diubah ke Permanen oleh ' . Auth::user()->name . '. Kendaraan dikeluarkan dari stok.',
                'ditemukan' => 'Status diubah ke Ditemukan oleh ' . Auth::user()->name . '. Kendaraan kembali tersedia.',
                default     => 'Status diubah dari ' . $statusLama . ' ke ' . $statusBaru . ' oleh ' . Auth::user()->name . '.',
            };

            LaporanKehilangan::create([
                'kehilangan_id'    => $kehilangan->id,
                'laporan'          => $pesanLog,
                'tanggal_laporan'  => now()->toDateString(),
                'keterangan'       => $request->keterangan_update,
                'lokasi_pencarian' => $request->lokasi_pencarian,
                'file'             => $filePath,
            ]);
        });

        return redirect()->route('laporan-kehilangan.show', $id)
            ->with('success', 'Status berhasil diubah ke "' . ucfirst($request->status_baru) . '".');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY — hapus laporan (hanya jika masih sementara)
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $kehilangan = Kehilangan::findOrFail($id);

        if ($kehilangan->status !== 'sementara') {
            return back()->withErrors(['delete' => 'Laporan hanya bisa dihapus jika masih berstatus Sementara.']);
        }

        if ($kehilangan->file && file_exists(public_path($kehilangan->file))) {
            unlink(public_path($kehilangan->file));
        }

        $kehilangan->delete();

        return redirect()->route('laporan-kehilangan.index', ['tab' => 'sementara'])
            ->with('success', 'Laporan kehilangan berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPER
    |--------------------------------------------------------------------------
    */
    private function uploadFile($file): string
    {
        $dir = public_path('kehilangan');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return 'kehilangan/' . $filename;
    }
}
