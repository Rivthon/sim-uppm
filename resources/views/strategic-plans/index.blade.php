@extends('layouts.app')
@section('title','Renstra & Roadmap')
@section('body')
<x-app-shell title="Renstra & Roadmap">
@if(session('success'))<div class="success-alert">{{ session('success') }}</div>@endif
<div class="page-intro"><div><span class="eyebrow eyebrow-dark">ARAH STRATEGIS</span><h2>Renstra Penelitian & Pengabdian</h2><p>Arah institusi dan program studi sebagai acuan penyusunan proposal serta target luaran.</p></div>@can('create',App\Models\StrategicPlan::class)<a class="primary-link" href="{{ route('strategic-plans.create') }}">+ Buat Renstra</a>@endcan</div>
<div class="plan-list">@forelse($plans as $plan)<a class="plan-card" href="{{ route('strategic-plans.show',$plan) }}"><div class="plan-period"><strong>{{ $plan->start_year }}</strong><span>—</span><strong>{{ $plan->end_year }}</strong></div><div class="plan-content"><div class="proposal-meta"><span class="type-pill">{{ $plan->type==='pkm'?'PkM':ucfirst($plan->type) }}</span><span>{{ $plan->scope==='institution'?'Institusi':$plan->studyProgram?->name }}</span></div><h3>{{ $plan->title }}</h3><p>{{ str($plan->vision)->limit(150) }}</p></div><div class="plan-side"><span class="status-pill status-{{ $plan->status }}">{{ ucfirst($plan->status) }}</span><small>{{ $plan->milestones_count }} tahapan</small></div></a>@empty<div class="empty-card"><h3>Belum ada Renstra</h3><p>Admin UPPM dapat membuat arah strategis pertama.</p></div>@endforelse</div>{{ $plans->links() }}
</x-app-shell>
@endsection
