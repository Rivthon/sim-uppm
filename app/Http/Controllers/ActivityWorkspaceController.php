<?php

namespace App\Http\Controllers;

use App\Models\FundingContract;
use App\Models\Proposal;
use App\Models\ResearchOutput;
use App\Models\StrategicPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class ActivityWorkspaceController extends Controller
{
    public function __invoke(string $type): View
    {
        abort_unless(in_array($type, ['penelitian', 'pkm'], true), 404);
        $user = auth()->user();
        $proposalQuery = Proposal::query()->where('type', $type)
            ->accessibleTo($user);
        $contractQuery = FundingContract::query()->whereHas('proposal', fn (Builder $query) => $query->where('type', $type))
            ->when(! $user->hasRole('admin_uppm'), fn (Builder $query) => $query->whereHas('proposal', fn (Builder $proposal) => $proposal->accessibleTo($user)));
        $stats = [
            'proposals' => (clone $proposalQuery)->count(),
            'submitted' => (clone $proposalQuery)->whereNot('status', 'draft')->count(),
            'active' => (clone $contractQuery)->whereIn('status', ['signed', 'active'])->count(),
            'outputs' => ResearchOutput::query()->where('verification_status', 'verified')->whereHas('contract.proposal', function (Builder $query) use ($type, $user): void {
                $query->where('type', $type)->when(! $user->hasRole('admin_uppm'), fn (Builder $proposal) => $proposal->accessibleTo($user));
            })->count(),
            'roadmaps' => StrategicPlan::query()->where('status', 'active')->whereIn('type', [$type, 'integrated'])->count(),
        ];
        $recentProposals = (clone $proposalQuery)->with('lead:id,name')->latest()->limit(5)->get();

        return view('workspaces.show', compact('type', 'stats', 'recentProposals'));
    }
}
