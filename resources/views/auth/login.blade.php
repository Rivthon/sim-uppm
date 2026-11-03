@extends('layouts.app')
@section('title', 'Masuk')
@section('body')
<main class="login-shell">
 <section class="login-story"><div class="brand brand-light"><div class="brand-mark">U</div><div><strong>SIM UPPM</strong><span>Kampus SBH</span></div></div><div class="story-copy"><span class="eyebrow">Satu pintu pengelolaan tridharma</span><h1>Riset yang tertata.<br>Pengabdian yang berdampak.</h1><p>Kelola proposal, pendanaan, monitoring, laporan, dan luaran dalam satu alur yang transparan dan terdokumentasi.</p><div class="story-points"><span>Penelitian</span><i></i><span>Pengabdian</span><i></i><span>Luaran</span></div></div><p class="story-footer">Unit Penelitian dan Pengabdian kepada Masyarakat</p></section>
 <section class="login-panel"><div class="login-card"><div class="mobile-brand brand"><div class="brand-mark">U</div><div><strong>SIM UPPM</strong><span>Kampus SBH</span></div></div><span class="eyebrow eyebrow-dark">Portal terintegrasi PAS</span><h2>Selamat datang</h2><p class="login-subtitle">Masuk menggunakan akun PAS kampus Anda. Tidak perlu membuat akun baru.</p>
 @if ($errors->any())<div class="alert" role="alert">{{ $errors->first() }}</div>@endif
 <form action="{{ route('login.store') }}" method="POST" class="login-form">@csrf
  <label for="username">Username PAS</label><div class="field-wrap"><svg viewBox="0 0 24 24"><path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z"/></svg><input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" placeholder="Masukkan username" required autofocus></div>
  <label for="password">Password PAS</label><div class="field-wrap"><svg viewBox="0 0 24 24"><path d="M6 10V8a6 6 0 0 1 12 0v2M5 10h14v11H5zM12 14v3"/></svg><input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan password" required><button type="button" class="password-toggle" aria-label="Tampilkan password" data-password-toggle><svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></div>
  <label class="remember"><input type="checkbox" name="remember" value="1"><span>Ingat saya di perangkat ini</span></label><button class="primary-button" type="submit">Masuk dengan akun PAS <span>→</span></button>
 </form>
 @if (config('services.pas.driver') === 'mock')<div class="demo-note"><strong>Mode pengembangan</strong><span>Username: {{ config('services.pas.mock_username') }} · Password: {{ config('services.pas.mock_password') }}</span></div>@endif
 <p class="help-text">Kesulitan masuk? Hubungi administrator PAS kampus.</p></div></section>
</main>
@endsection
