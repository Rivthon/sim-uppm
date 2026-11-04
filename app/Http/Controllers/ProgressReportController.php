<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgressReportRequest;
use App\Models\FundingContract;
use Illuminate\Http\RedirectResponse;

class ProgressReportController extends Controller
{
    public function store(StoreProgressReportRequest $request, FundingContract $contract): RedirectResponse
    {
        $contract->progressReports()->create([
            ...$request->safe()->except('document'),
            'document_path' => $request->file('document')->store('progress-reports'),
            'status' => 'submitted',
            'submitted_at' => now(),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Laporan kemajuan berhasil dikirim.');
    }
}
