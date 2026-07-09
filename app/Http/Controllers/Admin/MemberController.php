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
        return view('admin.members.index', [
            'data' => Member::withCount('kendaraans')->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function show($id)
    {
        $member = Member::with('kendaraans.jenis')->findOrFail($id);
        return view('admin.members.show', compact('member'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'            => 'required|string|max:255',
            'kontak'          => 'nullable|string|max:50',
            'email'           => 'nullable|email|max:100',
            'jenis_member'    => 'required|in:perorangan,perusahaan',
            'alamat'          => 'nullable|string',
            'file_stnk.*'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_attachment.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_kontrak'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'nama.required'         => 'Nama member wajib diisi',
            'jenis_member.required' => 'Jenis member wajib dipilih',
        ]);

        $folder = public_path('uploads/member');
        if (!file_exists($folder)) mkdir($folder, 0755, true);

        $data = [
            'nama'         => $request->nama,
            'kontak'       => $request->kontak,
            'email'        => $request->email,
            'jenis_member' => $request->jenis_member,
            'alamat'       => $request->alamat,
        ];

        // Single file
        if ($request->hasFile('file_kontrak')) {
            $file = $request->file('file_kontrak');
            $filename = time() . '_file_kontrak_' . $file->getClientOriginalName();
            $file->move($folder, $filename);
            $data['file_kontrak'] = 'uploads/member/' . $filename;
        }

        // Multiple STNK
        if ($request->hasFile('file_stnk')) {
            $paths = [];
            foreach ($request->file('file_stnk') as $file) {
                $filename = time() . '_stnk_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move($folder, $filename);
                $paths[] = 'uploads/member/' . $filename;
            }
            $data['file_stnk'] = $paths;
        }

        // Multiple attachments
        if ($request->hasFile('file_attachment')) {
            $paths = [];
            foreach ($request->file('file_attachment') as $file) {
                $filename = time() . '_att_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move($folder, $filename);
                $paths[] = 'uploads/member/' . $filename;
            }
            $data['file_attachment'] = $paths;
        }

        Member::create($data);

        return back()->with('success', 'Data member berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        $request->validate([
            'nama'            => 'required|string|max:255',
            'kontak'          => 'nullable|string|max:50',
            'email'           => 'nullable|email|max:100',
            'jenis_member'    => 'required|in:perorangan,perusahaan',
            'alamat'          => 'nullable|string',
            'file_stnk.*'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_attachment.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_kontrak'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'nama.required'         => 'Nama member wajib diisi',
            'jenis_member.required' => 'Jenis member wajib dipilih',
        ]);

        $folder = public_path('uploads/member');
        if (!file_exists($folder)) mkdir($folder, 0755, true);

        $data = [
            'nama'         => $request->nama,
            'kontak'       => $request->kontak,
            'email'        => $request->email,
            'jenis_member' => $request->jenis_member,
            'alamat'       => $request->alamat,
        ];

        // Single file kontrak
        if ($request->hasFile('file_kontrak')) {
            if ($member->file_kontrak && file_exists(public_path($member->file_kontrak))) {
                unlink(public_path($member->file_kontrak));
            }
            $file = $request->file('file_kontrak');
            $filename = time() . '_file_kontrak_' . $file->getClientOriginalName();
            $file->move($folder, $filename);
            $data['file_kontrak'] = 'uploads/member/' . $filename;
        }

        // Multiple STNK — tambahkan ke existing
        if ($request->hasFile('file_stnk')) {
            $existing = $member->file_stnk ?? [];
            foreach ($request->file('file_stnk') as $file) {
                $filename = time() . '_stnk_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move($folder, $filename);
                $existing[] = 'uploads/member/' . $filename;
            }
            $data['file_stnk'] = $existing;
        }

        // Multiple attachments — tambahkan ke existing
        if ($request->hasFile('file_attachment')) {
            $existing = $member->file_attachment ?? [];
            foreach ($request->file('file_attachment') as $file) {
                $filename = time() . '_att_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move($folder, $filename);
                $existing[] = 'uploads/member/' . $filename;
            }
            $data['file_attachment'] = $existing;
        }

        $member->update($data);

        return back()->with('success', 'Data member berhasil diupdate');
    }

    // Hapus 1 STNK tertentu
    public function destroyStnk(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $path   = $request->path;

        $stnks = collect($member->file_stnk ?? [])
            ->filter(fn($p) => $p !== $path)
            ->values()
            ->toArray();

        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }

        $member->update(['file_stnk' => $stnks]);

        return back()->with('success', 'File STNK berhasil dihapus');
    }

    // Hapus 1 attachment tertentu
    public function destroyAttachment(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $path   = $request->path;

        $attachments = collect($member->file_attachment ?? [])
            ->filter(fn($p) => $p !== $path)
            ->values()
            ->toArray();

        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }

        $member->update(['file_attachment' => $attachments]);

        return back()->with('success', 'Attachment berhasil dihapus');
    }

    public function destroy($id)
    {
        $member = Member::findOrFail($id);

        // Hapus semua STNK
        foreach ($member->file_stnk ?? [] as $path) {
            if ($path && file_exists(public_path($path))) {
                unlink(public_path($path));
            }
        }

        if ($member->file_kontrak && file_exists(public_path($member->file_kontrak))) {
            unlink(public_path($member->file_kontrak));
        }

        // Hapus semua attachment
        foreach ($member->file_attachment ?? [] as $path) {
            if ($path && file_exists(public_path($path))) {
                unlink(public_path($path));
            }
        }

        $member->delete();

        return back()->with('success', 'Data member berhasil dihapus');
    }

    public function pdf(Request $request)
    {
        $query = Member::withCount('kendaraans');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kontak', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('alamat', 'like', '%' . $request->search . '%')
                  ->orWhere('jenis_member', 'like', '%' . $request->search . '%');
            });
        }

        $data    = $query->latest()->get();
        $setting = Setting::first();

        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.members.pdf', compact('data', 'request', 'setting', 'logoSrc'));

        return $pdf->stream('data-member.pdf');
    }
}
