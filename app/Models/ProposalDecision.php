<?php

namespace App\Models;

use Database\Factories\ProposalDecisionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['proposal_id', 'decision', 'approved_budget', 'notes', 'decided_by', 'decided_at'])]
class ProposalDecision extends Model
{
    /** @use HasFactory<ProposalDecisionFactory> */
    use HasFactory;

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    protected function casts(): array
    {
        return ['approved_budget' => 'integer', 'decided_at' => 'datetime'];
    }
}
