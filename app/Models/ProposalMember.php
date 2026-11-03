<?php

namespace App\Models;

use Database\Factories\ProposalMemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['proposal_id', 'name', 'identity_number', 'member_type', 'study_program', 'sort_order'])]
class ProposalMember extends Model
{
    /** @use HasFactory<ProposalMemberFactory> */
    use HasFactory;

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
