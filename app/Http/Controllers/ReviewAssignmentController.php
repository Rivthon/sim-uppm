<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterActivityTypeRequest;
use App\Http\Requests\StoreReviewAssignmentRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Proposal;
use App\Models\ReviewAssignment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReviewAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilterActivityTypeRequest $request): View
    {
        Gate::authorize('viewAny', ReviewAssignment::class);

        $assignments = ReviewAssignment::query()
            ->with(['proposal:id,code,title,type,lead_id', 'proposal.lead:id,name', 'reviewer:id,name,identity_number'])
            ->when($request->filled('type'), fn ($query) => $query->whereHas('proposal', fn ($proposal) => $proposal->where('type', $request->string('type'))))
            ->when(! auth()->user()->hasRole('admin_uppm'), fn ($query) => $query->whereBelongsTo(auth()->user(), 'reviewer'))
            ->orderByRaw("FIELD(status, 'assigned', 'in_review', 'submitted', 'conflict')")
            ->orderBy('due_at')
            ->paginate(10);

        return view('reviews.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(FilterActivityTypeRequest $request): View
    {
        Gate::authorize('create', ReviewAssignment::class);

        $proposals = Proposal::query()
            ->with('lead:id,name')
            ->where('status', 'submitted')
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->latest('submitted_at')
            ->get(['id', 'code', 'title', 'lead_id']);
        $reviewers = User::query()->where('is_active', true)->orderBy('name')->get()
            ->filter(fn (User $user): bool => $user->hasRole('reviewer'));

        return view('reviews.create', compact('proposals', 'reviewers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewAssignmentRequest $request): RedirectResponse
    {
        $assignment = ReviewAssignment::create([
            ...$request->validated(),
            'assigned_by' => $request->user()->id,
            'status' => 'assigned',
        ]);

        return redirect()->route('reviews.show', $assignment)->with('success', 'Reviewer berhasil ditugaskan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReviewAssignment $assignment): View
    {
        Gate::authorize('view', $assignment);
        $assignment->load(['proposal.grantPeriod', 'proposal.researchField', 'proposal.lead', 'reviewer', 'assigner']);

        return view('reviews.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReviewAssignment $assignment): View
    {
        Gate::authorize('update', $assignment);
        $assignment->load(['proposal.grantPeriod', 'proposal.researchField', 'proposal.lead']);

        return view('reviews.edit', compact('assignment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, ReviewAssignment $assignment): RedirectResponse
    {
        $assignment->update([
            ...$request->validated(),
            'has_conflict' => $request->boolean('has_conflict'),
            'status' => 'in_review',
        ]);

        return back()->with('success', 'Draft penilaian berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReviewAssignment $assignment): RedirectResponse
    {
        Gate::authorize('delete', $assignment);
        $assignment->delete();

        return redirect()->route('reviews.index')->with('success', 'Penugasan reviewer berhasil dibatalkan.');
    }
}
