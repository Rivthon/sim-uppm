<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSiteSettingRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);
        $settings = SiteSetting::query()->firstOrCreate(['id' => 1], SiteSetting::defaults());

        return view('site-settings.edit', compact('settings'));
    }

    public function update(UpdateSiteSettingRequest $request): RedirectResponse
    {
        $settings = SiteSetting::query()->firstOrCreate(['id' => 1], SiteSetting::defaults());
        $settings->update([
            ...$request->safe()->except('is_announcement_active'),
            'is_announcement_active' => $request->boolean('is_announcement_active'),
            'updated_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Pengaturan beranda berhasil diperbarui.');
    }
}
