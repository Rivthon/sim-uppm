@extends('layouts.app')
@section('title', $publicPage->title)
@section('body')
<div class="public-home">
    <x-public-navigation :settings="$settings" :menu-pages="$menuPages" />
    <main class="public-page-wrap">
        <nav class="public-breadcrumb"><a href="{{ route('home') }}">Beranda</a><span>›</span>@if($publicPage->parent)<a href="{{ route('public-pages.show', $publicPage->parent) }}">{{ $publicPage->parent->title }}</a><span>›</span>@endif<strong>{{ $publicPage->title }}</strong></nav>
        <header class="public-page-hero"><span>Penelitian & Pengabdian</span><h1>{{ $publicPage->title }}</h1>@if($publicPage->summary)<p>{{ $publicPage->summary }}</p>@endif</header>
        <div class="public-page-layout"><article class="public-page-content">{!! nl2br(e($publicPage->content)) !!}</article><aside class="public-page-side"><strong>Menu terkait</strong>@if($publicPage->parent)<a href="{{ route('public-pages.show', $publicPage->parent) }}">{{ $publicPage->parent->title }}</a>@endif @foreach(($publicPage->parent ? $publicPage->parent->children : $publicPage->children) as $related)<a class="{{ $related->is($publicPage) ? 'active' : '' }}" href="{{ route('public-pages.show', $related) }}">{{ $related->title }}</a>@endforeach</aside></div>
    </main>
    <footer class="public-footer"><div><div class="brand brand-light"><span class="brand-mark">U</span><span><strong>{{ $settings->site_name }}</strong><small>{{ $settings->tagline }}</small></span></div><p>{{ $settings->address }}</p></div><div><span>Hubungi UPPM</span>@if($settings->contact_email)<a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a>@endif</div><small>© {{ now()->year }} {{ $settings->site_name }}</small></footer>
</div>
@endsection
