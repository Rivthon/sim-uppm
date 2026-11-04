@extends('layouts.app')
@section('title','Ubah Renstra')
@section('body')<x-app-shell title="Ubah Renstra">@if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif<div class="page-intro"><div><span class="eyebrow eyebrow-dark">PENGATURAN</span><h2>{{ $strategicPlan->title }}</h2></div></div><form class="form-panel" method="POST" action="{{ route('strategic-plans.update',$strategicPlan) }}" enctype="multipart/form-data">@include('strategic-plans._form')</form></x-app-shell>@endsection
