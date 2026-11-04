@extends('layouts.app')
@section('title','Rekap Akreditasi')
@section('body')
<x-app-shell title="Rekap Akreditasi">
<div class="page-intro"><div><span class="eyebrow eyebrow-dark">BUKTI KINERJA</span><h2>Rekap bukti akreditasi {{ $year }}</h2><p>Dokumen laporan akhir dan luaran yang telah diverifikasi Admin UPPM.</p></div><a class="primary-link" href="{{ route('accreditation.export',['year'=>$year]) }}">Ekspor CSV</a></div>
<form class="dashboard-filter" method="GET"><label>Tahun<input type="number" min="2020" max="2100" name="year" value="{{ $year }}"></label><button class="primary-button">Tampilkan</button></form>
<section class="stat-grid accreditation-stats"><article><span>Total bukti</span><strong>{{ $evidence->count() }}</strong><small>Dokumen terverifikasi</small></article><article><span>Luaran</span><strong>{{ $summary->get('Luaran',0) }}</strong><small>Publikasi, HKI, buku, produk</small></article><article><span>Laporan akhir</span><strong>{{ $summary->get('Laporan Akhir',0) }}</strong><small>Penelitian dan PkM selesai</small></article></section>
<section class="table-panel"><table><thead><tr><th>Kategori</th><th>Jenis</th><th>Kegiatan</th><th>Bukti</th><th>Dosen</th><th>Tanggal</th><th>Aksi</th></tr></thead><tbody>@forelse($evidence as $item)<tr><td><span class="type-pill">{{ $item['category'] }}</span></td><td>{{ $item['type'] }}</td><td><strong>{{ $item['proposal'] }}</strong></td><td>{{ $item['title'] }}</td><td>{{ $item['lecturer'] }}</td><td>{{ $item['date'] }}</td><td><a class="table-action" href="{{ $item['url'] }}">Unduh bukti</a></td></tr>@empty<tr><td colspan="7" class="empty-state">Belum ada bukti terverifikasi pada tahun ini.</td></tr>@endforelse</tbody></table></section>
</x-app-shell>
@endsection
