@extends('layouts.app')
@section('title', 'Menu & Halaman Publik')
@section('body')
<x-app-shell title="Menu & Halaman Publik">
    @if(session('success'))<div class="success-alert">{{ session('success') }}</div>@endif
    <div class="page-intro"><div><span class="eyebrow eyebrow-dark">Konten dinamis</span><h2>Menu Penelitian & Pengabdian</h2><p>Kelola halaman utama, submenu, urutan, alamat URL, isi, dan status publikasi.</p></div><a class="primary-link" href="{{ route('public-pages.create') }}">+ Tambah halaman</a></div>
    <section class="public-page-admin-list">
        @forelse($pages as $page)
            <article class="public-page-admin-item {{ $page->parent_id ? 'is-child' : '' }}"><div><span class="page-level">{{ $page->parent ? 'Submenu · '.$page->parent->title : 'Menu utama' }}</span><h3>{{ $page->title }}</h3><p>/informasi/{{ $page->slug }} · Urutan {{ $page->sort_order }} @if(!$page->parent_id)· {{ $page->children_count }} submenu @endif</p></div><span class="status-pill {{ $page->is_published ? '' : 'status-draft' }}">{{ $page->is_published ? 'Tayang' : 'Draft' }}</span><div class="page-admin-actions"><a href="{{ route('public-pages.show', $page) }}" target="_blank">Lihat</a><a href="{{ route('public-pages.edit', $page) }}">Edit</a><form method="POST" action="{{ route('public-pages.destroy', $page) }}" onsubmit="return confirm('Hapus halaman ini beserta submenu di bawahnya?')">@csrf @method('DELETE')<button type="submit">Hapus</button></form></div></article>
        @empty<div class="empty-card"><strong>Belum ada halaman publik</strong><p>Tambahkan menu pertama untuk beranda UPPM.</p></div>@endforelse
    </section>
</x-app-shell>
@endsection
