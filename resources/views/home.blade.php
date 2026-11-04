@extends('layouts.app')
@section('title', 'Beranda')
@section('body')
<div class="public-home">
    <header class="public-nav">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark">U</span><span><strong>{{ $settings->site_name }}</strong><small>{{ $settings->tagline }}</small></span></a>
        <nav><a href="#tentang">Tentang</a><a href="#informasi">Informasi</a><a href="#kontak">Kontak</a>@auth<a class="public-login" href="{{ route('dashboard') }}">Buka Dashboard</a>@else<a class="public-login" href="{{ route('login') }}">Masuk Portal</a>@endauth</nav>
    </header>

    <main>
        <section class="public-hero">
            <div class="public-hero-copy"><span class="public-kicker">Unit Penelitian dan Pengabdian kepada Masyarakat</span><h1>{{ $settings->hero_title }}</h1><p>{{ $settings->hero_description }}</p><div class="public-actions">@auth<a href="{{ route('dashboard') }}">Buka Dashboard <span>→</span></a>@else<a href="{{ route('login') }}">Masuk dengan PAS <span>→</span></a>@endauth<a href="#tentang" class="public-secondary">Pelajari UPPM</a></div></div>
            <div class="public-hero-visual"><div class="orbit orbit-one"></div><div class="orbit orbit-two"></div><div class="visual-card"><span>Penelitian</span><strong>Gagasan menjadi dampak</strong><small>Pengajuan · Review · Pendanaan · Luaran</small></div><div class="visual-card visual-card-alt"><span>Pengabdian</span><strong>Ilmu hadir di masyarakat</strong><small>Kolaborasi · Pelaksanaan · Evaluasi</small></div></div>
        </section>

        @if($settings->is_announcement_active && $settings->announcement_title)
            <section id="informasi" class="announcement-strip"><span>Informasi terbaru</span><div><strong>{{ $settings->announcement_title }}</strong><p>{{ $settings->announcement_content }}</p></div>@if($settings->announcement_link)<a href="{{ $settings->announcement_link }}" target="_blank" rel="noopener noreferrer">Selengkapnya →</a>@endif</section>
        @endif

        <section class="public-stats" aria-label="Statistik UPPM">
            <article><strong>{{ number_format($stats['proposals']) }}</strong><span>Proposal diajukan</span></article><article><strong>{{ number_format($stats['activities']) }}</strong><span>Kegiatan didanai</span></article><article><strong>{{ number_format($stats['outputs']) }}</strong><span>Luaran terverifikasi</span></article><article><strong>{{ number_format($stats['lecturers']) }}</strong><span>Dosen terhubung</span></article>
        </section>

        <section id="tentang" class="public-about">
            <div><span class="public-section-label">Tentang kami</span><h2>{{ $settings->about_title }}</h2></div><div><p>{{ $settings->about_content }}</p><div class="public-pillars"><article><span>01</span><strong>Penelitian</strong><p>Mendukung riset yang relevan, terukur, dan selaras dengan roadmap institusi.</p></article><article><span>02</span><strong>Pengabdian</strong><p>Mendorong penerapan ilmu untuk kebutuhan nyata masyarakat dan mitra.</p></article><article><span>03</span><strong>Luaran</strong><p>Mendokumentasikan publikasi, HKI, produk, serta capaian kegiatan.</p></article></div></div>
        </section>
    </main>

    <footer id="kontak" class="public-footer"><div><div class="brand brand-light"><span class="brand-mark">U</span><span><strong>{{ $settings->site_name }}</strong><small>{{ $settings->tagline }}</small></span></div><p>{{ $settings->address }}</p></div><div><span>Hubungi UPPM</span>@if($settings->contact_email)<a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a>@endif @if($settings->contact_phone)<a href="tel:{{ $settings->contact_phone }}">{{ $settings->contact_phone }}</a>@endif</div><small>© {{ now()->year }} {{ $settings->site_name }}</small></footer>
</div>
@endsection
