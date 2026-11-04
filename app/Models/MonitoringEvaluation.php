<?php

namespace App\Models;

use Database\Factories\MonitoringEvaluationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['funding_contract_id', 'progress_report_id', 'evaluator_id', 'scheduled_at', 'mode', 'score', 'findings', 'recommendations', 'follow_up', 'status', 'completed_at', 'created_by'])]
class MonitoringEvaluation extends Model
{
    /** @use HasFactory<MonitoringEvaluationFactory> */
    use HasFactory;

    public function contract(): BelongsTo
    {
        return $this->belongsTo(FundingContract::class, 'funding_contract_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(ProgressReport::class, 'progress_report_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime', 'score' => 'integer', 'completed_at' => 'datetime'];
    }
}
