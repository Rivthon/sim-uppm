<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposalRequest;
use App\Http\Requests\UpdateProposalRequest;
use App\Models\GrantPeriod;
use App\Models\Proposal;
use App\Models\ResearchField;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Proposal::class);

        $proposals = Proposal::query()
            ->with(['grantPeriod:id,name', 'researchField:id,name', 'lead:id,name,identity_number'])
            ->withCount('members')
            ->when(! auth()->user()->hasRole('admin_uppm'), fn ($query) => $query->whereBelongsTo(auth()->user(), 'lead'))
            ->latest('id')
            ->paginate(10);

        return view('proposals.index', compact('proposals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Proposal::class);

        return view('proposals.create', $this->formOptions());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProposalRequest $request): RedirectResponse
    {
        $validated = $request->safe()->except(['document', 'members']);
        $period = GrantPeriod::with('scheme:id,type')->findOrFail($validated['grant_period_id']);

        $proposal = DB::transaction(function () use ($request, $validated, $period): Proposal {
            $proposal = Proposal::create([
                ...$validated,
                'lead_id' => $request->user()->id,
                'type' => $period->scheme->type,
                'document_path' => $request->file('document')?->store('proposals'),
                'status' => 'draft',
            ]);

            $this->replaceMembers($proposal, $request->validated('members', []));

            return $proposal;
        });

        return redirect()->route('proposals.edit', $proposal)->with('success', 'Draft proposal berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proposal $proposal): View
    {
        Gate::authorize('view', $proposal);
        $proposal->load(['grantPeriod.scheme', 'researchField', 'lead', 'members', 'decision.decider', 'reviewAssignments.reviewer']);

        return view('proposals.show', compact('proposal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proposal $proposal): View
    {
        Gate::authorize('update', $proposal);
        $proposal->load('members');

        return view('proposals.edit', [...$this->formOptions(), 'proposal' => $proposal]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProposalRequest $request, Proposal $proposal): RedirectResponse
    {
        $validated = $request->safe()->except(['document', 'members']);
        $period = GrantPeriod::with('scheme:id,type')->findOrFail($validated['grant_period_id']);
        $oldDocument = $proposal->document_path;

        DB::transaction(function () use ($request, $proposal, $validated, $period): void {
            $proposal->update([
                ...$validated,
                'type' => $period->scheme->type,
                'document_path' => $request->file('document')?->store('proposals') ?? $proposal->document_path,
            ]);
            $this->replaceMembers($proposal, $request->validated('members', []));
        });

        if ($request->hasFile('document') && $oldDocument) {
            Storage::delete($oldDocument);
        }

        return back()->with('success', 'Draft proposal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposal $proposal): RedirectResponse
    {
        Gate::authorize('delete', $proposal);
        $document = $proposal->document_path;
        $proposal->delete();

        if ($document) {
            Storage::delete($document);
        }

        return redirect()->route('proposals.index')->with('success', 'Draft proposal berhasil dihapus.');
    }

    /** @return array{grantPeriods: Collection<int, GrantPeriod>, researchFields: Collection<int, ResearchField>} */
    private function formOptions(): array
    {
        return [
            'grantPeriods' => GrantPeriod::query()->with('scheme:id,name,type,maximum_funding')->where('status', 'open')->orderBy('submission_ends_at')->get(),
            'researchFields' => ResearchField::query()->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']),
        ];
    }

    /** @param array<int, array<string, mixed>> $members */
    private function replaceMembers(Proposal $proposal, array $members): void
    {
        $proposal->members()->delete();
        $proposal->members()->createMany(collect($members)
            ->filter(fn (array $member): bool => filled($member['name'] ?? null))
            ->values()
            ->map(fn (array $member, int $index): array => [...$member, 'sort_order' => $index])
            ->all());
    }
}
