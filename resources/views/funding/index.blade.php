@extends('layouts.app')
@section('title','Kontrak & Pendanaan')
@section('body')
<x-app-shell title="Kontrak & Pendanaan">
@if(session('success'))<div class="success-alert">{{ session('success') }}</div>@endif
<div class="page-intro"><div><span class="eyebrow eyebrow-dark">{{ request('type')==='pkm'?'PENDANAAN PKM':(request('type')==='penelitian'?'PENDANAAN PENELITIAN':'SEMUA PENDANAAN') }}</span><h2>{{ auth()->user()->hasRole('admin_uppm')?'Daftar kontrak':'Kontrak saya' }}</h2><p>Pantau nilai kontrak, masa pelaksanaan, dan realisasi pencairan dana.</p></div>@can('create',App\Models\FundingContract::class)<a class="primary-link" href="{{ route('funding.create',['type'=>request('type')]) }}">+ Buat kontrak</a>@endcan</div>
<section class="funding-grid">@forelse($contracts as $contract)<article class="funding-card"><div class="funding-top"><span class="status-pill">{{ ucfirst($contract->status) }}</span><small>{{ $contract->contract_number }}</small></div><h3>{{ $contract->proposal->title }}</h3><p>{{ $contract->proposal->lead->name }}</p><div class="funding-progress"><div><span>Realisasi</span><strong>Rp {{ number_format($contract->paid_amount??0,0,',','.') }}</strong></div><div><span>Nilai kontrak</span><strong>Rp {{ number_format($contract->contract_value,0,',','.') }}</strong></div></div><a href="{{ route('funding.show',$contract) }}">Lihat pendanaan →</a></article>@empty<div class="empty-card"><strong>Belum ada kontrak</strong><p>Kontrak dibuat setelah proposal ditetapkan diterima.</p></div>@endforelse</section>{{ $contracts->links() }}
</x-app-shell>
@endsection
