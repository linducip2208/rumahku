<?php

use App\Http\Controllers\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Agent\LeadController as AgentLeadController;
use App\Http\Controllers\Agent\ListingController as AgentListingController;
use App\Http\Controllers\Agent\OrderController as AgentOrderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayController;
use App\Http\Controllers\Public\AgentController;
use App\Http\Controllers\Public\ArticleController;
use App\Http\Controllers\Public\AuctionController;
use App\Http\Controllers\Public\AuctionShowController;
use App\Http\Controllers\Public\BidController;
use App\Http\Controllers\Public\CompareController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\FavoriteController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\InfoController;
use App\Http\Controllers\Public\KprController;
use App\Http\Controllers\Public\LeadController;
use App\Http\Controllers\Public\MessageController;
use App\Http\Controllers\Public\ProjectController;
use App\Http\Controllers\Public\PropertyController;
use App\Http\Controllers\Public\SeoController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\ServiceShowController;
use App\Http\Controllers\SitemapController;
use App\Models\Package;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/properti', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properti/{slug}', [PropertyController::class, 'show'])->name('properties.show');

Route::post('/leads', [LeadController::class, 'store'])->middleware('throttle:30,1')->name('leads.store');

Route::post('/messages', [MessageController::class, 'store'])
    ->middleware(['auth', 'throttle:30,1'])
    ->name('messages.store');

Route::get('/login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware(['guest', 'throttle:10,1']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'register'])->middleware(['guest', 'throttle:10,1']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/listings', [AgentListingController::class, 'index'])->name('agent.listings.index');
    Route::get('/dashboard/listings/create', [AgentListingController::class, 'create'])->name('agent.listings.create');
    Route::post('/dashboard/listings', [AgentListingController::class, 'store'])->name('agent.listings.store');
    Route::get('/dashboard/listings/{listing}/edit', [AgentListingController::class, 'edit'])->name('agent.listings.edit');
    Route::put('/dashboard/listings/{listing}', [AgentListingController::class, 'update'])->name('agent.listings.update');
    Route::delete('/dashboard/listings/{listing}', [AgentListingController::class, 'destroy'])->name('agent.listings.destroy');
    Route::post('/dashboard/listings/{listing}/sold', [AgentListingController::class, 'sold'])->name('agent.listings.sold');
    Route::post('/dashboard/listings/{listing}/relist', [AgentListingController::class, 'relist'])->name('agent.listings.relist');
    Route::get('/dashboard/orders', [AgentOrderController::class, 'index'])->name('agent.orders.index');
    Route::get('/dashboard/leads', [AgentLeadController::class, 'index'])->name('dashboard.leads.index');
    Route::put('/dashboard/leads/{lead}', [AgentLeadController::class, 'update'])->name('dashboard.leads.update');
});

Route::get('/proyek', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/proyek/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/agen', [AgentController::class, 'index'])->name('agents.index');
Route::get('/agen/{id}', [AgentController::class, 'show'])->whereNumber('id')->name('agents.show');

Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{slug}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/harga', function () {
    $packages = Package::query()->orderBy('price')->get();

    return view('pages.pricing', compact('packages'));
})->name('pricing');

Route::post('/orders', [OrderController::class, 'store'])->middleware('auth')->name('orders.store');

Route::middleware('auth')->group(function () {
    Route::get('/pay/{order}', [PayController::class, 'pay'])->name('pay.show');
});
Route::post('/pay/callback/{driver}', [PayController::class, 'callback'])->name('pay.callback');

Route::get('/bandingkan', [CompareController::class, 'index'])->name('compare.index');
Route::post('/bandingkan/{property}', [CompareController::class, 'add'])->name('compare.add');
Route::delete('/bandingkan/{property}', [CompareController::class, 'remove'])->name('compare.remove');

Route::post('/favorit/{property}', [FavoriteController::class, 'toggle'])->middleware('auth')->name('favorites.toggle');

Route::get('/lelang', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/lelang/{auction}', [AuctionShowController::class, 'show'])->name('auctions.show');
Route::post('/lelang/{auction}/bid', [BidController::class, 'store'])->middleware('auth')->name('auctions.bids.store');
Route::get('/jasa', [ServiceController::class, 'index'])->name('services.index');
Route::get('/jasa/{service}', [ServiceShowController::class, 'show'])->name('services.show');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::post('/kpr', [KprController::class, 'store'])->middleware('throttle:30,1')->name('kpr.store');

Route::get('/{page}', [InfoController::class, 'show'])
    ->whereIn('page', ['tentang', 'bantuan', 'privasi', 'syarat'])
    ->name('info.show');

Route::get('/kontak', [ContactController::class, 'index'])->name('contact');
Route::post('/kontak', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact.store');

Route::get('/{listing}/{type}/{city}', [SeoController::class, 'landing'])
    ->whereIn('listing', ['jual', 'sewa'])
    ->name('seo.landing');
