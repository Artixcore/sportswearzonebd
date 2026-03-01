<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PosController as AdminPosController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SaleController as AdminSaleController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/category/{slug}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');
Route::get('/checkout/success/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/success', [OrderController::class, 'success'])->name('orders.success');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::get('products/{product}/variants', [AdminProductVariantController::class, 'index'])->name('products.variants.index');
    Route::get('products/{product}/variants/create', [AdminProductVariantController::class, 'create'])->name('products.variants.create');
    Route::post('products/{product}/variants', [AdminProductVariantController::class, 'store'])->name('products.variants.store');
    Route::get('products/{product}/variants/{variant}/edit', [AdminProductVariantController::class, 'edit'])->name('products.variants.edit');
    Route::put('products/{product}/variants/{variant}', [AdminProductVariantController::class, 'update'])->name('products.variants.update');
    Route::delete('products/{product}/variants/{variant}', [AdminProductVariantController::class, 'destroy'])->name('products.variants.destroy');
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::get('inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/adjust', [AdminInventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::post('inventory/adjust', [AdminInventoryController::class, 'storeAdjust'])->name('inventory.storeAdjust');
    Route::get('inventory/product/{product}', [AdminInventoryController::class, 'productHistory'])->name('inventory.productHistory');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('orders/{order}/delivery-advance', [AdminOrderController::class, 'updateDeliveryAdvance'])->name('orders.updateDeliveryAdvance');
    Route::post('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('pos', [AdminPosController::class, 'index'])->name('pos.index');
    Route::get('pos/search-products', [AdminPosController::class, 'searchProducts'])->name('pos.searchProducts');
    Route::post('pos/checkout', [AdminPosController::class, 'checkout'])->name('pos.checkout');
    Route::get('pos/receipt/{sale}', [AdminPosController::class, 'receipt'])->name('pos.receipt');
    Route::get('sales', [AdminSaleController::class, 'index'])->name('sales.index');
    Route::get('sales/{sale}', [AdminSaleController::class, 'show'])->name('sales.show');
    Route::resource('customers', AdminCustomerController::class);
    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export-csv', [AdminReportController::class, 'exportCsv'])->name('reports.exportCsv');
    Route::get('activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [AdminSettingsController::class, 'update'])->name('settings.update');
});
