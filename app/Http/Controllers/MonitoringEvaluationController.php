<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMonitoringEvaluationRequest;
use App\Http\Requests\UpdateMonitoringEvaluationRequest;
use App\Models\FundingContract;
use App\Models\MonitoringEvaluation;
use Illuminate\Http\RedirectResponse;

class MonitoringEvaluationController extends Controller
{
    public function store(StoreMonitoringEvaluationRequest $request, FundingContract $contract): RedirectResponse
    {
        $contract->evaluations()->create([
            ...$request->validated(),
            'status' => 'scheduled',
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Monitoring dan evaluasi berhasil dijadwalkan.');
    }

    public function update(UpdateMonitoringEvaluationRequest $request, MonitoringEvaluation $evaluation): RedirectResponse
    {
        $evaluation->update([
            ...$request->validated(),
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Hasil monitoring dan evaluasi berhasil disimpan.');
    }
}
