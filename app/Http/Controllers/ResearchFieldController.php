<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveResearchFieldRequest;
use App\Models\ResearchField;
use Illuminate\Http\RedirectResponse;

class ResearchFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveResearchFieldRequest $request): RedirectResponse
    {
        ResearchField::query()->create($request->validated());

        return back()->with('success', 'Bidang ilmu berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveResearchFieldRequest $request, ResearchField $researchField): RedirectResponse
    {
        $researchField->update($request->validated());

        return back()->with('success', 'Bidang ilmu berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResearchField $researchField): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);
        if ($researchField->proposals()->exists()) {
            return back()->withErrors(['delete' => 'Bidang ilmu sudah dipakai proposal dan tidak dapat dihapus.']);
        }
        $researchField->delete();

        return back()->with('success', 'Bidang ilmu berhasil dihapus.');
    }
}
