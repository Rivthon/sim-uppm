@extends('layouts.app')
@section('title', 'Master Data')
@section('body')
<x-app-shell title="Master Data">
    <div class="page-intro"><div><span class="eyebrow eyebrow-dark">Referensi sistem</span><h2>Data dasar UPPM</h2><p>Data referensi yang digunakan bersama oleh hibah, proposal, reviewer, dan pelaporan.</p></div></div>
    <div class="master-grid">
        <section class="data-panel"><div class="panel-heading"><h3>Program studi</h3><span>{{ $studyPrograms->count() }} data</span></div>@forelse($studyPrograms as $item)<div class="data-row"><div><strong>{{ $item->name }}</strong><small>{{ $item->code }} · {{ $item->level }} · {{ $item->faculty }}</small></div><span class="status-pill">Aktif</span></div>@empty<p class="empty-state">Belum ada program studi.</p>@endforelse</section>
        <section class="data-panel"><div class="panel-heading"><h3>Tahun akademik</h3><span>{{ $academicYears->count() }} data</span></div>@forelse($academicYears as $item)<div class="data-row"><div><strong>{{ $item->name }}</strong><small>{{ $item->starts_at->format('d M Y') }} – {{ $item->ends_at->format('d M Y') }}</small></div>@if($item->is_active)<span class="status-pill">Aktif</span>@endif</div>@empty<p class="empty-state">Belum ada tahun akademik.</p>@endforelse</section>
        <section class="data-panel"><div class="panel-heading"><h3>Bidang ilmu</h3><span>{{ $researchFields->count() }} data</span></div>@forelse($researchFields as $item)<div class="data-row"><div><strong>{{ $item->name }}</strong><small>{{ $item->code }}</small></div><span class="status-pill">Aktif</span></div>@empty<p class="empty-state">Belum ada bidang ilmu.</p>@endforelse</section>
        <section class="data-panel"><div class="panel-heading"><h3>Skema hibah</h3><span>{{ $schemes->count() }} data</span></div>@forelse($schemes as $item)<div class="data-row"><div><strong>{{ $item->name }}</strong><small>{{ $item->code }} · {{ strtoupper($item->type) }} · Maks. Rp {{ number_format($item->maximum_funding, 0, ',', '.') }}</small></div><span class="status-pill">Aktif</span></div>@empty<p class="empty-state">Belum ada skema hibah.</p>@endforelse</section>
    </div>
</x-app-shell>
@endsection
