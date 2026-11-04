<?php

namespace App\Models;

use Database\Factories\FinalReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['funding_contract_id', 'summary', 'conclusions', 'recommendations', 'budget_realized', 'document_path', 'status', 'verification_notes', 'submitted_at', 'submitted_by', 'verified_by', 'verified_at'])]
class FinalReport extends Model
{
    /** @use HasFactory<FinalReportFactory> */
    use HasFactory;

    public function contract(): BelongsTo
    {
        return $this->belongsTo(FundingContract::class, 'funding_contract_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    protected function casts(): array
    {
        return ['budget_realized' => 'integer', 'submitted_at' => 'datetime', 'verified_at' => 'datetime'];
    }
}
