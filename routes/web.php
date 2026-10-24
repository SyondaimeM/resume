<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\admin\PortfolioContentController;
use App\Http\Controllers\admin\PortfolioProfileController;
use App\Http\Controllers\ClientErrorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::prefix('')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/experience', [HomeController::class, 'experience'])->name('experience');
    Route::get('/projects', [HomeController::class, 'projects'])->name('projects');
    Route::get('/education', [HomeController::class, 'education'])->name('education');
    Route::get('/skills', [HomeController::class, 'skills'])->name('skills');
    Route::get('/contact/captcha', [HomeController::class, 'captcha'])
        ->middleware('throttle:contact-captcha')->name('contact.captcha');
    Route::post('/message', [HomeController::class, 'message'])
        ->middleware('throttle:contact-message')->name('message');
    Route::post('/client-errors', [ClientErrorController::class, 'store'])
        ->middleware('throttle:3,1')->name('client-errors');
});

Auth::routes(['register' => false]);

//Admin Side Pages
Route::group(['middleware' => 'auth'], function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index']);
        Route::get('index', [AdminController::class, 'index']);
        Route::get('profile', [AdminController::class, 'profile']);
        Route::post('profile/update/{id}', [AdminController::class, 'updateProfile']);
        Route::get('portfolio', [PortfolioProfileController::class, 'edit']);
        Route::post('portfolio', [PortfolioProfileController::class, 'update']);

        foreach (['experience', 'education', 'skill', 'project'] as $kind) {
            Route::get($kind, [PortfolioContentController::class, 'index'])->defaults('kind', $kind);
            Route::get($kind.'/create', [PortfolioContentController::class, 'create'])->defaults('kind', $kind);
            Route::post($kind.'/store', [PortfolioContentController::class, 'store'])->defaults('kind', $kind);
            Route::get($kind.'/edit/{id}', [PortfolioContentController::class, 'edit'])->defaults('kind', $kind);
            Route::post($kind.'/update/{id}', [PortfolioContentController::class, 'update'])->defaults('kind', $kind);
            Route::delete($kind.'/delete/{id}', [PortfolioContentController::class, 'destroy'])->defaults('kind', $kind);
        }
    });
});
