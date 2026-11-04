<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResearchOutputRequest;
use App\Models\FundingContract;
use Illuminate\Http\RedirectResponse;

class ResearchOutputController extends Controller
{
    public function store(StoreResearchOutputRequest $request, FundingContract $contract): RedirectResponse
    {
        $contract->outputs()->create([
            ...$request->safe()->except('evidence'),
            'evidence_path' => $request->file('evidence')->store('research-outputs'),
            'verification_status' => 'pending', 'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Luaran berhasil ditambahkan dan menunggu verifikasi.');
    }
}
