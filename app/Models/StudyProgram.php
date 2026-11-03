<?php

namespace App\Models;

use Database\Factories\StudyProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'name', 'faculty', 'level', 'is_active'])]
class StudyProgram extends Model
{
    /** @use HasFactory<StudyProgramFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
