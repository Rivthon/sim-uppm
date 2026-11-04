@extends('layouts.app')
@section('title','Buat Renstra')
@section('body')<x-app-shell title="Buat Renstra">@if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif<div class="page-intro"><div><span class="eyebrow eyebrow-dark">RENSTRA BARU</span><h2>Tetapkan arah strategis</h2><p>Roadmap tahunan dapat ditambahkan setelah Renstra disimpan.</p></div></div><form class="form-panel" method="POST" action="{{ route('strategic-plans.store') }}" enctype="multipart/form-data">@include('strategic-plans._form')</form></x-app-shell>@endsection
