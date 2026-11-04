<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class IncomingProposalController extends Controller
{
    public function __invoke(): View
    {
        Gate::authorize('viewAny', Proposal::class);
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);

        $proposals = Proposal::query()
            ->with(['lead:id,name,identity_number', 'grantPeriod:id,name', 'researchField:id,name'])
            ->withCount(['members', 'reviewAssignments'])
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->latest('id')
            ->paginate(12);

        return view('proposals.incoming', compact('proposals'));
    }
}
