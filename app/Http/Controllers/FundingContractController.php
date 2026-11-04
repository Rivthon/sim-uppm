<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFundingContractRequest;
use App\Models\FundingContract;
use App\Models\Proposal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FundingContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', FundingContract::class);
        $contracts = FundingContract::query()
            ->with(['proposal:id,code,title,lead_id', 'proposal.lead:id,name'])
            ->withSum(['disbursements as paid_amount' => fn ($query) => $query->where('status', 'paid')], 'amount')
            ->when(! auth()->user()->hasRole('admin_uppm'), fn ($query) => $query->whereHas('proposal', fn ($proposal) => $proposal->whereBelongsTo(auth()->user(), 'lead')))
            ->latest('id')->paginate(10);

        return view('funding.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', FundingContract::class);
        $proposals = Proposal::query()->with(['lead:id,name', 'decision'])
            ->whereHas('decision', fn ($query) => $query->where('decision', 'accepted'))
            ->whereDoesntHave('fundingContract')->latest('id')->get();

        return view('funding.create', compact('proposals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFundingContractRequest $request): RedirectResponse
    {
        $contract = FundingContract::create([
            ...$request->safe()->except('document'),
            'document_path' => $request->file('document')?->store('contracts'),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('funding.show', $contract)->with('success', 'Kontrak pendanaan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FundingContract $contract): View
    {
        Gate::authorize('view', $contract);
        $contract->load(['proposal.lead', 'proposal.decision', 'disbursements']);

        return view('funding.show', compact('contract'));
    }
}
