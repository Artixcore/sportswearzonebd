<footer class="bg-base text-white mt-auto">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <h2 class="text-lg font-bold tracking-tight">{{ config('app.name') }}</h2>
                <p class="mt-2 text-sm text-gray-300">Premium men's fashion & sportswear. Free delivery, easy returns.</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-300">Quick Links</h3>
                <ul class="mt-4 space-y-2">
                    <li><a href="{{ route('shop.index') }}" class="text-sm text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-accent">Shop</a></li>
                    <li><a href="{{ route('contact') }}" class="text-sm text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-accent">Contact</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-300">Support</h3>
                <ul class="mt-4 space-y-2 text-sm text-gray-300">
                    <li>Free delivery on orders over ৳1000</li>
                    <li>30 day return</li>
                    <li>24/7 support</li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-300">Newsletter</h3>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mt-4 flex gap-2">
                    @csrf
                    <label for="footer-email" class="sr-only">Email</label>
                    <input type="email" id="footer-email" name="email" required placeholder="Your email"
                           class="min-w-0 flex-1 rounded-lg border border-base-light bg-base-light px-3 py-2 text-sm text-white placeholder-gray-400 focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent">
                    <button type="submit" class="shrink-0 rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-base">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="mt-10 border-t border-base-light pt-8 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</footer>
