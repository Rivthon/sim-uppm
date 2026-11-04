<?php

namespace App\Http\Controllers;

use App\Models\FundingContract;
use App\Models\Proposal;
use App\Models\PublicPage;
use App\Models\ResearchOutput;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $settings = SiteSetting::query()->first() ?? new SiteSetting(SiteSetting::defaults());
        $stats = [
            'proposals' => Proposal::query()->whereNotNull('submitted_at')->count(),
            'activities' => FundingContract::query()->whereIn('status', ['signed', 'active', 'completed'])->count(),
            'outputs' => ResearchOutput::query()->where('verification_status', 'verified')->count(),
            'lecturers' => User::query()->where('is_active', true)->whereJsonContains('roles', 'dosen')->count(),
        ];
        $menuPages = PublicPage::query()->whereNull('parent_id')->where('is_published', true)
            ->with(['children' => fn ($query) => $query->where('is_published', true)])
            ->orderBy('sort_order')->orderBy('title')->get();

        return view('home', compact('settings', 'stats', 'menuPages'));
    }
}
