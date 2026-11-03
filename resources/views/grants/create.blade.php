@extends('layouts.app')
@section('title', 'Buat Program Hibah')
@section('body')<x-app-shell title="Buat Program Hibah"><div class="page-intro"><div><span class="eyebrow eyebrow-dark">Program baru</span><h2>Konfigurasi periode hibah</h2><p>Program dapat disimpan sebagai draft sebelum diumumkan kepada dosen.</p></div></div><form class="form-panel" method="POST" action="{{ route('grants.store') }}">@include('grants._form')</form></x-app-shell>@endsection
