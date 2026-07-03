<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VirtualAccount;
use App\Models\Member;
use App\Exports\VirtualAccountExport;
use App\Models\Setting;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class VirtualController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $query = VirtualAccount::with('member');

        if ($search) {
            $query->where('va_number', 'like', "%$search%")
                ->orWhere('bank', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%");
        }

        $data = $query->latest()->get();
        $members = Member::all();

        return view('admin.virtual.index', compact('data', 'members', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required',
            'bank' => 'required',
            'expected_amount' => 'required|numeric',
        ]);

        $lastId = VirtualAccount::max('id') + 1;

        $vaNumber = 'VA-' . date('Y') . '-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);

        $bukti = null;

        if ($request->hasFile('bukti_pembayaran')) {

            $file = $request->file('bukti_pembayaran');

            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('virtual-account/bukti');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $bukti = 'virtual-account/bukti/' . $filename;
        }

        VirtualAccount::create([
            'va_number' => $vaNumber,
            'member_id' => $request->member_id,
            'invoice_id' => $request->invoice_id,
            'bukti_pembayaran' => $bukti,
            'bank' => $request->bank,
            'expected_amount' => $request->expected_amount,
            'paid_amount' => $request->paid_amount ?? 0,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $data = VirtualAccount::findOrFail($id);

        $bukti = $data->bukti_pembayaran;

        if ($request->hasFile('bukti_pembayaran')) {

            // hapus file lama
            if ($data->bukti_pembayaran && file_exists(public_path($data->bukti_pembayaran))) {
                unlink(public_path($data->bukti_pembayaran));
            }

            $file = $request->file('bukti_pembayaran');

            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('virtual-account/bukti');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $bukti = 'virtual-account/bukti/' . $filename;
        }

        $data->update([
            'member_id' => $request->member_id,
            'invoice_id' => $request->invoice_id,
            'bank' => $request->bank,
            'expected_amount' => $request->expected_amount,
            'paid_amount' => $request->paid_amount,
            'status' => $request->status,
            'bukti_pembayaran' => $bukti,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $data = VirtualAccount::findOrFail($id);

        if ($data->bukti_pembayaran && file_exists(public_path($data->bukti_pembayaran))) {
            unlink(public_path($data->bukti_pembayaran));
        }

        $data->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }



    public function pdf(Request $request)
    {
        $search = $request->query('search'); // lebih aman dari input()

        $query = VirtualAccount::with('member');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('va_number', 'like', "%$search%")
                    ->orWhere('bank', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhereHas('member', function ($m) use ($search) {
                        $m->where('nama_member', 'like', "%$search%");
                    });
            });
        }

        $setting = Setting::first();
        $data = $query->get();

        $pdf = Pdf::loadView('admin.virtual.pdf', compact('data', 'search', 'setting'));

        return $pdf->stream('virtual-account.pdf');
    }



    public function exportExcel(Request $request)
    {
        $filename = 'virtual_account_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new VirtualAccountExport($request->search),
            $filename
        );
    }
}
