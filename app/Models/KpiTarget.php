<?php

namespace App\Models;

use Database\Factories\KpiTargetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['year', 'metric', 'target_value', 'unit', 'created_by'])]
class KpiTarget extends Model
{
    /** @use HasFactory<KpiTargetFactory> */
    use HasFactory;

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function casts(): array
    {
        return ['year' => 'integer', 'target_value' => 'integer'];
    }
}
