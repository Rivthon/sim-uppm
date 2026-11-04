<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinalReportRequest;
use App\Models\FundingContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FinalReportController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $contracts = FundingContract::query()
            ->with(['proposal.lead', 'finalReport', 'outputs'])
            ->when(! $user->hasRole('admin_uppm'), fn ($query) => $query->whereHas('proposal', fn ($proposal) => $proposal->whereBelongsTo($user, 'lead')))
            ->latest('id')->paginate(10);

        return view('reporting.index', compact('contracts'));
    }

    public function show(FundingContract $contract): View
    {
        abort_unless(auth()->user()->hasRole('admin_uppm') || $contract->proposal->lead_id === auth()->id(), 403);
        $contract->load(['proposal.lead', 'finalReport.submitter', 'outputs.creator', 'outputs.verifier']);

        return view('reporting.show', compact('contract'));
    }

    public function store(StoreFinalReportRequest $request, FundingContract $contract): RedirectResponse
    {
        $contract->finalReport()->create([
            ...$request->safe()->except('document'),
            'document_path' => $request->file('document')->store('final-reports'),
            'status' => 'submitted', 'submitted_at' => now(), 'submitted_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Laporan akhir berhasil dikirim.');
    }
}
