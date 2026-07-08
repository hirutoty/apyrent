<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftLembur;
use Illuminate\Http\Request;

class ShiftLemburController extends Controller
{
    public function index()
    {
        $data = ShiftLembur::latest()->paginate(15)->withQueryString();

        $totalShift   = ShiftLembur::count();
        $totalLembur  = ShiftLembur::whereNotNull('jam_lembur')->where('jam_lembur', '!=', '')->count();
        $totalPegawai = ShiftLembur::distinct('nama_pegawai')->count('nama_pegawai');
        $shiftStats   = ShiftLembur::selectRaw('shift, count(*) as total')->groupBy('shift')->pluck('total', 'shift');

        return view('admin.hrd.shift.index', compact(
            'data', 'totalShift', 'totalLembur', 'totalPegawai', 'shiftStats'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'shift'        => 'required|string|max:255',
            'jam_masuk'    => 'required',
            'jam_pulang'   => 'required',
            'jam_lembur'   => 'nullable|string|max:50',
            'total_jam'    => 'required|string|max:50',
            'keterangan'   => 'required|string|max:500',
        ]);

        ShiftLembur::create($validated);

        return redirect()->route('shift.index')
            ->with('success', 'Data shift & lembur berhasil ditambahkan.');
    }

    public function update(Request $request, ShiftLembur $shift)
    {
        $validated = $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'shift'        => 'required|string|max:255',
            'jam_masuk'    => 'required',
            'jam_pulang'   => 'required',
            'jam_lembur'   => 'nullable|string|max:50',
            'total_jam'    => 'required|string|max:50',
            'keterangan'   => 'required|string|max:500',
        ]);

        $shift->update($validated);

        return redirect()->route('shift.index')
            ->with('success', 'Data shift & lembur berhasil diperbarui.');
    }

    public function destroy(ShiftLembur $shift)
    {
        $shift->delete();

        return redirect()->route('shift.index')
            ->with('success', 'Data shift & lembur berhasil dihapus.');
    }
}
