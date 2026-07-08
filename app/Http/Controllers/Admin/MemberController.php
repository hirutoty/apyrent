<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class MemberController extends Controller
{
    public function index()
    {
        return view('admin.member.index', [
            'data' => Member::latest()->paginate(15)->withQueryString()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_member' => 'required|string|max:255|unique:member,nama_member',
            'kontak_member' => 'nullable|string|max:50',
            'email_member' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'jenis_member' => 'required|in:perorangan,perusahaan',
        ], [
            'nama_member.required' => 'Nama member wajib diisi',
            'nama_member.unique' => 'Nama member sudah digunakan, tidak boleh sama',
            'jenis_member.required' => 'Jenis member Wajib diisi',

        ]);

        Member::create([
            'nama_member' => $request->nama_member,
            'kontak_member' => $request->kontak_member,
            'email_member' => $request->email_member,
            'alamat' => $request->alamat,
            'jenis_member' => $request->jenis_member,
        ]);

        return back()->with('success', 'Member berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        $request->validate([
            'nama_member' => 'required|string|max:255|unique:member,nama_member,' . $id,
            'kontak_member' => 'string|max:50',
            'email_member' => 'nullable|max:50',
            'alamat' => 'nullable|string',
            'jenis_member' => 'required|in:perorangan,perusahaan',
        ], [
            'nama_member.required' => 'Nama member wajib diisi',
            'jenis_member.required' => 'Jenis member wajib diisi',
            'nama_member.unique' => 'Nama member sudah dipakai member lain',
        ]);

        $member->update([
            'nama_member' => $request->nama_member,
            'kontak_member' => $request->kontak_member,
            'email_member' => $request->email_member,
            'alamat' => $request->alamat,
            'jenis_member' => $request->jenis_member,
        ]);

        return back()->with('success', 'Member berhasil diupdate');
    }

    public function destroy($id)
    {
        Member::findOrFail($id)->delete();

        return back()->with('success', 'Member berhasil dihapus');
    }

    public function pdf(Request $request)
    {
        $query = Member::query();

        // ── FILTER SEARCH ──
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_member', 'like', '%' . $request->search . '%')
                    ->orWhere('kontak_member', 'like', '%' . $request->search . '%')
                    ->orWhere('email_member', 'like', '%' . $request->search . '%')
                    ->orWhere('alamat', 'like', '%' . $request->search . '%')
                    ->orWhere('jenis_member', 'like', '%' . $request->search . '%');
            });
        }

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $data = $query->latest()->get();

        $pdf = PDF::loadView('admin.member.pdf', compact('data', 'request', 'setting', 'logoSrc'));

        return $pdf->stream('data-member.pdf');
    }
}
