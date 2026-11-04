<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveStudyProgramRequest;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;

class StudyProgramController extends Controller
{
    public function store(SaveStudyProgramRequest $request): RedirectResponse
    {
        StudyProgram::query()->create($request->validated());

        return back()->with('success', 'Program studi berhasil ditambahkan.');
    }

    public function update(SaveStudyProgramRequest $request, StudyProgram $studyProgram): RedirectResponse
    {
        $studyProgram->update($request->validated());

        return back()->with('success', 'Program studi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudyProgram $studyProgram): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);
        $studyProgram->delete();

        return back()->with('success', 'Program studi berhasil dihapus.');
    }
}
