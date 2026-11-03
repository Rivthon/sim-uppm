<?php

namespace App\Models;

use Database\Factories\GrantPeriodFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['academic_year_id', 'grant_scheme_id', 'name', 'submission_starts_at', 'submission_ends_at', 'quota', 'budget', 'requirements', 'status', 'created_by'])]
class GrantPeriod extends Model
{
    /** @use HasFactory<GrantPeriodFactory> */
    use HasFactory;

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(GrantScheme::class, 'grant_scheme_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function casts(): array
    {
        return [
            'submission_starts_at' => 'datetime',
            'submission_ends_at' => 'datetime',
            'quota' => 'integer',
            'budget' => 'integer',
        ];
    }
}
