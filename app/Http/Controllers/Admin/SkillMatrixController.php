<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkillMatrix;
use Illuminate\Http\Request;

class SkillMatrixController extends Controller
{
    public function index()
    {
        $data = SkillMatrix::latest()->paginate(15)->withQueryString();

        $totalSkill         = SkillMatrix::count();
        $totalBersertifikat = SkillMatrix::where('sertifikasi', 'Y')->count();
        $totalPegawai       = SkillMatrix::distinct('nama_pegawai')->count('nama_pegawai');
        $rataLevel          = round(SkillMatrix::avg('level') ?? 0, 1);

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
