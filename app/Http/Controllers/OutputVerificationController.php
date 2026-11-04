<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyResearchOutputRequest;
use App\Models\ResearchOutput;
use Illuminate\Http\RedirectResponse;

class OutputVerificationController extends Controller
{
    public function update(VerifyResearchOutputRequest $request, ResearchOutput $output): RedirectResponse
    {
        $output->update([
            ...$request->validated(),
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Status verifikasi luaran berhasil diperbarui.');
    }
}
