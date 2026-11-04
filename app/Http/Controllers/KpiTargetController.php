<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveKpiTargetRequest;
use App\Models\KpiTarget;
use Illuminate\Http\RedirectResponse;

class KpiTargetController extends Controller
{
    public function store(SaveKpiTargetRequest $request): RedirectResponse
    {
        KpiTarget::query()->updateOrCreate(
            $request->safe()->only(['year', 'metric']),
            [...$request->validated(), 'created_by' => $request->user()->id],
        );

        return back()->with('success', 'Target KPI berhasil disimpan.');
    }
}
