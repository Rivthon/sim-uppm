@extends('layouts.app')
@section('title', 'Detail Proposal')
@section('body')
<x-app-shell title="Detail Proposal">
 @if(session('success'))<div class="success-alert">{{ session('success') }}</div>@endif @if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
 <div class="proposal-hero"><div><div class="proposal-meta"><span class="type-pill">{{ strtoupper($proposal->type) }}</span><span>{{ $proposal->grantPeriod->name }}</span><span>{{ $proposal->researchField->name }}</span></div><h2>{{ $proposal->title }}</h2><p>{{ $proposal->code ?? 'Belum memiliki nomor pengajuan' }}</p></div><span class="status-pill status-{{ $proposal->status }}">{{ $proposal->status === 'submitted' ? 'Diajukan' : 'Draft' }}</span></div>
 <div class="detail-grid"><section class="detail-main"><article class="detail-panel"><h3>Abstrak</h3><p>{{ $proposal->abstract }}</p></article><article class="detail-panel"><h3>Latar belakang</h3><p>{{ $proposal->background }}</p></article><article class="detail-panel"><h3>Tujuan</h3><p>{{ $proposal->objectives }}</p></article><article class="detail-panel"><h3>Metodologi</h3><p>{{ $proposal->methodology }}</p></article><article class="detail-panel"><h3>Keselarasan roadmap</h3><p>{{ $proposal->roadmap_alignment }}</p></article></section>
 <aside class="detail-side"><section class="detail-panel"><h3>Informasi pengajuan</h3><dl class="detail-list"><div><dt>Ketua</dt><dd>{{ $proposal->lead->name }}</dd></div><div><dt>Durasi</dt><dd>{{ $proposal->duration_months }} bulan</dd></div><div><dt>Dana</dt><dd>Rp {{ number_format($proposal->requested_budget, 0, ',', '.') }}</dd></div><div><dt>Mitra</dt><dd>{{ $proposal->partner_name ?: '—' }}</dd></div><div><dt>Diajukan</dt><dd>{{ $proposal->submitted_at?->translatedFormat('d F Y, H:i') ?? 'Belum' }}</dd></div></dl></section><section class="detail-panel"><h3>Anggota tim</h3>@forelse($proposal->members as $member)<div class="team-item"><strong>{{ $member->name }}</strong><small>{{ ucfirst($member->member_type) }} · {{ $member->identity_number ?: 'Tanpa nomor' }}</small></div>@empty<p class="muted">Belum ada anggota.</p>@endforelse</section>
 @if($proposal->document_path)<a class="document-link" href="{{ route('proposals.documents.show', $proposal) }}">Unduh dokumen proposal <span>↓</span></a>@endif
 @can('update',$proposal)<a class="primary-link block-link" href="{{ route('proposals.edit', $proposal) }}">Edit draft</a>@endcan
 @can('submit',$proposal)<form method="POST" action="{{ route('proposals.submissions.store', $proposal) }}" onsubmit="return confirm('Ajukan proposal? Setelah diajukan, proposal tidak dapat diedit.')">@csrf<button class="submit-proposal" type="submit">Ajukan ke UPPM →</button></form>@endcan
 </aside></div>
</x-app-shell>
@endsection
