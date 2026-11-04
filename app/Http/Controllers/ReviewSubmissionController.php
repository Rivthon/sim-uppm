<?php

namespace App\Http\Controllers;

use App\Models\ReviewAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ReviewSubmissionController extends Controller
{
    public function store(ReviewAssignment $assignment): RedirectResponse
    {
        Gate::authorize('submit', $assignment);

        if ($assignment->has_conflict) {
            if (! $assignment->conflict_notes) {
                return back()->withErrors(['conflict_notes' => 'Jelaskan konflik kepentingan sebelum mengirim deklarasi.']);
            }

            $assignment->update(['status' => 'conflict', 'recommendation' => null, 'submitted_at' => now()]);

            return redirect()->route('reviews.show', $assignment)->with('success', 'Konflik kepentingan telah dilaporkan kepada UPPM.');
        }

        $required = ['relevance_score', 'methodology_score', 'feasibility_score', 'budget_score', 'output_score', 'strengths', 'weaknesses', 'comments', 'recommendation'];
        if (collect($required)->contains(fn (string $field): bool => blank($assignment->{$field}))) {
            return back()->withErrors(['review' => 'Lengkapi seluruh skor, catatan, dan rekomendasi sebelum mengirim penilaian.']);
        }

        $assignment->update(['status' => 'submitted', 'submitted_at' => now()]);

        return redirect()->route('reviews.show', $assignment)->with('success', 'Penilaian berhasil dikirim dan dikunci.');
    }
}
