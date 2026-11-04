<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ProgressReport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExecutionDocumentController extends Controller
{
    public function activity(ActivityLog $activity): StreamedResponse
    {
        Gate::authorize('viewExecution', $activity->contract);
        abort_unless($activity->attachment_path && Storage::exists($activity->attachment_path), 404);

        return Storage::download($activity->attachment_path);
    }

    public function report(ProgressReport $report): StreamedResponse
    {
        Gate::authorize('viewExecution', $report->contract);
        abort_unless($report->document_path && Storage::exists($report->document_path), 404);

        return Storage::download($report->document_path);
    }
}
