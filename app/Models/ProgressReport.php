<?php

namespace App\Models;

use Database\Factories\ProgressReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['funding_contract_id', 'reporting_period', 'progress_percentage', 'summary', 'achievements', 'obstacles', 'budget_realized', 'output_progress', 'document_path', 'status', 'submitted_at', 'created_by'])]
class ProgressReport extends Model
{
    /** @use HasFactory<ProgressReportFactory> */
    use HasFactory;

    public function contract(): BelongsTo
    {
        return $this->belongsTo(FundingContract::class, 'funding_contract_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(MonitoringEvaluation::class);
    }

    protected function casts(): array
    {
        return ['reporting_period' => 'date', 'progress_percentage' => 'integer', 'budget_realized' => 'integer', 'submitted_at' => 'datetime'];
    }
}
