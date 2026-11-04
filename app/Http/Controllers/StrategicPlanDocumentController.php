<?php

namespace App\Http\Controllers;

use App\Models\StrategicPlan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StrategicPlanDocumentController extends Controller
{
    public function __invoke(StrategicPlan $strategicPlan): StreamedResponse
    {
        Gate::authorize('view', $strategicPlan);
        abort_unless($strategicPlan->document_path && Storage::exists($strategicPlan->document_path), 404);

        return Storage::download($strategicPlan->document_path);
    }
}
