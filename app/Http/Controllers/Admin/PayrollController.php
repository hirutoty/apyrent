<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $data = Payroll::latest()->get();

        $totalPegawai = $data->count();
        $totalGaji    = $data->sum('total_gaji');
        $totalBpjs    = $data->sum('bpjs');
        $totalPph21   = $data->sum('pph21');

        return view('admin.hrd.payroll.index', compact(
            'data', 'totalPegawai', 'totalGaji', 'totalBpjs', 'totalPph21'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'gaji_pokok'   => 'required|numeric|min:0',
            'tunjangan'    => 'required|numeric|min:0',
            'thr'          => 'required|numeric|min:0',
            'bpjs'         => 'required|numeric|min:0',
            'pph21'        => 'required|numeric|min:0',
            'total_gaji'   => 'required|numeric|min:0',
            'slip_gaji'    => 'nullable|string|max:255',
        ]);

        Payroll::create($validated);

        return redirect()->route('payroll.index')
            ->with('success', 'Data payroll berhasil ditambahkan.');
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'gaji_pokok'   => 'required|numeric|min:0',
            'tunjangan'    => 'required|numeric|min:0',
            'thr'          => 'required|numeric|min:0',
            'bpjs'         => 'required|numeric|min:0',
            'pph21'        => 'required|numeric|min:0',
            'total_gaji'   => 'required|numeric|min:0',
            'slip_gaji'    => 'nullable|string|max:255',
        ]);

        $payroll->update($validated);

        return redirect()->route('payroll.index')
            ->with('success', 'Data payroll berhasil diperbarui.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()->route('payroll.index')
            ->with('success', 'Data payroll berhasil dihapus.');
    }
}
