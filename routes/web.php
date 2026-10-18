<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------- Site public
Route::get('/', HomeController::class)->name('home');

Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
Route::get('/videos/{video}', [VideoController::class, 'show'])->name('videos.show');
Route::post('/videos/{video}/like', [VideoController::class, 'like'])->middleware('throttle:30,1')->name('videos.like');
Route::post('/videos', [VideoController::class, 'store'])->middleware('throttle:5,10')->name('videos.store');

Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::post('/newsletter', [NewsletterController::class, 'store'])->middleware('throttle:5,1')->name('newsletter.store');

// ---------------------------------------------------------------- Administration
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/connexion', [Admin\AuthController::class, 'create'])->middleware('guest')->name('login');
    Route::post('/connexion', [Admin\AuthController::class, 'store'])->middleware(['guest', 'throttle:6,1']);
    Route::post('/deconnexion', [Admin\AuthController::class, 'destroy'])->middleware('auth')->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::patch('videos/{video}/publication', [Admin\VideoController::class, 'togglePublished'])->name('videos.toggle');
        Route::resource('videos', Admin\VideoController::class)->except('show');
        Route::resource('portfolio', Admin\PortfolioController::class)->except('show')->parameters(['portfolio' => 'item']);
        Route::resource('equipe', Admin\TeamController::class)->except('show')->parameters(['equipe' => 'member'])->names('team');

        Route::get('messages', [Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
        Route::delete('messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('abonnes', [Admin\SubscriberController::class, 'index'])->name('subscribers.index');
        Route::get('abonnes/export', [Admin\SubscriberController::class, 'export'])->name('subscribers.export');
        Route::delete('abonnes/{subscriber}', [Admin\SubscriberController::class, 'destroy'])->name('subscribers.destroy');
    });
});
