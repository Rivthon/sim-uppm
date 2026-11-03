<?php

use App\Http\Controllers\Auth\PasLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GrantProgramController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ProposalDocumentController;
use App\Http\Controllers\ProposalSubmissionController;
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
    Route::post('/logout', [PasLoginController::class, 'destroy'])->name('logout');
});
