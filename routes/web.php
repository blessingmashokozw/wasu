<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Buyer\BuyerReportController;
use App\Http\Controllers\Buyer\DeliveryController as BuyerDeliveryController;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Seller\DeliveryController as SellerDeliveryController;
use App\Http\Controllers\Seller\ListingController;
use App\Http\Controllers\Seller\ReportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/catalogue', [CatalogueController::class, 'index'])->name('catalogue.index');
Route::get('/listings/{listing}', [CatalogueController::class, 'show'])->name('catalogue.show');

// Payment routes (no auth required for callbacks)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/fake/redirect', [PaymentController::class, 'fakeRedirect'])->name('payment.fake.redirect');
Route::get('/payment/fake/success', [PaymentController::class, 'fakeSuccess'])->name('payment.fake.success');
Route::get('/payment/fake/fail', [PaymentController::class, 'fakeFail'])->name('payment.fake.fail');

Route::get('/', function () {
    return redirect()->route('catalogue.index');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Messages (available to buyers and sellers)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/show', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // Admin routes
    Route::middleware('role:Administrator')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/sellers', [SellerController::class, 'index'])->name('sellers.index');
        Route::get('/sellers/create', [SellerController::class, 'create'])->name('sellers.create');
        Route::post('/sellers', [SellerController::class, 'store'])->name('sellers.store');
        Route::post('/sellers/{seller}/verify', [SellerController::class, 'verify'])->name('sellers.verify');
        Route::post('/sellers/{seller}/reject', [SellerController::class, 'reject'])->name('sellers.reject');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Seller routes
    Route::middleware('role:Seller')->prefix('seller')->name('seller.')->group(function () {
        Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
        Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
        Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
        Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
        Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
        Route::patch('/listings/{listing}/status', [ListingController::class, 'updateStatus'])->name('listings.status');
        Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');

        Route::get('/deliveries', [SellerDeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/{delivery}', [SellerDeliveryController::class, 'show'])->name('deliveries.show');
        Route::put('/deliveries/{delivery}', [SellerDeliveryController::class, 'update'])->name('deliveries.update');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // Buyer routes
    Route::middleware('role:Buyer')->prefix('buyer')->name('buyer.')->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::get('/deliveries', [BuyerDeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/{delivery}', [BuyerDeliveryController::class, 'show'])->name('deliveries.show');
        Route::post('/deliveries/{delivery}/confirm', [BuyerDeliveryController::class, 'confirmReceipt'])->name('deliveries.confirm');

        // Review routes
        Route::get('/orders/{order}/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
        Route::post('/orders/{order}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::get('/reports', [BuyerReportController::class, 'index'])->name('reports.index');
    });

    // Review routes (authenticated users can edit/delete their own reviews)
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

require __DIR__.'/auth.php';
