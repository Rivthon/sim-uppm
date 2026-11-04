@extends('layouts.app')
@section('title', 'Laporan & Luaran')
@section('body')
<x-app-shell title="Laporan & Luaran">
    <div class="page-intro"><div><span class="eyebrow">HASIL {{ request('type')==='pkm'?'PENGABDIAN':(request('type')==='penelitian'?'PENELITIAN':'KEGIATAN') }}</span><h2>Laporan akhir dan luaran {{ request('type')==='pkm'?'PkM':(request('type')==='penelitian'?'Penelitian & Pengembangan':'seluruh kegiatan') }}</h2><p>Kelola dokumen akhir, publikasi, HKI, buku, produk, dan bukti capaian lainnya.</p></div></div>
    <div class="execution-list">
        @forelse($contracts as $contract)
            <a class="execution-card" href="{{ route('reporting.show', $contract) }}">
                <div class="execution-main"><div class="proposal-meta"><span class="type-pill">{{ $contract->proposal->code }}</span><span>{{ $contract->contract_number }}</span></div><h3>{{ $contract->proposal->title }}</h3><p>{{ $contract->proposal->lead->name }}</p></div>
                <div class="execution-counts"><span><b>{{ $contract->finalReport ? 1 : 0 }}</b>Laporan akhir</span><span><b>{{ $contract->outputs->count() }}</b>Total luaran</span><span><b>{{ $contract->outputs->where('verification_status', 'verified')->count() }}</b>Terverifikasi</span></div>
            </a>
        @empty<div class="empty-card"><h3>Belum ada kontrak</h3><p>Kontrak pendanaan akan muncul di halaman ini.</p></div>@endforelse
    </div>
    {{ $contracts->links() }}
</x-app-shell>
@endsection
