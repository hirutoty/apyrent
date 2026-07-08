<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkillMatrix;
use Illuminate\Http\Request;

class SkillMatrixController extends Controller
{
    public function index()
    {
        $data = SkillMatrix::latest()->get();

        $totalSkill         = $data->count();
        $totalBersertifikat = $data->where('sertifikasi', 'Y')->count();
        $totalPegawai       = $data->pluck('nama_pegawai')->unique()->count();
        $rataLevel          = $data->count() ? round($data->avg('level'), 1) : 0;

        return view('admin.hrd.skills.index', compact(
            'data', 'totalSkill', 'totalBersertifikat', 'totalPegawai', 'rataLevel'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pegawai'     => 'required|string|max:255',
            'skill'            => 'required|string|max:255',
            'level'            => 'required|integer|min:1|max:5',
            'sertifikasi'      => 'required|in:Y,T',
            'evaluator'        => 'required|string|max:255',
            'tanggal_evaluasi' => 'required|date',
        ]);

        SkillMatrix::create($validated);

        return redirect()->route('skills.index')
            ->with('success', 'Data skill berhasil ditambahkan.');
    }

    public function update(Request $request, SkillMatrix $skill)
    {
        $validated = $request->validate([
            'nama_pegawai'     => 'required|string|max:255',
            'skill'            => 'required|string|max:255',
            'level'            => 'required|integer|min:1|max:5',
            'sertifikasi'      => 'required|in:Y,T',
            'evaluator'        => 'required|string|max:255',
            'tanggal_evaluasi' => 'required|date',
        ]);

        $skill->update($validated);

        return redirect()->route('skills.index')
            ->with('success', 'Data skill berhasil diperbarui.');
    }

    public function destroy(SkillMatrix $skill)
    {
        $skill->delete();

        return redirect()->route('skills.index')
            ->with('success', 'Data skill berhasil dihapus.');
    }
}
