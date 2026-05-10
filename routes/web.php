<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SearchController as AdminSearchController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('shop', [ProductController::class, 'index'])->name('products.index');
Route::get('shop/category/{categorySlug}', [ProductController::class, 'index'])->name('products.category');
Route::get('shop/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::post('shop/{product:slug}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::post('newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

Route::get('about-us', fn () => view('pages.info', [
    'title' => 'About Us',
    'eyebrow' => 'Information',
    'heading' => 'About Us',
    'lead' => 'A trusted trading post for witchers, merchants, scholars and travelers who need reliable gear before the next contract.',
    'sections' => [
        [
            'title' => 'Who We Are',
            'body' => 'We gather weapons, armor, alchemy supplies and rare trophies from dependable makers across the Continent. Every item in the catalogue is selected for practical use, fair pricing and clear origin.',
        ],
        [
            'title' => 'Our Promise',
            'body' => 'The shop keeps orders simple: clear product details, secure checkout, careful packing and fast dispatch through trusted couriers.',
        ],
    ],
]))->name('pages.about');

Route::get('merchant-contracts', fn () => view('pages.info', [
    'title' => 'Merchant Contracts',
    'eyebrow' => 'Information',
    'heading' => 'Merchant Contracts',
    'lead' => 'We work with blacksmiths, herbalists, armorers and collectors who can supply goods fit for dangerous roads.',
    'sections' => [
        [
            'title' => 'Partnership Terms',
            'body' => 'Suppliers may offer finished gear, ingredients, card collections or documented trophies. Each contract is reviewed for quality, provenance and delivery reliability before products enter the catalogue.',
        ],
        [
            'title' => 'Trade Requests',
            'body' => 'For larger batches, limited items or regional goods, contact our trade desk with product details, quantity, expected price and available dispatch dates.',
        ],
    ],
]))->name('pages.contracts');

Route::get('delivery-couriers', fn () => view('pages.info', [
    'title' => 'Delivery & Couriers',
    'eyebrow' => 'Information',
    'heading' => 'Delivery & Couriers',
    'lead' => 'Orders are packed with care and handed to couriers suited for the route, cargo type and destination.',
    'sections' => [
        [
            'title' => 'Delivery Options',
            'body' => 'Standard courier delivery is available for most goods. Fragile alchemy items and heavy armor may require special handling, which is shown during checkout before the order is confirmed.',
        ],
        [
            'title' => 'Dispatch',
            'body' => 'Available stock is usually prepared quickly after payment confirmation. Delivery details are saved with the order so registered customers can review their purchases later.',
        ],
    ],
]))->name('pages.delivery');

Route::get('returns-policy', fn () => view('pages.info', [
    'title' => 'Returns Policy',
    'eyebrow' => 'Information',
    'heading' => 'Returns Policy',
    'lead' => 'If the delivered product is wrong, damaged or not as described, the order can be reviewed for return or replacement.',
    'sections' => [
        [
            'title' => 'Return Conditions',
            'body' => 'Products should be returned unused, complete and safely packed. Consumables, opened alchemy supplies and custom contract items may be excluded unless they arrived damaged.',
        ],
        [
            'title' => 'How To Start',
            'body' => 'Send the order number, contact details and a short description of the issue. We will confirm the next step and the suitable return route.',
        ],
    ],
]))->name('pages.returns');

Route::get('authenticity-guarantee', fn () => view('pages.info', [
    'title' => 'Authenticity Guarantee',
    'eyebrow' => 'Information',
    'heading' => 'Authenticity Guarantee',
    'lead' => 'Rare gear, trophies and collectibles are checked before listing so customers know what they are buying.',
    'sections' => [
        [
            'title' => 'Product Checks',
            'body' => 'We verify product descriptions, images, category placement and key attributes such as rarity, school, material or origin whenever that information is available.',
        ],
        [
            'title' => 'Image Accuracy',
            'body' => 'Product photos are stored with the item and can be updated by administrators. If a product image is removed, the file is physically deleted from storage as well.',
        ],
    ],
]))->name('pages.authenticity');

Route::get('cart', [CartController::class, 'show'])->name('cart.show');
Route::post('cart', [CartController::class, 'add'])->name('cart.add');
Route::patch('cart/items/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('cart/items/{item}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('checkout/success/{order:order_number}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('admin/login', [AdminLoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('dashboard', fn () => redirect()->route('account.profile'))->name('dashboard');

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'profile'])->name('profile');
        Route::get('orders', [AccountController::class, 'orders'])->name('orders');
        Route::get('wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
    });
    Route::post('wishlist/toggle/{product:id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class)->except('show');
    Route::post('products/{product}/images', [AdminProductController::class, 'uploadImage'])->name('products.images.upload');
    Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order:order_number}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order:order_number}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('search', AdminSearchController::class)->name('search');
});
