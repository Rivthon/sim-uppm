<?php

namespace App\Http\Controllers;

use App\Models\FundingContract;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ExecutionController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $contracts = FundingContract::query()
            ->with(['proposal:id,code,title,lead_id', 'proposal.lead:id,name'])
            ->withCount(['activityLogs', 'progressReports', 'evaluations'])
            ->when(! $user->hasRole('admin_uppm'), function ($query) use ($user): void {
                $query->where(function ($query) use ($user): void {
                    $query->whereHas('proposal', fn ($proposal) => $proposal->whereBelongsTo($user, 'lead'))
                        ->orWhereHas('evaluations', fn ($evaluation) => $evaluation->whereBelongsTo($user, 'evaluator'));
                });
            })
            ->latest('id')->paginate(10);

        return view('execution.index', compact('contracts'));
    }

    public function show(FundingContract $contract): View
    {
        Gate::authorize('viewExecution', $contract);
        $contract->load(['proposal.lead', 'activityLogs.creator', 'progressReports', 'evaluations.evaluator']);

        $evaluators = collect();
        if (auth()->user()->hasRole('admin_uppm')) {
            $evaluators = User::query()->where('is_active', true)->orderBy('name')->get()
                ->filter(fn ($user): bool => $user->hasRole('reviewer') || $user->hasRole('admin_uppm'));
        }

        return view('execution.show', compact('contract', 'evaluators'));
    }
}
