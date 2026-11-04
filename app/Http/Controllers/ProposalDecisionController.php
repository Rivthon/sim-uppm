<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposalDecisionRequest;
use App\Models\Proposal;
use Illuminate\Http\RedirectResponse;

class ProposalDecisionController extends Controller
{
    public function store(StoreProposalDecisionRequest $request, Proposal $proposal): RedirectResponse
    {
        abort_unless($proposal->status === 'submitted', 422);

        $proposal->decision()->updateOrCreate([], [
            ...$request->validated(),
            'approved_budget' => $request->input('decision') === 'accepted' ? $request->integer('approved_budget') : null,
            'decided_by' => $request->user()->id,
            'decided_at' => now(),
        ]);

        return back()->with('success', 'Keputusan akhir proposal berhasil disimpan.');
    }
}
