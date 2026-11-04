<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveGrantSchemeRequest;
use App\Models\GrantScheme;
use Illuminate\Http\RedirectResponse;

class GrantSchemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveGrantSchemeRequest $request): RedirectResponse
    {
        GrantScheme::query()->create($request->validated());

        return back()->with('success', 'Skema hibah berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveGrantSchemeRequest $request, GrantScheme $grantScheme): RedirectResponse
    {
        $grantScheme->update($request->validated());

        return back()->with('success', 'Skema hibah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GrantScheme $grantScheme): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);
        if ($grantScheme->periods()->exists()) {
            return back()->withErrors(['delete' => 'Skema sudah dipakai periode hibah dan tidak dapat dihapus.']);
        }
        $grantScheme->delete();

        return back()->with('success', 'Skema hibah berhasil dihapus.');
    }
}
