<?php

namespace App\Models;

use Database\Factories\ProposalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['grant_period_id', 'research_field_id', 'lead_id', 'code', 'title', 'type', 'abstract', 'background', 'objectives', 'methodology', 'roadmap_alignment', 'partner_name', 'partner_location', 'requested_budget', 'duration_months', 'document_path', 'status', 'submitted_at'])]
class Proposal extends Model
{
    /** @use HasFactory<ProposalFactory> */
    use HasFactory;

    public function grantPeriod(): BelongsTo
    {
        return $this->belongsTo(GrantPeriod::class);
    }

    public function researchField(): BelongsTo
    {
        return $this->belongsTo(ResearchField::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProposalMember::class)->orderBy('sort_order');
    }

    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class);
    }

    public function decision(): HasOne
    {
        return $this->hasOne(ProposalDecision::class);
    }

    public function fundingContract(): HasOne
    {
        return $this->hasOne(FundingContract::class);
    }

    protected function casts(): array
    {
        return [
            'requested_budget' => 'integer',
            'duration_months' => 'integer',
            'submitted_at' => 'datetime',
        ];
    }
}
