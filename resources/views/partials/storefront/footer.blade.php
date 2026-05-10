<footer class="site-footer">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="footer-logo">
                    <span class="brand-name">{{ config('app.name') }}</span>
                    <span class="brand-sub">Novigrad's Finest Trading Post</span>
                </div>
                <p class="footer-desc">Serving witchers, soldiers, merchants and scholars since the reign of King Foltest. Quality gear and rare ingredients, delivered anywhere on the Continent.</p>
                <div class="footer-socials">
                    <a href="#" class="social-link" aria-label="Follow us on Twitter"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                    <a href="#" class="social-link" aria-label="Follow us on Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                    <a href="#" class="social-link" aria-label="Join our Discord"><i class="bi bi-discord" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <h3 class="footer-heading">Shop</h3>
                <ul class="footer-links">
                    @forelse (($navCategories ?? collect()) as $category)
                        <li><a href="{{ route('products.category', ['categorySlug' => $category->slug]) }}">{{ $category->name }}</a></li>
                    @empty
                        <li><a href="{{ route('products.index') }}">Browse Catalogue</a></li>
                    @endforelse
                </ul>
            </div>
            <div class="col-sm-6 col-lg-2">
                <h3 class="footer-heading">Information</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('pages.about') }}">About Us</a></li>
                    <li><a href="{{ route('pages.contracts') }}">Merchant Contracts</a></li>
                    <li><a href="{{ route('pages.delivery') }}">Delivery &amp; Couriers</a></li>
                    <li><a href="{{ route('pages.returns') }}">Returns Policy</a></li>
                    <li><a href="{{ route('pages.authenticity') }}">Authenticity Guarantee</a></li>
                </ul>
            </div>
            <div class="col-sm-6 col-lg-2">
                <h3 class="footer-heading">Account</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('login') }}">Sign In</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                    <li><a href="{{ route('cart.show') }}">Your Cart</a></li>
                    <li><a href="#">Order History</a></li>
                    <li><a href="#">Wishlist</a></li>
                </ul>
            </div>
            <div class="col-sm-6 col-lg-2">
                <h3 class="footer-heading">Contact</h3>
                <address>
                    <ul class="footer-links">
                        <li><a href="#"><i class="bi bi-geo-alt" aria-hidden="true"></i> 7 Hierarch Square, Novigrad</a></li>
                        <li><a href="mailto:trade@whitewolf.gg"><i class="bi bi-envelope" aria-hidden="true"></i> trade@whitewolf.gg</a></li>
                        <li><a href="#"><i class="bi bi-headset" aria-hidden="true"></i> Support Portal</a></li>
                    </ul>
                </address>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="footer-bottom-text">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
                <div class="col-md-6">
                    <nav class="footer-bottom-links" aria-label="Legal links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Sale</a>
                        <a href="#">Cookie Settings</a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</footer>
