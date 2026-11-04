<?php

namespace App\Http\Controllers;

use App\Models\FinalReport;
use App\Models\ResearchOutput;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportingDocumentController extends Controller
{
    public function finalReport(FinalReport $report): StreamedResponse
    {
        Gate::authorize('view', $report);
        abort_unless(Storage::exists($report->document_path), 404);

        return Storage::download($report->document_path);
    }

    public function output(ResearchOutput $output): StreamedResponse
    {
        Gate::authorize('view', $output);
        abort_unless(Storage::exists($output->evidence_path), 404);

        return Storage::download($output->evidence_path);
    }
}
