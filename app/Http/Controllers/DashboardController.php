<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterDashboardRequest;
use App\Models\FinalReport;
use App\Models\FundingContract;
use App\Models\FundingDisbursement;
use App\Models\KpiTarget;
use App\Models\Proposal;
use App\Models\ResearchOutput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(FilterDashboardRequest $request): View
    {
        $year = $request->integer('year', now()->year);
        $type = $request->string('type', 'all')->toString();
        $user = $request->user();
        $isAdmin = $user->hasRole('admin_uppm');
        $proposalQuery = Proposal::query()->whereYear('created_at', $year)
            ->when($type !== 'all', fn (Builder $query) => $query->where('type', $type))
            ->when(! $isAdmin, fn (Builder $query) => $query->whereBelongsTo($user, 'lead'));
        $contractQuery = FundingContract::query()->whereYear('created_at', $year)
            ->when($type !== 'all', fn (Builder $query) => $query->whereHas('proposal', fn (Builder $proposal) => $proposal->where('type', $type)))
            ->when(! $isAdmin, fn (Builder $query) => $query->whereHas('proposal', fn (Builder $proposal) => $proposal->whereBelongsTo($user, 'lead')));
        $outputQuery = ResearchOutput::query()->whereYear('created_at', $year)
            ->when($type !== 'all', fn (Builder $query) => $query->whereHas('contract.proposal', fn (Builder $proposal) => $proposal->where('type', $type)))
            ->when(! $isAdmin, fn (Builder $query) => $query->whereHas('contract.proposal', fn (Builder $proposal) => $proposal->whereBelongsTo($user, 'lead')));

        $paid = FundingDisbursement::query()->where('status', 'paid')->whereYear('paid_at', $year)
            ->when($type !== 'all', fn (Builder $query) => $query->whereHas('contract.proposal', fn (Builder $proposal) => $proposal->where('type', $type)))
            ->when(! $isAdmin, fn (Builder $query) => $query->whereHas('contract.proposal', fn (Builder $proposal) => $proposal->whereBelongsTo($user, 'lead')))->sum('amount');
        $stats = ['proposals' => (clone $proposalQuery)->count(), 'submitted' => (clone $proposalQuery)->whereNot('status', 'draft')->count(),
            'active' => (clone $contractQuery)->whereIn('status', ['signed', 'active'])->count(), 'outputs' => (clone $outputQuery)->where('verification_status', 'verified')->count(),
            'funding' => (clone $contractQuery)->sum('contract_value'), 'paid' => $paid];
        $proposalTypes = ['penelitian' => (clone $proposalQuery)->where('type', 'penelitian')->count(), 'pkm' => (clone $proposalQuery)->where('type', 'pkm')->count()];
        $monthly = (clone $proposalQuery)->get(['created_at'])->countBy(fn (Proposal $proposal) => $proposal->created_at->month);
        $monthlySeries = collect(range(1, 12))->map(fn (int $month): array => ['label' => now()->month($month)->translatedFormat('M'), 'value' => $monthly->get($month, 0)]);
        $outputTypes = (clone $outputQuery)->where('verification_status', 'verified')->selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type');
        $verifiedFinalReports = FinalReport::query()->where('status', 'verified')->whereYear('verified_at', $year)
            ->when($type !== 'all', fn (Builder $query) => $query->whereHas('contract.proposal', fn (Builder $proposal) => $proposal->where('type', $type)))
            ->count();
        $actuals = ['funded_activities' => (clone $contractQuery)->count(), 'verified_outputs' => $stats['outputs'],
            'verified_final_reports' => $verifiedFinalReports, 'paid_funding' => $paid];
        $kpiTargets = $isAdmin ? KpiTarget::query()->where('year', $year)->get()->keyBy('metric') : collect();
        $recentOutputs = (clone $outputQuery)->with(['contract.proposal:id,title', 'creator:id,name'])->latest()->limit(6)->get();
        $years = collect(range(now()->year + 1, now()->year - 5));

        return view('dashboard', compact('year', 'type', 'isAdmin', 'stats', 'proposalTypes', 'monthlySeries', 'outputTypes', 'actuals', 'kpiTargets', 'recentOutputs', 'years'));
    }
}
