<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SerahterimaBook;
use App\Models\SerahterimaKontrak;
use App\Models\Rental;
use App\Models\InvKontrak;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\Storage;

class SerahterimaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'booking'); // default tab

        if ($tab === 'booking') {
            $query = SerahterimaBook::with(['rental.kendaraan', 'rental.member'])->latest('tanggal');

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('tanggal', 'like', "%{$s}%")
                        ->orWhereHas('rental.kendaraan', fn($k) => $k->where('nopol', 'like', "%{$s}%"))
                        ->orWhereHas('rental.member', fn($m) => $m->where('nama', 'like', "%{$s}%"));
                });
            }

            $dataBooking = $query->paginate(15)->withQueryString();
            $dataKontrak = collect();

            // Summary untuk booking
            $totalBooking = SerahterimaBook::count();
            $totalFileMasuk = SerahterimaBook::whereNotNull('file_masuk')->count();
            $totalFileKeluar = SerahterimaBook::whereNotNull('file_keluar')->count();
        } else {
            $query = SerahterimaKontrak::with(['kontrak', 'rental.kendaraan', 'rental.member'])->latest('tanggal');

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('tanggal', 'like', "%{$s}%")
                        ->orWhereHas('rental.kendaraan', fn($k) => $k->where('nopol', 'like', "%{$s}%"))
                        ->orWhereHas('rental.member', fn($m) => $m->where('nama', 'like', "%{$s}%"))
                        ->orWhereHas('kontrak', fn($kt) => $kt->where('no_kontrak', 'like', "%{$s}%"));
                });
            }

            $dataKontrak = $query->paginate(15)->withQueryString();
            $dataBooking = collect();

            // Summary untuk kontrak
            $totalBooking = SerahterimaKontrak::count();
            $totalFileMasuk = SerahterimaKontrak::whereNotNull('file_masuk')->count();
            $totalFileKeluar = SerahterimaKontrak::whereNotNull('file_keluar')->count();
        }

        // Data untuk dropdown
        $rentals = Rental::with('kendaraan', 'member')->latest()->get();
        $kontraks = InvKontrak::with(['penawaran.items.kendaraan'])
            ->whereIn('status', ['active', 'approved'])
            ->latest()
            ->get();

        return view('admin.serahterima.index', compact(
            'dataBooking',
            'dataKontrak',
            'tab',
            'rentals',
            'kontraks',
            'totalBooking',
            'totalFileMasuk',
            'totalFileKeluar'
        ));
    }

    /**
     * Store booking serah terima
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'id_rental' => 'required|exists:rentals,id',
            'tanggal' => 'required|date',
            'file_masuk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_keluar' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'id_rental.required' => 'Rental wajib dipilih',
            'id_rental.exists' => 'Rental tidak ditemukan',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'file_masuk.mimes' => 'File masuk harus berformat PDF, JPG, JPEG, atau PNG',
            'file_masuk.max' => 'File masuk maksimal 5MB',
            'file_keluar.mimes' => 'File keluar harus berformat PDF, JPG, JPEG, atau PNG',
            'file_keluar.max' => 'File keluar maksimal 5MB',
        ]);

        $data = $request->only(['id_rental', 'tanggal']);

        // Upload file masuk
        if ($request->hasFile('file_masuk')) {
            $data['file_masuk'] = $request->file('file_masuk')->store('serahterima/booking', 'public');
        }

        // Upload file keluar
        if ($request->hasFile('file_keluar')) {
            $data['file_keluar'] = $request->file('file_keluar')->store('serahterima/booking', 'public');
        }

        SerahterimaBook::create($data);

        return redirect()->route('serahterima.index', ['tab' => 'booking'])
            ->with('success', 'Data serah terima booking berhasil ditambahkan');
    }

    /**
     * Update booking serah terima
     */
    public function updateBooking(Request $request, $id)
    {
        $request->validate([
            'id_rental' => 'required|exists:rentals,id',
            'tanggal' => 'required|date',
            'file_masuk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_keluar' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'id_rental.required' => 'Rental wajib dipilih',
            'id_rental.exists' => 'Rental tidak ditemukan',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'file_masuk.mimes' => 'File masuk harus berformat PDF, JPG, JPEG, atau PNG',
            'file_masuk.max' => 'File masuk maksimal 5MB',
            'file_keluar.mimes' => 'File keluar harus berformat PDF, JPG, JPEG, atau PNG',
            'file_keluar.max' => 'File keluar maksimal 5MB',
        ]);

        $serahterima = SerahterimaBook::findOrFail($id);

        $data = $request->only(['id_rental', 'tanggal']);

        // Upload file masuk
        if ($request->hasFile('file_masuk')) {
            // Hapus file lama
            if ($serahterima->file_masuk) {
                Storage::disk('public')->delete($serahterima->file_masuk);
            }
            $data['file_masuk'] = $request->file('file_masuk')->store('serahterima/booking', 'public');
        }

        // Upload file keluar
        if ($request->hasFile('file_keluar')) {
            // Hapus file lama
            if ($serahterima->file_keluar) {
                Storage::disk('public')->delete($serahterima->file_keluar);
            }
            $data['file_keluar'] = $request->file('file_keluar')->store('serahterima/booking', 'public');
        }

        $serahterima->update($data);

        return redirect()->route('serahterima.index', ['tab' => 'booking'])
            ->with('success', 'Data serah terima booking berhasil diupdate');
    }

    /**
     * Delete booking serah terima
     */
    public function destroyBooking($id)
    {
        $serahterima = SerahterimaBook::findOrFail($id);

        // Hapus file
        if ($serahterima->file_masuk) {
            Storage::disk('public')->delete($serahterima->file_masuk);
        }
        if ($serahterima->file_keluar) {
            Storage::disk('public')->delete($serahterima->file_keluar);
        }

        $serahterima->delete();

        return redirect()->route('serahterima.index', ['tab' => 'booking'])
            ->with('success', 'Data serah terima booking berhasil dihapus');
    }

    /**
     * Store kontrak serah terima
     */
    public function storeKontrak(Request $request)
    {
        $request->validate([
            'idkontrak' => 'nullable|exists:inv_kontraks,id',
            'id_rental' => 'required|exists:rentals,id',
            'tanggal' => 'required|date',
            'file_masuk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_keluar' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'idkontrak.exists' => 'Kontrak tidak ditemukan',
            'id_rental.required' => 'Rental wajib dipilih',
            'id_rental.exists' => 'Rental tidak ditemukan',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'file_masuk.mimes' => 'File masuk harus berformat PDF, JPG, JPEG, atau PNG',
            'file_masuk.max' => 'File masuk maksimal 5MB',
            'file_keluar.mimes' => 'File keluar harus berformat PDF, JPG, JPEG, atau PNG',
            'file_keluar.max' => 'File keluar maksimal 5MB',
        ]);

        $data = $request->only(['idkontrak', 'id_rental', 'tanggal']);

        // Upload file masuk
        if ($request->hasFile('file_masuk')) {
            $data['file_masuk'] = $request->file('file_masuk')->store('serahterima/kontrak', 'public');
        }

        // Upload file keluar
        if ($request->hasFile('file_keluar')) {
            $data['file_keluar'] = $request->file('file_keluar')->store('serahterima/kontrak', 'public');
        }

        SerahterimaKontrak::create($data);

        return redirect()->route('serahterima.index', ['tab' => 'kontrak'])
            ->with('success', 'Data serah terima kontrak berhasil ditambahkan');
    }

    /**
     * Update kontrak serah terima
     */
    public function updateKontrak(Request $request, $id)
    {
        $request->validate([
            'idkontrak' => 'nullable|exists:inv_kontraks,id',
            'id_rental' => 'required|exists:rentals,id',
            'tanggal' => 'required|date',
            'file_masuk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_keluar' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'idkontrak.exists' => 'Kontrak tidak ditemukan',
            'id_rental.required' => 'Rental wajib dipilih',
            'id_rental.exists' => 'Rental tidak ditemukan',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'file_masuk.mimes' => 'File masuk harus berformat PDF, JPG, JPEG, atau PNG',
            'file_masuk.max' => 'File masuk maksimal 5MB',
            'file_keluar.mimes' => 'File keluar harus berformat PDF, JPG, JPEG, atau PNG',
            'file_keluar.max' => 'File keluar maksimal 5MB',
        ]);

        $serahterima = SerahterimaKontrak::findOrFail($id);

        $data = $request->only(['idkontrak', 'id_rental', 'tanggal']);

        // Upload file masuk
        if ($request->hasFile('file_masuk')) {
            // Hapus file lama
            if ($serahterima->file_masuk) {
                Storage::disk('public')->delete($serahterima->file_masuk);
            }
            $data['file_masuk'] = $request->file('file_masuk')->store('serahterima/kontrak', 'public');
        }

        // Upload file keluar
        if ($request->hasFile('file_keluar')) {
            // Hapus file lama
            if ($serahterima->file_keluar) {
                Storage::disk('public')->delete($serahterima->file_keluar);
            }
            $data['file_keluar'] = $request->file('file_keluar')->store('serahterima/kontrak', 'public');
        }

        $serahterima->update($data);

        return redirect()->route('serahterima.index', ['tab' => 'kontrak'])
            ->with('success', 'Data serah terima kontrak berhasil diupdate');
    }

    /**
     * Delete kontrak serah terima
     */
    public function destroyKontrak($id)
    {
        $serahterima = SerahterimaKontrak::findOrFail($id);

        // Hapus file
        if ($serahterima->file_masuk) {
            Storage::disk('public')->delete($serahterima->file_masuk);
        }
        if ($serahterima->file_keluar) {
            Storage::disk('public')->delete($serahterima->file_keluar);
        }

        $serahterima->delete();

        return redirect()->route('serahterima.index', ['tab' => 'kontrak'])
            ->with('success', 'Data serah terima kontrak berhasil dihapus');
    }
}
