<nav class="sticky top-0 z-50 bg-base text-white shadow-md" aria-label="Main navigation">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="shrink-0 text-lg font-bold tracking-tight focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-base">
            {{ config('app.name') }}
        </a>

        {{-- Search (desktop) --}}
        <form action="{{ route('shop.index') }}" method="GET" class="hidden flex-1 max-w-md lg:block">
            <label for="nav-search" class="sr-only">Search products</label>
            <input type="search" id="nav-search" name="q" value="{{ request('q') }}"
                   placeholder="Search products..."
                   class="w-full rounded-lg border border-base-light bg-base-light px-3 py-2 text-sm text-white placeholder-gray-400 focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent">
        </form>

        {{-- Desktop: Categories, Cart, Account --}}
        <div class="hidden items-center gap-2 md:flex">
            {{-- Categories dropdown --}}
            @if(isset($categories) && $categories->isNotEmpty())
            <div class="relative" data-dropdown>
                <button type="button" data-dropdown-toggle
                        class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-base-light focus:outline-none focus:ring-2 focus:ring-accent"
                        aria-expanded="false" aria-haspopup="true" aria-label="Categories">
                    Categories
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div data-dropdown-menu class="absolute left-0 top-full z-10 mt-1 hidden min-w-[180px] rounded-lg border border-muted-border bg-surface py-1 shadow-lg">
                    <a href="{{ route('shop.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-muted">All</a>
                    @foreach($categories as $cat)
                        <a href="{{ route('shop.category', $cat->slug) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-muted">{{ $cat->name }}</a>
                    @endforeach
                </div>
            </div>
            @else
            <a href="{{ route('shop.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-base-light">Shop</a>
            @endif

            <a href="{{ route('contact') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-base-light">Contact</a>

            {{-- Cart --}}
            <a href="{{ route('cart.index') }}" class="relative rounded-lg p-2 text-white hover:bg-base-light focus:outline-none focus:ring-2 focus:ring-accent" aria-label="Cart">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                @if(isset($cartCount) && $cartCount > 0)
                    <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-accent text-xs font-bold text-white">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                @endif
            </a>

            @auth
                <div class="relative" data-dropdown>
                    <button type="button" data-dropdown-toggle
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-base-light focus:outline-none focus:ring-2 focus:ring-accent"
                            aria-expanded="false" aria-haspopup="true">
                        {{ Auth::user()->name }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div data-dropdown-menu class="absolute right-0 top-full z-10 mt-1 hidden min-w-[160px] rounded-lg border border-muted-border bg-surface py-1 shadow-lg text-gray-700">
                        <a href="{{ route('account.orders') }}" class="block px-4 py-2 text-sm hover:bg-muted">My Orders</a>
                        @if(Auth::user()->is_admin ?? false)
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-muted">Admin</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="border-t border-muted-border">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm hover:bg-muted">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-base-light">Login</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-accent px-3 py-2 text-sm font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-base">Register</a>
            @endauth
        </div>

        {{-- Mobile: Cart + hamburger --}}
        <div class="flex items-center gap-2 md:hidden">
            <a href="{{ route('cart.index') }}" class="relative p-2 text-white" aria-label="Cart">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                @if(isset($cartCount) && $cartCount > 0)
                    <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-accent text-xs font-bold text-white">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                @endif
            </a>
            <button type="button" id="nav-mobile-toggle" aria-label="Open menu" aria-expanded="false" class="rounded-lg p-2 text-white hover:bg-base-light focus:outline-none focus:ring-2 focus:ring-accent">
                <svg id="nav-icon-open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg id="nav-icon-close" class="hidden h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="nav-mobile-menu" class="hidden border-t border-base-light bg-base md:hidden" aria-hidden="true">
        <div class="space-y-1 px-4 py-3">
            <form action="{{ route('shop.index') }}" method="GET" class="pb-3">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search..." class="w-full rounded-lg border border-base-light bg-base-light px-3 py-2 text-sm text-white placeholder-gray-400">
            </form>
            <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-white hover:bg-base-light">Home</a>
            <a href="{{ route('shop.index') }}" class="block rounded-lg px-3 py-2 text-white hover:bg-base-light">Shop</a>
            @if(isset($categories) && $categories->isNotEmpty())
                @foreach($categories as $cat)
                    <a href="{{ route('shop.category', $cat->slug) }}" class="block rounded-lg px-3 py-2 text-white hover:bg-base-light">{{ $cat->name }}</a>
                @endforeach
            @endif
            <a href="{{ route('contact') }}" class="block rounded-lg px-3 py-2 text-white hover:bg-base-light">Contact</a>
            @auth
                <a href="{{ route('account.orders') }}" class="block rounded-lg px-3 py-2 text-white hover:bg-base-light">My Orders</a>
                @if(Auth::user()->is_admin ?? false)
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-white hover:bg-base-light">Admin</a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-white hover:bg-base-light">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-white hover:bg-base-light">Login</a>
                <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2 text-white hover:bg-base-light">Register</a>
            @endauth
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('nav-mobile-toggle');
    var menu = document.getElementById('nav-mobile-menu');
    var iconOpen = document.getElementById('nav-icon-open');
    var iconClose = document.getElementById('nav-icon-close');
    if (toggle && menu) {
        toggle.addEventListener('click', function() {
            var isOpen = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            toggle.setAttribute('aria-expanded', !isOpen);
            if (iconOpen && iconClose) {
                iconOpen.classList.toggle('hidden', !isOpen);
                iconClose.classList.toggle('hidden', isOpen);
            }
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-dropdown]').forEach(function(container) {
        var toggle = container.querySelector('[data-dropdown-toggle]');
        var menu = container.querySelector('[data-dropdown-menu]');
        if (!toggle || !menu) return;
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            var isOpen = !menu.classList.contains('hidden');
            document.querySelectorAll('[data-dropdown-menu]').forEach(function(m) { m.classList.add('hidden'); });
            document.querySelectorAll('[data-dropdown-toggle]').forEach(function(t) { t.setAttribute('aria-expanded', 'false'); });
            if (!isOpen) {
                menu.classList.remove('hidden');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });
    });
    document.addEventListener('click', function() {
        document.querySelectorAll('[data-dropdown-menu]').forEach(function(m) { m.classList.add('hidden'); });
        document.querySelectorAll('[data-dropdown-toggle]').forEach(function(t) { t.setAttribute('aria-expanded', 'false'); });
    });
});
</script>
