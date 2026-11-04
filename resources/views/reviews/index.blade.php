@extends('layouts.app')
@section('title', 'Reviewer & Penilaian')
@section('body')
<x-app-shell title="Reviewer & Penilaian">
 @if(session('success'))<div class="success-alert">{{ session('success') }}</div>@endif
 <div class="page-intro"><div><span class="eyebrow eyebrow-dark">SELEKSI {{ request('type')==='pkm'?'PKM':(request('type')==='penelitian'?'PENELITIAN':'SEMUA KEGIATAN') }}</span><h2>{{ auth()->user()->hasRole('admin_uppm') ? 'Penugasan reviewer' : 'Tugas penilaian saya' }}</h2><p>Kelola penugasan, tenggat, konflik kepentingan, skor, dan rekomendasi proposal.</p></div>@can('create', App\Models\ReviewAssignment::class)<a class="primary-link" href="{{ route('reviews.create',['type'=>request('type')]) }}">+ Tugaskan reviewer</a>@endcan</div>
 <section class="review-grid">@forelse($assignments as $assignment)<article class="review-card"><div class="review-card-top"><span class="status-pill review-status-{{ $assignment->status }}">{{ str($assignment->status)->replace('_',' ')->title() }}</span><span class="review-deadline">Tenggat {{ $assignment->due_at->translatedFormat('d M Y') }}</span></div><h3>{{ $assignment->proposal->title }}</h3><p>{{ $assignment->proposal->code }} · Ketua: {{ $assignment->proposal->lead->name }}</p><dl><div><dt>Reviewer</dt><dd>{{ $assignment->reviewer->name }}</dd></div><div><dt>Skor</dt><dd>{{ $assignment->totalScore() ?? '—' }} / 100</dd></div></dl><a href="{{ route('reviews.show', $assignment) }}">Buka penugasan →</a></article>@empty<div class="empty-card"><strong>Belum ada tugas review</strong><p>Penugasan reviewer akan muncul setelah proposal diajukan.</p></div>@endforelse</section>
 {{ $assignments->links() }}
</x-app-shell>
@endsection
