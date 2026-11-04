<?php

namespace App\Models;

use Database\Factories\RoadmapMilestoneFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['strategic_plan_id', 'start_year', 'end_year', 'theme', 'focus', 'indicators', 'target_outputs', 'sort_order', 'created_by'])]
class RoadmapMilestone extends Model
{
    /** @use HasFactory<RoadmapMilestoneFactory> */
    use HasFactory;

    public function strategicPlan(): BelongsTo
    {
        return $this->belongsTo(StrategicPlan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function casts(): array
    {
        return ['start_year' => 'integer', 'end_year' => 'integer', 'sort_order' => 'integer'];
    }
}
