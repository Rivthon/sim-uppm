<div class="app-shell">
    <aside class="sidebar" data-sidebar>
        <div class="brand sidebar-brand"><div class="brand-mark">U</div><div><strong>SIM UPPM</strong><span>Kampus SBH</span></div></div>
        <nav class="nav-list">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span>⌂</span>Dashboard</a>
            <p class="nav-heading">Perencanaan</p>
            <a href="{{ route('master-data.index') }}" class="nav-item {{ request()->routeIs('master-data.*') ? 'active' : '' }}"><span>◇</span>Master Data</a>
            <a href="#" class="nav-item"><span>⌁</span>Renstra & Roadmap</a>
            <p class="nav-heading">Program</p>
            <a href="{{ route('grants.index') }}" class="nav-item {{ request()->routeIs('grants.*') ? 'active' : '' }}"><span>✦</span>Program & Hibah</a>
            <a href="{{ route('proposals.index') }}" class="nav-item {{ request()->routeIs('proposals.*') ? 'active' : '' }}"><span>□</span>Proposal</a><a href="{{ route('reviews.index') }}" class="nav-item {{ request()->routeIs('reviews.*') ? 'active' : '' }}"><span>◎</span>Reviewer</a><a href="{{ route('funding.index') }}" class="nav-item {{ request()->routeIs('funding.*') ? 'active' : '' }}"><span>▱</span>Kontrak & Pendanaan</a>
            <p class="nav-heading">Pelaksanaan</p><a href="{{ route('execution.index') }}" class="nav-item {{ request()->routeIs('execution.*') ? 'active' : '' }}"><span>↗</span>Pelaksanaan & Monev</a><a href="{{ route('reporting.index') }}" class="nav-item {{ request()->routeIs('reporting.*') ? 'active' : '' }}"><span>▥</span>Laporan & Luaran</a>
        </nav>
        <div class="sidebar-user"><div class="avatar">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</div><div><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->roleLabel() }}</span></div></div>
    </aside>
    <div class="main-area">
        <header class="topbar"><button class="menu-button" type="button" data-menu>☰</button><div><span class="breadcrumb">SIM UPPM / {{ $title }}</span><h1>{{ $title }}</h1></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button">Keluar</button></form></header>
        <main class="content">{{ $slot }}</main>
    </div>
</div>
