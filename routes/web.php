<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\EducationController;
use App\Http\Controllers\admin\ExperienceController;
use App\Http\Controllers\admin\SkillController;
use App\Http\Controllers\HomeController;
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
});

Auth::routes(['register' => false]);

//Admin Side Pages
Route::group(['middleware' => 'auth'], function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index']);
        Route::get('index', [AdminController::class, 'index']);
        Route::get('profile', [AdminController::class, 'profile']);
        Route::post('profile/update/{id}', [AdminController::class, 'updateProfile']);

        Route::prefix('experience')->group(function () {
            Route::get('', [ExperienceController::class, 'index']);
            Route::get('create', [ExperienceController::class, 'create']);
            Route::post('store', [ExperienceController::class, 'store']);
            Route::get('edit/{id}', [ExperienceController::class, 'edit']);
            Route::post('update/{id}', [ExperienceController::class, 'update']);
            Route::delete('delete/{id}', [ExperienceController::class, 'destroy']);
        });
        Route::prefix('education')->group(function () {
            Route::get('', [EducationController::class, 'index']);
            Route::get('create', [EducationController::class, 'create']);
            Route::post('store', [EducationController::class, 'store']);
            Route::get('edit/{id}', [EducationController::class, 'edit']);
            Route::post('update/{id}', [EducationController::class, 'update']);
            Route::delete('delete/{id}', [EducationController::class, 'destroy']);
        });
        Route::prefix('skill')->group(function () {
            Route::get('', [SkillController::class, 'index']);
            Route::post('store', [SkillController::class, 'store']);
            Route::post('update/{id}', [SkillController::class, 'update']);
            Route::delete('delete/{id}', [SkillController::class, 'destroy']);
        });
    });
});


