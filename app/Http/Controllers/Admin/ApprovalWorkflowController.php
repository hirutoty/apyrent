<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalWorkflow;
use Illuminate\Http\Request;

class ApprovalWorkflowController extends Controller
{
    public function index()
    {
        $data = ApprovalWorkflow::latest()->paginate(15)->withQueryString();

        $statusStats    = ApprovalWorkflow::selectRaw('status_approval, count(*) as total')->groupBy('status_approval')->pluck('total', 'status_approval');

        $totalApproval  = ApprovalWorkflow::count();
        $totalApproved  = ApprovalWorkflow::where('status_approval', 'Approved')->count();
        $totalPending   = ApprovalWorkflow::where('status_approval', 'Pending')->count();
        $totalRejected  = ApprovalWorkflow::where('status_approval', 'Rejected')->count();

        return view('admin.approvalw.index', compact(
            'data', 'statusStats',
            'totalApproval', 'totalApproved', 'totalPending', 'totalRejected'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        ApprovalWorkflow::create($validated);

        return redirect()->route('approval-workflow.index')
            ->with('success', 'Approval Workflow berhasil ditambahkan.');
    }

    public function update(Request $request, ApprovalWorkflow $approvalWorkflow)
    {
        $validated = $this->validateData($request);

        $approvalWorkflow->update($validated);

        return redirect()->route('approval-workflow.index')
            ->with('success', 'Approval Workflow berhasil diperbarui.');
    }

    public function destroy(ApprovalWorkflow $approvalWorkflow)
    {
        $approvalWorkflow->delete();

        return redirect()->route('approval-workflow.index')
            ->with('success', 'Approval Workflow berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'id_po'           => 'required|string|max:255',
            'urutan_approval' => 'required|integer|min:1',
            'jabatan'         => 'required|string|max:255',
            'nama_approver'   => 'required|string|max:255',
            'tanggal'         => 'nullable|date',
            'status_approval' => 'required|in:Pending,Approved,Rejected',
            'catatan'         => 'nullable|string',
        ]);
    }
}
