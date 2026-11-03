@extends('layouts.app')
@section('title', 'Proposal')
@section('body')
<x-app-shell title="Proposal">
    @if(session('success'))<div class="success-alert">{{ session('success') }}</div>@endif
    <div class="page-intro"><div><span class="eyebrow eyebrow-dark">Penelitian & PkM</span><h2>{{ auth()->user()->hasRole('admin_uppm') ? 'Seluruh proposal' : 'Proposal saya' }}</h2><p>Kelola draft, tim pelaksana, dokumen substansi, dan status pengajuan.</p></div>@can('create', App\Models\Proposal::class)<a class="primary-link" href="{{ route('proposals.create') }}">+ Buat proposal</a>@endcan</div>
    <section class="proposal-list">
        @forelse($proposals as $proposal)
            <article class="proposal-card"><div class="proposal-main"><div class="proposal-meta"><span class="type-pill">{{ strtoupper($proposal->type) }}</span><span>{{ $proposal->grantPeriod->name }}</span><span>{{ $proposal->researchField->name }}</span></div><h3>{{ $proposal->title }}</h3><p>{{ $proposal->lead->name }} · {{ $proposal->members_count }} anggota · Rp {{ number_format($proposal->requested_budget, 0, ',', '.') }}</p></div><div class="proposal-side"><span class="status-pill status-{{ $proposal->status }}">{{ $proposal->status === 'submitted' ? 'Diajukan' : 'Draft' }}</span><a class="table-action" href="{{ route('proposals.show', $proposal) }}">Lihat detail →</a></div></article>
        @empty
            <div class="empty-card"><strong>Belum ada proposal</strong><p>Buat draft proposal pertama pada program hibah yang sedang dibuka.</p></div>
        @endforelse
    </section>
    {{ $proposals->links() }}
</x-app-shell>
@endsection
