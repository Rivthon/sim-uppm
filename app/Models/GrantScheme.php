<?php

namespace App\Models;

use Database\Factories\GrantSchemeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'type', 'description', 'maximum_funding', 'duration_months', 'is_active'])]
class GrantScheme extends Model
{
    /** @use HasFactory<GrantSchemeFactory> */
    use HasFactory;

    public function periods(): HasMany
    {
        return $this->hasMany(GrantPeriod::class);
    }

    protected function casts(): array
    {
        return ['maximum_funding' => 'integer', 'duration_months' => 'integer', 'is_active' => 'boolean'];
    }
}
