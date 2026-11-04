<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterActivityTypeRequest;
use App\Http\Requests\StoreGrantPeriodRequest;
use App\Http\Requests\UpdateGrantPeriodRequest;
use App\Models\AcademicYear;
use App\Models\GrantPeriod;
use App\Models\GrantScheme;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GrantProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilterActivityTypeRequest $request): View
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);

        $periods = GrantPeriod::query()
            ->with(['academicYear:id,name', 'scheme:id,code,name,type'])
            ->when($request->filled('type'), fn ($query) => $query->whereHas('scheme', fn ($scheme) => $scheme->where('type', $request->string('type'))))
            ->latest('submission_starts_at')
            ->latest('id')
            ->paginate(10);

        return view('grants.index', compact('periods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);

        return view('grants.create', $this->formOptions());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGrantPeriodRequest $request): RedirectResponse
    {
        $period = GrantPeriod::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('grants.edit', $period)->with('success', 'Program hibah berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(GrantPeriod $grant): RedirectResponse
    {
        return redirect()->route('grants.edit', $grant);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GrantPeriod $grant): View
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);

        return view('grants.edit', [...$this->formOptions(), 'grant' => $grant]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGrantPeriodRequest $request, GrantPeriod $grant): RedirectResponse
    {
        $grant->update($request->validated());

        return back()->with('success', 'Program hibah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GrantPeriod $grant): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);
        $grant->delete();

        return redirect()->route('grants.index')->with('success', 'Program hibah berhasil dihapus.');
    }

    /** @return array{academicYears: Collection<int, AcademicYear>, schemes: Collection<int, GrantScheme>} */
    private function formOptions(): array
    {
        return [
            'academicYears' => AcademicYear::query()->orderByDesc('starts_at')->get(['id', 'name']),
            'schemes' => GrantScheme::query()->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name', 'type']),
        ];
    }
}
