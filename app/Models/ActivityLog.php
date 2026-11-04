<?php

namespace App\Models;

use Database\Factories\ActivityLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['funding_contract_id', 'activity_date', 'description', 'result', 'progress_percentage', 'expense_amount', 'attachment_path', 'created_by'])]
class ActivityLog extends Model
{
    /** @use HasFactory<ActivityLogFactory> */
    use HasFactory;

    public function contract(): BelongsTo
    {
        return $this->belongsTo(FundingContract::class, 'funding_contract_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function casts(): array
    {
        return ['activity_date' => 'date', 'progress_percentage' => 'integer', 'expense_amount' => 'integer'];
    }
}
