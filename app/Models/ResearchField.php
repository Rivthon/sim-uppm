<?php

namespace App\Models;

use Database\Factories\ResearchFieldFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'description', 'is_active'])]
class ResearchField extends Model
{
    /** @use HasFactory<ResearchFieldFactory> */
    use HasFactory;

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
