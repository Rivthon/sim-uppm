@extends('layouts.app')
@section('title', 'Pengajuan Masuk')
@section('body')
<x-app-shell title="Pengajuan Masuk">
    @if(session('success'))<div class="success-alert">{{ session('success') }}</div>@endif
    <div class="page-intro">
        <div><span class="eyebrow eyebrow-dark">Kotak masuk UPPM</span><h2>Proposal siap diperiksa</h2><p>Klik satu pengajuan untuk membaca seluruh proposal dan langsung menugaskan reviewer.</p></div>
        <span class="inbox-total">{{ $proposals->total() }} pengajuan</span>
    </div>
    <section class="proposal-list">
        @forelse($proposals as $proposal)
            <a class="proposal-card incoming-card" href="{{ route('proposals.show', $proposal) }}">
                <div class="proposal-main"><div class="proposal-meta"><span class="type-pill">{{ $proposal->type === 'pkm' ? 'PkM' : 'Penelitian' }}</span><span>{{ $proposal->code }}</span><span>{{ $proposal->submitted_at?->translatedFormat('d M Y, H:i') }}</span></div><h3>{{ $proposal->title }}</h3><p>{{ $proposal->lead->name }} · {{ $proposal->members_count }} anggota · {{ $proposal->grantPeriod->name }}</p></div>
                <div class="proposal-side"><span class="status-pill {{ $proposal->review_assignments_count ? 'status-assigned' : 'status-waiting' }}">{{ $proposal->review_assignments_count ? $proposal->review_assignments_count.' reviewer ditugaskan' : 'Menunggu reviewer' }}</span><span class="table-action">Baca & assign reviewer →</span></div>
            </a>
        @empty
            <div class="empty-card"><strong>Tidak ada pengajuan baru</strong><p>Proposal dosen yang sudah dikirim akan otomatis tampil di halaman ini.</p></div>
        @endforelse
    </section>
    {{ $proposals->links() }}
</x-app-shell>
@endsection
