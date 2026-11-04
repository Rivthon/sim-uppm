@extends('layouts.app')
@section('title', 'Tambah Halaman Publik')
@section('body')<x-app-shell title="Tambah Halaman Publik">@if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif<div class="page-intro"><div><span class="eyebrow eyebrow-dark">Halaman baru</span><h2>Tambah menu atau submenu</h2><p>Konten dapat diterbitkan sekarang atau disimpan sebagai draft.</p></div></div><form class="form-panel site-setting-form" method="POST" action="{{ route('public-pages.store') }}">@include('public-pages._form')</form></x-app-shell>@endsection
