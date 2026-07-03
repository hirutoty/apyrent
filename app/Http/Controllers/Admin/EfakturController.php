<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EfakturExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Efaktur;
use App\Models\Setting;

class EfakturController extends Controller
{
    public function index()
    {
        $statusMap = [
        'draft' => 'Pending',
    ];

    $data = Efaktur::all()->map(function ($item) use ($statusMap) {
        $item->status = $statusMap[$item->status] ?? $item->status;
        return $item;
    });

    return view('admin.efaktur.index', compact('data'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nomor_faktur'   => 'nullable|string|max:255',
            'tanggal_faktur' => 'nullable|date',
            'tipe'           => 'nullable|in:Keluaran,Masukan',
            'npwp_lawan'     => 'nullable|string|max:255',
            'nama_lawan'     => 'nullable|string|max:255',
            'dpp'            => 'nullable|numeric',
            'ppn'            => 'nullable|numeric',
            'ppnbm'          => 'nullable|numeric',
            'status'         => 'nullable|string|max:255',
            'file_faktur' => 'nullable|file|max:5120',
        ]);

        $file = null;

        if ($request->hasFile('file_faktur')) {

            $fileUpload = $request->file('file_faktur');

            $filename = time() . '_' . $fileUpload->getClientOriginalName();
            $destination = public_path('efaktur');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $fileUpload->move($destination, $filename);

            $file = 'efaktur/' . $filename;
        }

        Efaktur::create([
            'nomor_faktur'   => $request->nomor_faktur,
            'tanggal_faktur' => $request->tanggal_faktur,
            'tipe'           => $request->tipe,
            'npwp_lawan'     => $request->npwp_lawan,
            'nama_lawan'     => $request->nama_lawan,
            'dpp'            => $request->dpp,
            'ppn'            => $request->ppn,
            'ppnbm'          => $request->ppnbm,
            'status'         => $request->status ?? 'Draft',
            'file_faktur'    => $file,
        ]);

        return back()->with('success', 'Data eFaktur berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $data = Efaktur::findOrFail($id);

        $request->validate([
            'nomor_faktur'   => 'nullable|string|max:255',
            'tanggal_faktur' => 'nullable|date',
            'tipe'           => 'nullable|in:Keluaran,Masukan',
            'npwp_lawan'     => 'nullable|string|max:255',
            'nama_lawan'     => 'nullable|string|max:255',
            'dpp'            => 'nullable|numeric',
            'ppn'            => 'nullable|numeric',
            'ppnbm'          => 'nullable|numeric',
            'status'         => 'nullable|string|max:255',
            'file_faktur' => 'nullable|file|max:5120',
        ]);

        $file = $data->file_faktur;

        if ($request->hasFile('file_faktur')) {

            // hapus file lama
            if ($data->file_faktur && file_exists(public_path($data->file_faktur))) {
                unlink(public_path($data->file_faktur));
            }

            $fileUpload = $request->file('file_faktur');

            $filename = time() . '_' . $fileUpload->getClientOriginalName();
            $destination = public_path('efaktur');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $fileUpload->move($destination, $filename);

            $file = 'efaktur/' . $filename;
        }

        $data->update([
            'nomor_faktur'   => $request->nomor_faktur,
            'tanggal_faktur' => $request->tanggal_faktur,
            'tipe'           => $request->tipe,
            'npwp_lawan'     => $request->npwp_lawan,
            'nama_lawan'     => $request->nama_lawan,
            'dpp'            => $request->dpp,
            'ppn'            => $request->ppn,
            'ppnbm'          => $request->ppnbm,
            'status'         => $request->status,
            'file_faktur'    => $file,
        ]);

        return back()->with('success', 'Data eFaktur berhasil diupdate');
    }

    public function destroy($id)
    {
        $data = Efaktur::findOrFail($id);

        if ($data->file_faktur && file_exists(public_path($data->file_faktur))) {
            unlink(public_path($data->file_faktur));
        }

        $data->delete();

        return back()->with('success', 'Data eFaktur berhasil dihapus');
    }


    public function pdf(Request $request)
    {
        $query = Efaktur::query();

        // FILTER SEARCH (sama seperti table kamu)
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nomor_faktur', 'like', "%$search%")
                    ->orWhere('nama_lawan', 'like', "%$search%")
                    ->orWhere('npwp_lawan', 'like', "%$search%")
                    ->orWhere('tipe', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            });
        }

        $setting = Setting::first();
        $data = $query->get();

        $pdf = PDF::loadView('admin.efaktur.pdf', compact('data', 'setting'));

        return $pdf->stream('efaktur.pdf');
    }



    public function exportExcel(Request $request)
    {
        $filename = 'efaktur_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new EfakturExport($request->search),
            $filename
        );
    }
}
