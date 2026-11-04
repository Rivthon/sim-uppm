<div class="app-shell">
    <aside class="sidebar" data-sidebar>
        <div class="brand sidebar-brand"><div class="brand-mark">U</div><div><strong>SIM UPPM</strong><span>Kampus SBH</span></div></div>
        <nav class="nav-list">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span>⌂</span>Dashboard</a>@if(auth()->user()->hasRole('admin_uppm'))<a href="{{ route('accreditation.index') }}" class="nav-item {{ request()->routeIs('accreditation.*') ? 'active' : '' }}"><span>▤</span>Rekap Akreditasi</a>@endif
            <p class="nav-heading">Ruang kerja</p>
            <a href="{{ route('workspaces.show','penelitian') }}" class="nav-item workspace-nav research {{ request()->routeIs('workspaces.show') && request()->route('type') === 'penelitian' || request('type') === 'penelitian' ? 'active' : '' }}"><span>⌁</span>Penelitian & Pengembangan</a>
            <a href="{{ route('workspaces.show','pkm') }}" class="nav-item workspace-nav community {{ request()->routeIs('workspaces.show') && request()->route('type') === 'pkm' || request('type') === 'pkm' ? 'active' : '' }}"><span>♧</span>Pengabdian (PkM)</a>
            @if(auth()->user()->hasRole('admin_uppm'))<p class="nav-heading">Administrasi</p><a href="{{ route('master-data.index') }}" class="nav-item {{ request()->routeIs('master-data.*') ? 'active' : '' }}"><span>◇</span>Master Data</a>@endif
        </nav>
        <div class="sidebar-user"><div class="avatar">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</div><div><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->roleLabel() }}</span></div></div>
    </aside>
    <div class="main-area">
        <header class="topbar"><button class="menu-button" type="button" data-menu>☰</button><div><span class="breadcrumb">SIM UPPM / {{ $title }}</span><h1>{{ $title }}</h1></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button">Keluar</button></form></header>
        <main class="content">{{ $slot }}</main>
    </div>
</div>
