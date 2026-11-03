<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProposalDocumentController extends Controller
{
    public function __invoke(Proposal $proposal): StreamedResponse
    {
        Gate::authorize('view', $proposal);
        abort_unless($proposal->document_path && Storage::exists($proposal->document_path), 404);

        return Storage::download($proposal->document_path, 'proposal-'.$proposal->id.'.'.pathinfo($proposal->document_path, PATHINFO_EXTENSION));
    }
}
