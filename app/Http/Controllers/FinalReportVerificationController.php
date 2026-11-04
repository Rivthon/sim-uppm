<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyFinalReportRequest;
use App\Models\FinalReport;
use Illuminate\Http\RedirectResponse;

class FinalReportVerificationController extends Controller
{
    public function update(VerifyFinalReportRequest $request, FinalReport $report): RedirectResponse
    {
        $report->update([
            ...$request->validated(),
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Status laporan akhir berhasil diperbarui.');
    }
}
