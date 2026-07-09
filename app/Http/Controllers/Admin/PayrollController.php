<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Bukubesar;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $data = Payroll::latest()->paginate(15)->withQueryString();

        $totalPegawai = Payroll::count();
        $totalGaji    = Payroll::sum('total_gaji');
        $totalBpjs    = Payroll::sum('bpjs');
        $totalPph21   = Payroll::sum('pph21');

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

        $payroll = Payroll::create($validated);

        // Auto-posting ke Buku Besar sebagai Beban Gaji
        $kodeJurnal = 'PAY-' . $payroll->id;

        Bukubesar::create([
            'kode_jurnal' => $kodeJurnal,
            'transaksi'   => 'Beban Gaji - ' . $payroll->nama_pegawai,
            'kategori'    => 'Beban',
            'tanggal'     => now()->toDateString(),
            'debit'       => $payroll->total_gaji,
            'kredit'      => 0,
            'saldo'       => $payroll->total_gaji,
            'aktivitas'   => 'Operasi',
            'keterangan'  => 'Auto-posting: Gaji ' . $payroll->nama_pegawai
                             . ' (Pokok: ' . number_format($payroll->gaji_pokok, 0, ',', '.')
                             . ', Tunjangan: ' . number_format($payroll->tunjangan, 0, ',', '.')
                             . ', THR: ' . number_format($payroll->thr, 0, ',', '.') . ')',
        ]);

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

        // Sinkron jurnal Buku Besar
        $kodeJurnal = 'PAY-' . $payroll->id;
        $jurnal     = Bukubesar::where('kode_jurnal', $kodeJurnal)->first();

        if ($jurnal) {
            $jurnal->update([
                'transaksi'  => 'Beban Gaji - ' . $payroll->nama_pegawai,
                'debit'      => $payroll->total_gaji,
                'saldo'      => $payroll->total_gaji,
                'keterangan' => 'Auto-posting: Gaji ' . $payroll->nama_pegawai
                                . ' (Pokok: ' . number_format($payroll->gaji_pokok, 0, ',', '.')
                                . ', Tunjangan: ' . number_format($payroll->tunjangan, 0, ',', '.')
                                . ', THR: ' . number_format($payroll->thr, 0, ',', '.') . ')',
            ]);
        } else {
            // Jurnal belum ada (data lama sebelum fitur ini) — buat baru
            Bukubesar::create([
                'kode_jurnal' => $kodeJurnal,
                'transaksi'   => 'Beban Gaji - ' . $payroll->nama_pegawai,
                'kategori'    => 'Beban',
                'tanggal'     => now()->toDateString(),
                'debit'       => $payroll->total_gaji,
                'kredit'      => 0,
                'saldo'       => $payroll->total_gaji,
                'aktivitas'   => 'Operasi',
                'keterangan'  => 'Auto-posting: Gaji ' . $payroll->nama_pegawai
                                 . ' (Pokok: ' . number_format($payroll->gaji_pokok, 0, ',', '.')
                                 . ', Tunjangan: ' . number_format($payroll->tunjangan, 0, ',', '.')
                                 . ', THR: ' . number_format($payroll->thr, 0, ',', '.') . ')',
            ]);
        }

        return redirect()->route('payroll.index')
            ->with('success', 'Data payroll berhasil diperbarui.');
    }

    public function destroy(Payroll $payroll)
    {
        // Hapus jurnal Buku Besar terkait sebelum delete payroll
        Bukubesar::where('kode_jurnal', 'PAY-' . $payroll->id)->delete();

        $payroll->delete();

        return redirect()->route('payroll.index')
            ->with('success', 'Data payroll berhasil dihapus.');
    }
}
