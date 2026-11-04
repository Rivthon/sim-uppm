<div class="app-shell">
    <aside class="sidebar" data-sidebar>
        <div class="brand sidebar-brand"><div class="brand-mark">U</div><div><strong>SIM UPPM</strong><span>Kampus SBH</span></div></div>
        <nav class="nav-list">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span>⌂</span>Dashboard</a>
            @if(auth()->user()->hasRole('admin_uppm'))
                <p class="nav-heading">Alur utama</p>
                <a href="{{ route('incoming-proposals.index') }}" class="nav-item {{ request()->routeIs('incoming-proposals.*') || request()->routeIs('proposals.show') ? 'active' : '' }}"><span>↓</span>Pengajuan Masuk</a>
                <a href="{{ route('reviews.index') }}" class="nav-item {{ request()->routeIs('reviews.*') ? 'active' : '' }}"><span>✓</span>Proses Review</a>
                <a href="{{ route('execution.index') }}" class="nav-item {{ request()->routeIs('execution.*') ? 'active' : '' }}"><span>▶</span>Kegiatan Berjalan</a>
                <a href="{{ route('reporting.index') }}" class="nav-item {{ request()->routeIs('reporting.*') ? 'active' : '' }}"><span>□</span>Laporan & Luaran</a>
                <p class="nav-heading">Perencanaan & data</p>
                <a href="{{ route('strategic-plans.index') }}" class="nav-item {{ request()->routeIs('strategic-plans.*') ? 'active' : '' }}"><span>◇</span>Renstra & Roadmap</a>
                <a href="{{ route('master-data.index') }}" class="nav-item {{ request()->routeIs('master-data.*') ? 'active' : '' }}"><span>≡</span>Master Data</a>
                <a href="{{ route('site-settings.edit') }}" class="nav-item {{ request()->routeIs('site-settings.*') ? 'active' : '' }}"><span>⚙</span>Pengaturan Website</a>
                <a href="{{ route('accreditation.index') }}" class="nav-item {{ request()->routeIs('accreditation.*') ? 'active' : '' }}"><span>▤</span>Rekap Akreditasi</a>
            @else
                <p class="nav-heading">Pengajuan saya</p>
                @can('create', App\Models\Proposal::class)
                    <a href="{{ route('proposals.create', ['type' => 'penelitian']) }}" class="nav-item {{ request('type') === 'penelitian' && request()->routeIs('proposals.create') ? 'active' : '' }}"><span>+</span>Ajukan Penelitian</a>
                    <a href="{{ route('proposals.create', ['type' => 'pkm']) }}" class="nav-item {{ request('type') === 'pkm' && request()->routeIs('proposals.create') ? 'active' : '' }}"><span>+</span>Ajukan PkM</a>
                @endcan
                <a href="{{ route('proposals.index') }}" class="nav-item {{ request()->routeIs('proposals.*') && ! request()->routeIs('proposals.create') ? 'active' : '' }}"><span>□</span>{{ auth()->user()->isStudent() ? 'Kegiatan Tim' : 'Pengajuan Saya' }}</a>
                @if(! auth()->user()->isStudent())<a href="{{ route('reviews.index') }}" class="nav-item {{ request()->routeIs('reviews.*') ? 'active' : '' }}"><span>✓</span>Tugas Review</a>@endif
                <a href="{{ route('execution.index') }}" class="nav-item {{ request()->routeIs('execution.*') ? 'active' : '' }}"><span>▶</span>Kegiatan Berjalan</a>
                <a href="{{ route('reporting.index') }}" class="nav-item {{ request()->routeIs('reporting.*') ? 'active' : '' }}"><span>□</span>Laporan & Luaran</a>
            @endif
        </nav>
        <div class="sidebar-user"><div class="avatar">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</div><div><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->roleLabel() }}</span></div></div>
    </aside>
    <div class="main-area">
        <header class="topbar"><button class="menu-button" type="button" data-menu>☰</button><div><span class="breadcrumb">SIM UPPM / {{ $title }}</span><h1>{{ $title }}</h1></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button">Keluar</button></form></header>
        <main class="content">{{ $slot }}</main>
    </div>
</div>
