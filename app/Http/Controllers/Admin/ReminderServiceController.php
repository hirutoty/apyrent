<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReminderService;
use App\Models\Kendaraan;

class ReminderServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        $data = ReminderService::with([
                'kendaraan.jenis',
                'servicePart.category',
            ])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('nama_reminder', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%")
                      ->orWhereHas('kendaraan', fn($k) =>
                          $k->where('merk', 'like', "%{$search}%")
                            ->orWhere('nopol', 'like', "%{$search}%")
                      )
                      ->orWhereHas('servicePart', fn($p) =>
                          $p->where('nama_part', 'like', "%{$search}%")
                            ->orWhere('posisi', 'like', "%{$search}%")
                            ->orWhereHas('category', fn($c) =>
                                $c->where('nama', 'like', "%{$search}%")
                            )
                      );
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalAktif      = ReminderService::where('status', 'aktif')->count();
        $totalJatuhTempo = ReminderService::where('status', 'jatuh_tempo')->count();
        $totalSelesai    = ReminderService::where('status', 'selesai')->count();

        return view('admin.service.reminder_service', compact(
            'data', 'totalAktif', 'totalJatuhTempo', 'totalSelesai'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aktif,jatuh_tempo,selesai',
        ]);

        ReminderService::findOrFail($id)->update(['status' => $request->status]);

        return back()->with('success', 'Status reminder berhasil diubah.');
    }

    public function destroy($id)
    {
        ReminderService::findOrFail($id)->delete();
        return back()->with('success', 'Reminder berhasil dihapus.');
    }
}
