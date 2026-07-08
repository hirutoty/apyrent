<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailDomain;
use Illuminate\Http\Request;

class EmailDomainController extends Controller
{
    public function index()
    {
        $data           = EmailDomain::latest()->paginate(15)->withQueryString();
        $totalDomain    = EmailDomain::count();
        $totalAktif     = EmailDomain::where('status', 'aktif')->count();
        $totalExpired   = EmailDomain::where('status', 'nonaktif')->count();
        $totalDns       = EmailDomain::where('dns_terkelola', true)->count();

        return view('admin.technology.emaild.index',
            compact('data','totalDomain','totalAktif','totalExpired','totalDns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_domain'   => 'required|string|max:255',
            'provider'      => 'required|string|max:100',
            'status'        => 'required|in:aktif,nonaktif',
            'expired_date'  => 'nullable|date',
            'email_aktif'   => 'required|integer|min:0',
            'dns_terkelola' => 'required|in:0,1',
        ]);

        EmailDomain::create($request->only([
            'nama_domain','provider','status','expired_date','email_aktif','dns_terkelola'
        ]));

        return redirect()->route('emaild.index')->with('success', 'Email & Domain berhasil ditambahkan.');
    }

    public function update(Request $request, EmailDomain $emaild)
    {
        $request->validate([
            'nama_domain'   => 'required|string|max:255',
            'provider'      => 'required|string|max:100',
            'status'        => 'required|in:aktif,nonaktif',
            'expired_date'  => 'nullable|date',
            'email_aktif'   => 'required|integer|min:0',
            'dns_terkelola' => 'required|in:0,1',
        ]);

        $emaild->update($request->only([
            'nama_domain','provider','status','expired_date','email_aktif','dns_terkelola'
        ]));

        return redirect()->route('emaild.index')->with('success', 'Email & Domain berhasil diperbarui.');
    }

    public function destroy(EmailDomain $emaild)
    {
        $emaild->delete();
        return redirect()->route('emaild.index')->with('success', 'Email & Domain berhasil dihapus.');
    }
}
