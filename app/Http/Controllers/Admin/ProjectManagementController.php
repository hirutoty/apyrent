<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectManagement;
use Illuminate\Http\Request;

class ProjectManagementController extends Controller
{
    public function index()
    {
        $data           = ProjectManagement::latest()->paginate(15)->withQueryString();
        $totalProyek    = ProjectManagement::count();
        $totalProgress  = ProjectManagement::where('status', 'In Progress')->count();
        $totalDone      = ProjectManagement::where('status', 'Selesai')->count();
        $totalPending   = ProjectManagement::where('status', 'Pending')->count();

        return view('admin.technology.projectm.index',
            compact('data','totalProyek','totalProgress','totalDone','totalPending'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_proyek'    => 'required|string|max:255',
            'pic_proyek'     => 'required|string|max:255',
            'tujuan'         => 'required|string',
            'estimasi_waktu' => 'required|string|max:100',
            'status'         => 'required|string|max:50',
            'progres'        => 'required|integer|min:0|max:100',
        ]);

        ProjectManagement::create($request->only([
            'nama_proyek','pic_proyek','tujuan','estimasi_waktu','status','progres'
        ]));

        return redirect()->route('projectm.index')->with('success', 'Project IT berhasil ditambahkan.');
    }

    public function update(Request $request, ProjectManagement $projectm)
    {
        $request->validate([
            'nama_proyek'    => 'required|string|max:255',
            'pic_proyek'     => 'required|string|max:255',
            'tujuan'         => 'required|string',
            'estimasi_waktu' => 'required|string|max:100',
            'status'         => 'required|string|max:50',
            'progres'        => 'required|integer|min:0|max:100',
        ]);

        $projectm->update($request->only([
            'nama_proyek','pic_proyek','tujuan','estimasi_waktu','status','progres'
        ]));

        return redirect()->route('projectm.index')->with('success', 'Project IT berhasil diperbarui.');
    }

    public function destroy(ProjectManagement $projectm)
    {
        $projectm->delete();
        return redirect()->route('projectm.index')->with('success', 'Project IT berhasil dihapus.');
    }
}
