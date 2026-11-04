<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveAcademicYearRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            if ($request->boolean('is_active')) {
                AcademicYear::query()->update(['is_active' => false]);
            }
            AcademicYear::query()->create($request->validated());
        });

        return back()->with('success', 'Tahun akademik berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveAcademicYearRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        DB::transaction(function () use ($request, $academicYear): void {
            if ($request->boolean('is_active')) {
                AcademicYear::query()->whereKeyNot($academicYear->id)->update(['is_active' => false]);
            }
            $academicYear->update($request->validated());
        });

        return back()->with('success', 'Tahun akademik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);
        if ($academicYear->grantPeriods()->exists()) {
            return back()->withErrors(['delete' => 'Tahun akademik sudah dipakai pada periode hibah dan tidak dapat dihapus.']);
        }
        $academicYear->delete();

        return back()->with('success', 'Tahun akademik berhasil dihapus.');
    }
}
