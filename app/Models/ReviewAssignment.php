<?php

namespace App\Models;

use Database\Factories\ReviewAssignmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['proposal_id', 'reviewer_id', 'assigned_by', 'due_at', 'status', 'relevance_score', 'methodology_score', 'feasibility_score', 'budget_score', 'output_score', 'strengths', 'weaknesses', 'comments', 'recommendation', 'has_conflict', 'conflict_notes', 'submitted_at'])]
class ReviewAssignment extends Model
{
    /** @use HasFactory<ReviewAssignmentFactory> */
    use HasFactory;

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function totalScore(): ?int
    {
        $scores = [$this->relevance_score, $this->methodology_score, $this->feasibility_score, $this->budget_score, $this->output_score];

        return in_array(null, $scores, true) ? null : array_sum($scores);
    }

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'has_conflict' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }
}
