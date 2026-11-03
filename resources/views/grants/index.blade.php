@extends('layouts.app')
@section('title', 'Program & Hibah')
@section('body')
<x-app-shell title="Program & Hibah">
    @if(session('success'))<div class="success-alert">{{ session('success') }}</div>@endif
    <div class="page-intro"><div><span class="eyebrow eyebrow-dark">Pengelolaan program</span><h2>Periode hibah</h2><p>Atur jadwal, skema, kuota, anggaran, dan status penerimaan proposal.</p></div><a class="primary-link" href="{{ route('grants.create') }}">+ Buat program hibah</a></div>
    <section class="table-panel"><table><thead><tr><th>Program</th><th>Skema</th><th>Periode pengajuan</th><th>Anggaran</th><th>Status</th><th></th></tr></thead><tbody>@forelse($periods as $period)<tr><td><strong>{{ $period->name }}</strong><small>{{ $period->academicYear->name }}</small></td><td>{{ $period->scheme->name }}<small>{{ $period->scheme->code }}</small></td><td>{{ $period->submission_starts_at->format('d M Y, H:i') }}<small>s.d. {{ $period->submission_ends_at->format('d M Y, H:i') }}</small></td><td>Rp {{ number_format($period->budget, 0, ',', '.') }}<small>Kuota: {{ $period->quota ?? 'Tanpa batas' }}</small></td><td><span class="status-pill status-{{ $period->status }}">{{ ucfirst($period->status) }}</span></td><td><a class="table-action" href="{{ route('grants.edit', $period) }}">Kelola →</a></td></tr>@empty<tr><td colspan="6" class="empty-state">Belum ada periode hibah. Buat program pertama untuk mulai menerima proposal.</td></tr>@endforelse</tbody></table></section>
    {{ $periods->links() }}
</x-app-shell>
@endsection
