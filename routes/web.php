<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AccreditationController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ActivityWorkspaceController;
use App\Http\Controllers\Auth\PasLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExecutionController;
use App\Http\Controllers\ExecutionDocumentController;
use App\Http\Controllers\FinalReportController;
use App\Http\Controllers\FinalReportVerificationController;
use App\Http\Controllers\FundingContractController;
use App\Http\Controllers\FundingDisbursementController;
use App\Http\Controllers\FundingDocumentController;
use App\Http\Controllers\GrantProgramController;
use App\Http\Controllers\GrantSchemeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncomingProposalController;
use App\Http\Controllers\KpiTargetController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\MonitoringEvaluationController;
use App\Http\Controllers\OutputVerificationController;
use App\Http\Controllers\ProgressReportController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ProposalDecisionController;
use App\Http\Controllers\ProposalDocumentController;
use App\Http\Controllers\ProposalSubmissionController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\ReportingDocumentController;
use App\Http\Controllers\ResearchFieldController;
use App\Http\Controllers\ResearchOutputController;
use App\Http\Controllers\ReviewAssignmentController;
use App\Http\Controllers\ReviewSubmissionController;
use App\Http\Controllers\RoadmapMilestoneController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\StrategicPlanController;
use App\Http\Controllers\StrategicPlanDocumentController;
use App\Http\Controllers\StudyProgramController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/informasi/{public_page:slug}', [PublicPageController::class, 'show'])->name('public-pages.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [PasLoginController::class, 'create'])->name('login');
    Route::post('/login', [PasLoginController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');
});

Route::middleware(['auth', 'authorized.pas'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/pengaturan-website', [SiteSettingController::class, 'edit'])->name('site-settings.edit');
    Route::put('/pengaturan-website', [SiteSettingController::class, 'update'])->name('site-settings.update');
    Route::resource('/pengaturan-website/halaman', PublicPageController::class)
        ->except('show')->parameters(['halaman' => 'public_page'])->names('public-pages');
    Route::get('/ruang-kerja/{type}', ActivityWorkspaceController::class)->whereIn('type', ['penelitian', 'pkm'])->name('workspaces.show');
    Route::post('/dashboard/kpi', [KpiTargetController::class, 'store'])->name('dashboard.kpi.store');
    Route::get('/rekap-akreditasi', [AccreditationController::class, 'index'])->name('accreditation.index');
    Route::get('/rekap-akreditasi/export', [AccreditationController::class, 'export'])->name('accreditation.export');
    Route::get('/master-data', MasterDataController::class)->name('master-data.index');
    Route::resource('/renstra-roadmap', StrategicPlanController::class)->parameters(['renstra-roadmap' => 'strategic_plan'])->names('strategic-plans');
    Route::post('/renstra-roadmap/{strategic_plan}/milestone', [RoadmapMilestoneController::class, 'store'])->name('strategic-plans.milestones.store');
    Route::put('/roadmap-milestone/{milestone}', [RoadmapMilestoneController::class, 'update'])->name('strategic-plans.milestones.update');
    Route::delete('/roadmap-milestone/{milestone}', [RoadmapMilestoneController::class, 'destroy'])->name('strategic-plans.milestones.destroy');
    Route::get('/renstra-roadmap/{strategic_plan}/dokumen', StrategicPlanDocumentController::class)->name('strategic-plans.document');
    Route::resource('/master-data/program-studi', StudyProgramController::class)->only(['store', 'update', 'destroy'])->parameters(['program-studi' => 'study_program'])->names('master-data.study-programs');
    Route::resource('/master-data/tahun-akademik', AcademicYearController::class)->only(['store', 'update', 'destroy'])->parameters(['tahun-akademik' => 'academic_year'])->names('master-data.academic-years');
    Route::resource('/master-data/bidang-ilmu', ResearchFieldController::class)->only(['store', 'update', 'destroy'])->parameters(['bidang-ilmu' => 'research_field'])->names('master-data.research-fields');
    Route::resource('/master-data/skema-hibah', GrantSchemeController::class)->only(['store', 'update', 'destroy'])->parameters(['skema-hibah' => 'grant_scheme'])->names('master-data.grant-schemes');
    Route::resource('/program-hibah', GrantProgramController::class)->parameters(['program-hibah' => 'grant'])->names('grants');
    Route::resource('/proposal', ProposalController::class)->parameters(['proposal' => 'proposal'])->names('proposals');
    Route::get('/pengajuan-masuk', IncomingProposalController::class)->name('incoming-proposals.index');
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
    Route::get('/laporan-luaran', [FinalReportController::class, 'index'])->name('reporting.index');
    Route::get('/laporan-luaran/{contract}', [FinalReportController::class, 'show'])->name('reporting.show');
    Route::post('/laporan-luaran/{contract}/laporan-akhir', [FinalReportController::class, 'store'])->name('reporting.final-reports.store');
    Route::put('/laporan-akhir/{report}/verifikasi', [FinalReportVerificationController::class, 'update'])->name('reporting.final-reports.verify');
    Route::post('/laporan-luaran/{contract}/luaran', [ResearchOutputController::class, 'store'])->name('reporting.outputs.store');
    Route::put('/luaran/{output}/verifikasi', [OutputVerificationController::class, 'update'])->name('reporting.outputs.verify');
    Route::get('/laporan-akhir/{report}/dokumen', [ReportingDocumentController::class, 'finalReport'])->name('reporting.documents.final-report');
    Route::get('/luaran/{output}/bukti', [ReportingDocumentController::class, 'output'])->name('reporting.documents.output');
    Route::post('/logout', [PasLoginController::class, 'destroy'])->name('logout');
});
