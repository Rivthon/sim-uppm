<?php

namespace App\Models;

use Database\Factories\ResearchOutputFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['funding_contract_id', 'type', 'title', 'achievement_status', 'identifier', 'publisher', 'publication_date', 'url', 'evidence_path', 'notes', 'verification_status', 'verification_notes', 'created_by', 'verified_by', 'verified_at'])]
class ResearchOutput extends Model
{
    /** @use HasFactory<ResearchOutputFactory> */
    use HasFactory;

    public function contract(): BelongsTo
    {
        return $this->belongsTo(FundingContract::class, 'funding_contract_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    protected function casts(): array
    {
        return ['publication_date' => 'date', 'verified_at' => 'datetime'];
    }
}
