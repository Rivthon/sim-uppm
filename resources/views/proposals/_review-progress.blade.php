<section class="lecturer-review-panel">
    <div class="lecturer-review-heading">
        <div><span class="eyebrow eyebrow-dark">Proses review</span><h3>Reviewer dan hasil penilaian</h3><p>Hasil lengkap akan terbuka setelah reviewer mengirim dan mengunci penilaiannya.</p></div>
        <span class="review-count">{{ $assignments->count() }} reviewer</span>
    </div>

    @forelse($assignments as $assignment)
        @php
            $statusLabel = match($assignment->status) {
                'assigned' => 'Ditugaskan',
                'in_review' => 'Sedang direview',
                'submitted' => 'Review selesai',
                'conflict' => 'Konflik dilaporkan',
                default => str($assignment->status)->replace('_', ' ')->title(),
            };
        @endphp
        <article class="lecturer-review-card">
            <div class="reviewer-identity">
                <span class="reviewer-avatar">{{ str($assignment->reviewer->name)->substr(0, 1)->upper() }}</span>
                <div><strong>{{ $assignment->reviewer->name }}</strong><small>Tenggat {{ $assignment->due_at->translatedFormat('d F Y, H:i') }}</small></div>
                <span class="status-pill review-status-{{ $assignment->status }}">{{ $statusLabel }}</span>
            </div>

            @if($assignment->status === 'submitted')
                <div class="lecturer-score-grid">
                    @foreach(['Relevansi' => $assignment->relevance_score, 'Metodologi' => $assignment->methodology_score, 'Kelayakan' => $assignment->feasibility_score, 'Anggaran' => $assignment->budget_score, 'Luaran' => $assignment->output_score] as $label => $score)
                        <div><span>{{ $label }}</span><strong>{{ $score }}/20</strong></div>
                    @endforeach
                    <div class="total-review-score"><span>Total</span><strong>{{ $assignment->totalScore() }}/100</strong></div>
                </div>
                <div class="lecturer-review-notes">
                    <div><span>Kekuatan proposal</span><p>{{ $assignment->strengths }}</p></div>
                    <div><span>Kelemahan/perbaikan</span><p>{{ $assignment->weaknesses }}</p></div>
                    <div><span>Catatan keseluruhan</span><p>{{ $assignment->comments }}</p></div>
                    <div class="review-recommendation"><span>Rekomendasi reviewer</span><strong>{{ str($assignment->recommendation)->replace(['funded', 'revision', 'rejected'], ['Layak didanai', 'Revisi', 'Ditolak'])->title() }}</strong><small>Dikirim {{ $assignment->submitted_at?->translatedFormat('d F Y, H:i') }}</small></div>
                </div>
            @elseif($assignment->status === 'conflict')
                <div class="review-locked-note">Reviewer melaporkan konflik kepentingan. Admin UPPM akan menugaskan reviewer pengganti.</div>
            @else
                <div class="review-locked-note">Rincian skor dan catatan belum ditampilkan karena penilaian masih berlangsung.</div>
            @endif
        </article>
    @empty
        <div class="review-empty-state"><strong>Reviewer belum ditugaskan</strong><p>Admin UPPM akan menentukan reviewer setelah memeriksa pengajuan.</p></div>
    @endforelse
</section>
