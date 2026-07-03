<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();

        return view('admin.setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::first();

        $rules = [
            'nama_perusahaan'     => 'required',
            'alamat'              => 'required',
            'telepon'             => 'required',
            'email'               => 'required|email',
            'website'             => 'required',
            'nama_bank'           => 'required',
            'nomor_rekening'      => 'required',
            'atas_nama_rekening'  => 'required',
            'batas_reminder'              => 'required|integer|min:1',
            'satuan_reminder'        => 'required|in:hari,minggu,bulan,tahun',
        ];

        if (!$setting || !$setting->logo) {
            $rules['logo'] = 'required|image|mimes:jpg,jpeg,png,webp|max:2048';
        } else {
            $rules['logo'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048';
        }

        $messages = [
            'nama_perusahaan.required'    => 'Nama perusahaan wajib diisi.',
            'alamat.required'             => 'Alamat perusahaan wajib diisi.',
            'telepon.required'            => 'Nomor telepon wajib diisi.',
            'email.required'              => 'Email perusahaan wajib diisi.',
            'email.email'                 => 'Format email tidak valid.',
            'website.required'            => 'Website perusahaan wajib diisi.',
            'nama_bank.required'          => 'Nama bank wajib diisi.',
            'nomor_rekening.required'     => 'Nomor rekening wajib diisi.',
            'atas_nama_rekening.required' => 'Atas nama rekening wajib diisi.',
            'logo.required'               => 'Logo perusahaan wajib diunggah.',
            'logo.image'                  => 'File logo harus berupa gambar.',
            'logo.mimes'                  => 'Logo harus berformat JPG, JPEG, PNG, atau WEBP.',
            'logo.max'                    => 'Ukuran logo maksimal 2 MB.',
            'batas_reminder.required' => 'Batas Reminder wajib dipilih.',
            'satuan_reminder.in'       => 'Jenis Reminder tidak valid.',
        ];

        $request->validate($rules, $messages);

        if (!$setting) {
            $setting = new Setting();
        }

        $data = $request->except('logo');

        /*
        |--------------------------------
        | UPLOAD LOGO (MOVE ke public)
        |--------------------------------
        */
        if ($request->hasFile('logo')) {

            $file = $request->file('logo');

            $filename = time() . '_logo_' . $file->getClientOriginalName();

            $destination = public_path('uploads/setting');

            $file->move($destination, $filename);

            $data['logo'] = 'uploads/setting/' . $filename;
        }

        $setting->fill($data);
        $setting->save();

        return redirect()->back()->with(
            'success',
            'Konfigurasi perusahaan berhasil disimpan.'
        );
    }
}
