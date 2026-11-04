<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFundingDisbursementRequest;
use App\Models\FundingContract;
use Illuminate\Http\RedirectResponse;

class FundingDisbursementController extends Controller
{
    public function store(StoreFundingDisbursementRequest $request, FundingContract $contract): RedirectResponse
    {
        $contract->disbursements()->create([
            ...$request->safe()->except('proof'),
            'proof_path' => $request->file('proof')?->store('disbursement-proofs'),
            'recorded_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Termin pencairan berhasil dicatat.');
    }
}
