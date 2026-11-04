<?php

namespace App\Http\Controllers;

use App\Models\FundingContract;
use App\Models\FundingDisbursement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FundingDocumentController extends Controller
{
    public function contract(FundingContract $contract): StreamedResponse
    {
        Gate::authorize('view', $contract);
        abort_unless($contract->document_path && Storage::exists($contract->document_path), 404);

        return Storage::download($contract->document_path, 'kontrak-'.$contract->contract_number.'.pdf');
    }

    public function proof(FundingDisbursement $disbursement): StreamedResponse
    {
        Gate::authorize('view', $disbursement->contract);
        abort_unless($disbursement->proof_path && Storage::exists($disbursement->proof_path), 404);

        return Storage::download($disbursement->proof_path, 'bukti-pencairan-'.$disbursement->id.'.'.pathinfo($disbursement->proof_path, PATHINFO_EXTENSION));
    }
}
