<?php

namespace App\Models;

use Database\Factories\FundingContractFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['proposal_id', 'contract_number', 'decree_number', 'signed_at', 'starts_at', 'ends_at', 'contract_value', 'status', 'document_path', 'created_by'])]
class FundingContract extends Model
{
    /** @use HasFactory<FundingContractFactory> */
    use HasFactory;

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function disbursements(): HasMany
    {
        return $this->hasMany(FundingDisbursement::class)->orderBy('sequence');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->orderByDesc('activity_date');
    }

    public function progressReports(): HasMany
    {
        return $this->hasMany(ProgressReport::class)->orderByDesc('reporting_period');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(MonitoringEvaluation::class)->orderByDesc('scheduled_at');
    }

    protected function casts(): array
    {
        return ['signed_at' => 'date', 'starts_at' => 'date', 'ends_at' => 'date', 'contract_value' => 'integer'];
    }
}
