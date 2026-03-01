<footer class="bg-dark text-light py-5 mt-auto">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="fw-bold">{{ config('app.name') }}</h5>
                <p class="small text-secondary">Quality sportswear for everyone. Free delivery, easy returns.</p>
            </div>
            <div class="col-md-2">
                <h6 class="fw-bold">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('shop.index') }}" class="text-secondary text-decoration-none">Shop</a></li>
                    <li><a href="{{ route('contact') }}" class="text-secondary text-decoration-none">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <h6 class="fw-bold">Support</h6>
                <ul class="list-unstyled small">
                    <li><span class="text-secondary">Free Delivery</span></li>
                    <li><span class="text-secondary">30 Day Return</span></li>
                    <li><span class="text-secondary">24/7 Support</span></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-bold">Newsletter</h6>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="email" name="email" class="form-control form-control-sm" placeholder="Your email" required>
                    <button type="submit" class="btn btn-outline-light btn-sm">Subscribe</button>
                </form>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="small text-secondary text-center">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</footer>
