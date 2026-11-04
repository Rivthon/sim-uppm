@extends('layouts.app')
@section('title', 'Pelaksanaan & Monev')
@section('body')
<x-app-shell title="Pelaksanaan & Monev">
    <div class="page-intro"><div><span class="eyebrow">KENDALI KEGIATAN</span><h2>Pelaksanaan {{ request('type')==='pkm'?'Pengabdian kepada Masyarakat':(request('type')==='penelitian'?'Penelitian & Pengembangan':'seluruh kegiatan') }}</h2><p>Pantau logbook, laporan kemajuan, dan hasil evaluasi setiap kontrak aktif.</p></div></div>
    <div class="execution-list">
        @forelse($contracts as $contract)
            <a class="execution-card" href="{{ route('execution.show', $contract) }}">
                <div class="execution-main"><div class="proposal-meta"><span class="type-pill">{{ $contract->proposal->code }}</span><span>{{ $contract->contract_number }}</span></div><h3>{{ $contract->proposal->title }}</h3><p>{{ $contract->proposal->lead->name }} · {{ $contract->starts_at->format('d M Y') }} – {{ $contract->ends_at->format('d M Y') }}</p></div>
                <div class="execution-counts"><span><b>{{ $contract->activity_logs_count }}</b> Logbook</span><span><b>{{ $contract->progress_reports_count }}</b> Laporan</span><span><b>{{ $contract->evaluations_count }}</b> Monev</span></div>
            </a>
        @empty
            <div class="empty-card"><h3>Belum ada kontrak pelaksanaan</h3><p>Kontrak berstatus aktif atau ditandatangani akan muncul di sini.</p></div>
        @endforelse
    </div>
    {{ $contracts->links() }}
</x-app-shell>
@endsection
