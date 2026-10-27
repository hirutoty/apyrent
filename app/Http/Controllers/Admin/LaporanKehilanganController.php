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
            'status'          => 'required|in:sementara,permanen',
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

            // Ambil data kendaraan untuk snapshot
            $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

            $kehilangan = Kehilangan::create([
                'kendaraan_id'    => $request->kendaraan_id,
                'status'          => $request->status,
                'tanggal'         => $request->tanggal,
                'keterangan'      => $request->keterangan,
                'lokasi_kejadian' => $request->lokasi_kejadian,
                'pelanggan_id'    => $request->pelanggan_id ?: null,
                'pengurus'        => $request->pengurus,
                'informasi_gps'   => $request->informasi_gps,
                'file'            => $filePath,
                // Simpan snapshot data kendaraan
                'kendaraan_nopol' => $kendaraan->nopol,
                'kendaraan_merk'  => $kendaraan->merk,
                'kendaraan_type'  => null, // tidak ada kolom type di kendaraan
                'kendaraan_warna' => $kendaraan->warna ?? null,
                'kendaraan_tahun' => $kendaraan->tahun_pembuatan ?? null,
            ]);

            // Update status kendaraan berdasarkan status kehilangan
            $kehilangan->load('kendaraan');
            
            \Log::info('DEBUG Kehilangan Created', [
                'kehilangan_id' => $kehilangan->id,
                'kendaraan_id' => $request->kendaraan_id,
                'status_kehilangan' => $request->status,
                'status_kendaraan_sebelum' => $kendaraan->status_kendaraan,
                'snapshot_saved' => [
                    'nopol' => $kehilangan->kendaraan_nopol,
                    'merk'  => $kehilangan->kendaraan_merk,
                ],
            ]);
            
            if ($request->status === 'sementara') {
                // Sementara: status kendaraan jadi 'hilang', tapi masih ada di stok
                $kendaraan->update(['status_kendaraan' => 'hilang']);
                $kendaraanStatusMsg = 'Status kendaraan diubah menjadi Hilang (Sementara).';
                
            } elseif ($request->status === 'permanen') {
                // Permanen: HAPUS kendaraan dari stok (soft delete)
                $kendaraan->delete();
                $kendaraanStatusMsg = 'Kendaraan dihapus dari stok karena status permanen.';
            } else {
                $kendaraanStatusMsg = '';
            }
            
            \Log::info('DEBUG Kendaraan Updated', [
                'status_kehilangan' => $request->status,
                'action' => $request->status === 'permanen' ? 'DELETED' : 'STATUS_CHANGED',
            ]);

            // Log pertama otomatis
            $statusLabel = $request->status === 'sementara' ? 'Sementara (Sedang Dicari)' : 'Permanen (Hilang Tetap)';
            
            LaporanKehilangan::create([
                'kehilangan_id'    => $kehilangan->id,
                'laporan'          => 'Laporan kehilangan dibuat oleh ' . Auth::user()->name . ' dengan status ' . $statusLabel . ($kendaraanStatusMsg ? '. ' . $kendaraanStatusMsg : '.'),
                'tanggal_laporan'  => $request->tanggal,
                'keterangan'       => $request->keterangan,
                'lokasi_pencarian' => $request->lokasi_kejadian,
                'file'             => $filePath,
            ]);
        });

        return redirect()->route('laporan-kehilangan.index', ['tab' => $request->status])
            ->with('success', 'Laporan kehilangan berhasil dibuat dengan status ' . ucfirst($request->status) . '.');
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
            'status_baru' => 'required|in:permanen,ditemukan',
        ]);

        $kehilangan = Kehilangan::with('kendaraan')->findOrFail($id);

        $allowed = $kehilangan->allowedTransitions();
        if (!in_array($request->status_baru, $allowed)) {
            return back()->withErrors(['status_baru' => 'Transisi status tidak diizinkan dari "' . $kehilangan->status . '" ke "' . $request->status_baru . '"']);
        }

        DB::transaction(function () use ($request, $kehilangan) {
            $statusLama = $kehilangan->status;
            $statusBaru = $request->status_baru;

            $kehilangan->update(['status' => $statusBaru]);

            // Logic berdasarkan status baru
            if ($statusBaru === 'permanen') {
                // Permanen: HAPUS kendaraan dari stok (soft delete)
                $kehilangan->kendaraan->delete();
                $kendaraanMsg = 'Kendaraan dihapus dari stok.';
                
            } elseif ($statusBaru === 'ditemukan') {
                // Ditemukan: kembalikan kendaraan ke status 'tersedia'
                // Cek apakah kendaraan masih ada (belum dihapus permanen)
                $kendaraan = Kendaraan::withTrashed()->find($kehilangan->kendaraan_id);
                
                if ($kendaraan && $kendaraan->trashed()) {
                    // Restore kendaraan yang sudah soft deleted
                    $kendaraan->restore();
                    $kendaraan->update(['status_kendaraan' => 'tersedia']);
                    $kendaraanMsg = 'Kendaraan dikembalikan ke stok dengan status Tersedia.';
                } elseif ($kendaraan) {
                    // Kendaraan masih ada, cukup update status
                    $kendaraan->update(['status_kendaraan' => 'tersedia']);
                    $kendaraanMsg = 'Kendaraan kembali tersedia.';
                } else {
                    $kendaraanMsg = 'Kendaraan tidak dapat dikembalikan (sudah dihapus permanen).';
                }
            } else {
                $kendaraanMsg = '';
            }

            $pesanLog = match($statusBaru) {
                'permanen'  => 'Status diubah ke Permanen oleh ' . Auth::user()->name . '. ' . $kendaraanMsg,
                'ditemukan' => 'Status diubah ke Ditemukan oleh ' . Auth::user()->name . '. ' . $kendaraanMsg,
                default     => 'Status diubah dari ' . $statusLama . ' ke ' . $statusBaru . ' oleh ' . Auth::user()->name . '.',
            };

            LaporanKehilangan::create([
                'kehilangan_id'    => $kehilangan->id,
                'laporan'          => $pesanLog,
                'tanggal_laporan'  => now()->toDateString(),
                'keterangan'       => null,
                'lokasi_pencarian' => null,
                'file'             => null,
            ]);
        });

        // Redirect ke index jika dari quick change, ke detail jika dari detail page
        if ($request->has('from_index')) {
            return redirect()->route('laporan-kehilangan.index', ['tab' => $request->status_baru])
                ->with('success', 'Status berhasil diubah ke "' . ucfirst($request->status_baru) . '".');
        }

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

        if ($kehilangan->status === 'permanen') {
            return back()->withErrors(['delete' => 'Laporan dengan status Permanen tidak bisa dihapus karena sudah mempengaruhi stok kendaraan.']);
        }

        if ($kehilangan->status === 'ditemukan') {
            return back()->withErrors(['delete' => 'Laporan dengan status Ditemukan tidak bisa dihapus karena sudah diselesaikan.']);
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
