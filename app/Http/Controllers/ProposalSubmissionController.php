<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ProposalSubmissionController extends Controller
{
    public function store(Proposal $proposal): RedirectResponse
    {
        Gate::authorize('submit', $proposal);

        if (! $proposal->document_path) {
            return back()->withErrors(['document' => 'Unggah dokumen proposal sebelum mengajukan.']);
        }

        $proposal->update([
            'code' => 'UPPM-'.now()->format('Y').'-'.str_pad((string) $proposal->id, 5, '0', STR_PAD_LEFT),
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('proposals.show', $proposal)->with('success', 'Proposal berhasil diajukan dan tidak dapat diedit lagi.');
    }
}
