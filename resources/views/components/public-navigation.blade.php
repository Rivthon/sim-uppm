@props(['settings', 'menuPages'])
<header class="public-nav">
    <a class="brand" href="{{ route('home') }}"><span class="brand-mark">U</span><span><strong>{{ $settings->site_name }}</strong><small>{{ $settings->tagline }}</small></span></a>
    <nav>
        <a href="{{ route('home') }}">Beranda</a>
        <div class="public-dropdown"><button type="button">Penelitian & Pengabdian <span>⌄</span></button><div class="public-dropdown-menu">
            @foreach($menuPages as $menuPage)
                <div class="public-menu-entry"><a href="{{ route('public-pages.show', $menuPage) }}">{{ $menuPage->title }}</a>
                    @if($menuPage->children->isNotEmpty())<div class="public-submenu">@foreach($menuPage->children as $child)<a href="{{ route('public-pages.show', $child) }}">{{ $child->title }}</a>@endforeach</div>@endif
                </div>
            @endforeach
        </div></div>
        <a href="{{ route('home') }}#kontak">Kontak</a>
        @auth<a class="public-login" href="{{ route('dashboard') }}">Buka Dashboard</a>@else<a class="public-login" href="{{ route('login') }}">Masuk Portal</a>@endauth
    </nav>
</header>
