<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterActivityTypeRequest;
use App\Http\Requests\StoreFinalReportRequest;
use App\Models\FundingContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FinalReportController extends Controller
{
    public function index(FilterActivityTypeRequest $request): View
    {
        $user = auth()->user();
        $contracts = FundingContract::query()
            ->with(['proposal.lead', 'finalReport', 'outputs'])
            ->when($request->filled('type'), fn ($query) => $query->whereHas('proposal', fn ($proposal) => $proposal->where('type', $request->string('type'))))
            ->when(! $user->hasRole('admin_uppm'), fn ($query) => $query->whereHas('proposal', fn ($proposal) => $proposal->accessibleTo($user)))
            ->latest('id')->paginate(10);

        return view('reporting.index', compact('contracts'));
    }

    public function show(FundingContract $contract): View
    {
        abort_unless($contract->proposal->isAccessibleBy(auth()->user()), 403);
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
