<?php

namespace App\Models;

use Database\Factories\FundingDisbursementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['funding_contract_id', 'sequence', 'label', 'percentage', 'amount', 'scheduled_at', 'paid_at', 'status', 'proof_path', 'notes', 'recorded_by'])]
class FundingDisbursement extends Model
{
    /** @use HasFactory<FundingDisbursementFactory> */
    use HasFactory;

    public function contract(): BelongsTo
    {
        return $this->belongsTo(FundingContract::class, 'funding_contract_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    protected function casts(): array
    {
        return ['percentage' => 'integer', 'amount' => 'integer', 'scheduled_at' => 'date', 'paid_at' => 'date'];
    }
}
