<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\PasLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExecutionController;
use App\Http\Controllers\ExecutionDocumentController;
use App\Http\Controllers\FundingContractController;
use App\Http\Controllers\FundingDisbursementController;
use App\Http\Controllers\FundingDocumentController;
use App\Http\Controllers\GrantProgramController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\MonitoringEvaluationController;
use App\Http\Controllers\ProgressReportController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ProposalDecisionController;
use App\Http\Controllers\ProposalDocumentController;
use App\Http\Controllers\ProposalSubmissionController;
use App\Http\Controllers\ReviewAssignmentController;
use App\Http\Controllers\ReviewSubmissionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [PasLoginController::class, 'create'])->name('login');
    Route::post('/login', [PasLoginController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/master-data', MasterDataController::class)->name('master-data.index');
    Route::resource('/program-hibah', GrantProgramController::class)->parameters(['program-hibah' => 'grant'])->names('grants');
    Route::resource('/proposal', ProposalController::class)->parameters(['proposal' => 'proposal'])->names('proposals');
    Route::post('/proposal/{proposal}/pengajuan', [ProposalSubmissionController::class, 'store'])->name('proposals.submissions.store');
    Route::get('/proposal/{proposal}/dokumen', ProposalDocumentController::class)->name('proposals.documents.show');
    Route::resource('/review', ReviewAssignmentController::class)->parameters(['review' => 'assignment'])->names('reviews');
    Route::post('/review/{assignment}/pengajuan', [ReviewSubmissionController::class, 'store'])->name('reviews.submissions.store');
    Route::post('/proposal/{proposal}/keputusan', [ProposalDecisionController::class, 'store'])->name('proposals.decisions.store');
    Route::resource('/pendanaan', FundingContractController::class)->only(['index', 'create', 'store', 'show'])->parameters(['pendanaan' => 'contract'])->names('funding');
    Route::post('/pendanaan/{contract}/pencairan', [FundingDisbursementController::class, 'store'])->name('funding.disbursements.store');
    Route::get('/pendanaan/{contract}/dokumen', [FundingDocumentController::class, 'contract'])->name('funding.documents.contract');
    Route::get('/pencairan/{disbursement}/bukti', [FundingDocumentController::class, 'proof'])->name('funding.documents.proof');
    Route::get('/pelaksanaan', [ExecutionController::class, 'index'])->name('execution.index');
    Route::get('/pelaksanaan/{contract}', [ExecutionController::class, 'show'])->name('execution.show');
    Route::post('/pelaksanaan/{contract}/logbook', [ActivityLogController::class, 'store'])->name('execution.activities.store');
    Route::post('/pelaksanaan/{contract}/laporan-kemajuan', [ProgressReportController::class, 'store'])->name('execution.reports.store');
    Route::post('/pelaksanaan/{contract}/monev', [MonitoringEvaluationController::class, 'store'])->name('execution.evaluations.store');
    Route::put('/monev/{evaluation}', [MonitoringEvaluationController::class, 'update'])->name('execution.evaluations.update');
    Route::get('/logbook/{activity}/lampiran', [ExecutionDocumentController::class, 'activity'])->name('execution.documents.activity');
    Route::get('/laporan-kemajuan/{report}/dokumen', [ExecutionDocumentController::class, 'report'])->name('execution.documents.report');
    Route::post('/logout', [PasLoginController::class, 'destroy'])->name('logout');
});
