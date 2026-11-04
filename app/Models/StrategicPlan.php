<?php

namespace App\Models;

use Database\Factories\StrategicPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'scope', 'study_program_id', 'type', 'start_year', 'end_year', 'vision', 'objectives', 'document_path', 'status', 'created_by'])]
class StrategicPlan extends Model
{
    /** @use HasFactory<StrategicPlanFactory> */
    use HasFactory;

    protected $table = 'strategic_roadmaps';

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(RoadmapMilestone::class)->orderBy('sort_order')->orderBy('start_year');
    }

    protected function casts(): array
    {
        return ['start_year' => 'integer', 'end_year' => 'integer'];
    }
}
