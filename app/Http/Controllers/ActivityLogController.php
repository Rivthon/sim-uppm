<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityLogRequest;
use App\Models\FundingContract;
use Illuminate\Http\RedirectResponse;

class ActivityLogController extends Controller
{
    public function store(StoreActivityLogRequest $request, FundingContract $contract): RedirectResponse
    {
        $contract->activityLogs()->create([
            ...$request->safe()->except('attachment'),
            'attachment_path' => $request->file('attachment')?->store('activity-logs'),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Catatan kegiatan berhasil ditambahkan.');
    }
}
