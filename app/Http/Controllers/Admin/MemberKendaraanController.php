<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberKendaraan;
use App\Models\Member;
use App\Models\Kendaraan;
use Carbon\Carbon;

class MemberKendaraanController extends Controller
{
    public function index()
    {
        $data = MemberKendaraan::with(['member','kendaraan'])->latest()->paginate(15)->withQueryString();

        foreach ($data->getCollection() as $d) {
    $d->total_biaya = Carbon::parse($d->tanggal_sewa)
        ->diffInDays($d->tanggal_kembali) * $d->biaya_sewa;
}

        return view('admin.member.member_rental', [
            'data' => $data,
            'member' => Member::all(),
            'kendaraan' => Kendaraan::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:member,id',
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'tanggal_sewa' => 'required|date',
            'tanggal_kembali' => 'required|date',
            'biaya_sewa' => 'required|integer',
        ]);

        $days = Carbon::parse($request->tanggal_sewa)
                    ->diffInDays(Carbon::parse($request->tanggal_kembali));

        $total = $days * $request->biaya_sewa;

        MemberKendaraan::create([
            'member_id' => $request->member_id,
            'kendaraan_id' => $request->kendaraan_id,
            'tanggal_sewa' => $request->tanggal_sewa,
            'tanggal_kembali' => $request->tanggal_kembali,
            'biaya_sewa' => $request->biaya_sewa,
            'status_sewa' => 'aktif',
        ]);

        return back()->with('success', 'Data berhasil ditambahkan (Total: '.$total.')');
    }

    public function update(Request $request, $id)
    {
        $data = MemberKendaraan::findOrFail($id);

        $request->validate([
            'member_id' => 'required|exists:member,id',
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'tanggal_sewa' => 'required|date',
            'tanggal_kembali' => 'required|date',
            'biaya_sewa' => 'required|integer',
        ]);

        $data->update($request->all());

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        MemberKendaraan::findOrFail($id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    /*
    |----------------------------------------------------
    | AUTO STATUS UPDATE (CRON / MANUAL CALL)
    |----------------------------------------------------
    */
    public function autoUpdateStatus()
    {
        $now = Carbon::now()->toDateString();

        MemberKendaraan::where('tanggal_kembali', '<', $now)
            ->where('status_sewa', 'aktif')
            ->update(['status_sewa' => 'selesai']);
    }
}