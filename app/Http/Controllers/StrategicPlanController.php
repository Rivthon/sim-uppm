<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStrategicPlanRequest;
use App\Http\Requests\UpdateStrategicPlanRequest;
use App\Models\StrategicPlan;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StrategicPlanController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', StrategicPlan::class);
        $plans = StrategicPlan::query()->with(['studyProgram:id,code,name'])->withCount('milestones')
            ->when(! auth()->user()->hasRole('admin_uppm'), fn ($query) => $query->where('status', 'active'))
            ->orderByDesc('start_year')->paginate(10);

        return view('strategic-plans.index', compact('plans'));
    }

    public function create(): View
    {
        Gate::authorize('create', StrategicPlan::class);

        return view('strategic-plans.create', ['studyPrograms' => StudyProgram::query()->where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(StoreStrategicPlanRequest $request): RedirectResponse
    {
        $plan = StrategicPlan::query()->create([
            ...$request->safe()->except('document'),
            'study_program_id' => $request->input('scope') === 'study_program' ? $request->integer('study_program_id') : null,
            'document_path' => $request->file('document')?->store('strategic-plans'),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('strategic-plans.show', $plan)->with('success', 'Renstra berhasil dibuat.');
    }

    public function show(StrategicPlan $strategicPlan): View
    {
        Gate::authorize('view', $strategicPlan);
        $strategicPlan->load(['studyProgram', 'milestones']);

        return view('strategic-plans.show', compact('strategicPlan'));
    }

    public function edit(StrategicPlan $strategicPlan): View
    {
        Gate::authorize('update', $strategicPlan);

        return view('strategic-plans.edit', ['strategicPlan' => $strategicPlan, 'studyPrograms' => StudyProgram::query()->where('is_active', true)->orderBy('name')->get()]);
    }

    public function update(UpdateStrategicPlanRequest $request, StrategicPlan $strategicPlan): RedirectResponse
    {
        $data = [...$request->safe()->except('document'), 'study_program_id' => $request->input('scope') === 'study_program' ? $request->integer('study_program_id') : null];
        if ($request->hasFile('document')) {
            if ($strategicPlan->document_path) {
                Storage::delete($strategicPlan->document_path);
            }
            $data['document_path'] = $request->file('document')->store('strategic-plans');
        }
        $strategicPlan->update($data);

        return redirect()->route('strategic-plans.show', $strategicPlan)->with('success', 'Renstra berhasil diperbarui.');
    }

    public function destroy(StrategicPlan $strategicPlan): RedirectResponse
    {
        Gate::authorize('delete', $strategicPlan);
        if ($strategicPlan->document_path) {
            Storage::delete($strategicPlan->document_path);
        }
        $strategicPlan->delete();

        return redirect()->route('strategic-plans.index')->with('success', 'Renstra dan roadmap berhasil dihapus.');
    }
}
