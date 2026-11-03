<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\GrantScheme;
use App\Models\ResearchField;
use App\Models\StudyProgram;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    public function __invoke(): View
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);

        return view('master-data.index', [
            'studyPrograms' => StudyProgram::query()->orderBy('name')->get(),
            'academicYears' => AcademicYear::query()->orderByDesc('starts_at')->get(),
            'researchFields' => ResearchField::query()->orderBy('name')->get(),
            'schemes' => GrantScheme::query()->orderBy('type')->orderBy('name')->get(),
        ]);
    }
}
