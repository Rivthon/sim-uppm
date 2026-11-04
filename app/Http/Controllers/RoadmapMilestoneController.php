<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveRoadmapMilestoneRequest;
use App\Models\RoadmapMilestone;
use App\Models\StrategicPlan;
use Illuminate\Http\RedirectResponse;

class RoadmapMilestoneController extends Controller
{
    public function store(SaveRoadmapMilestoneRequest $request, StrategicPlan $strategicPlan): RedirectResponse
    {
        $strategicPlan->milestones()->create([...$request->validated(), 'created_by' => $request->user()->id]);

        return back()->with('success', 'Tahapan roadmap berhasil ditambahkan.');
    }

    public function update(SaveRoadmapMilestoneRequest $request, RoadmapMilestone $milestone): RedirectResponse
    {
        $milestone->update($request->validated());

        return back()->with('success', 'Tahapan roadmap berhasil diperbarui.');
    }

    public function destroy(RoadmapMilestone $milestone): RedirectResponse
    {
        abort_unless(auth()->user()->can('update', $milestone->strategicPlan), 403);
        $milestone->delete();

        return back()->with('success', 'Tahapan roadmap berhasil dihapus.');
    }
}
