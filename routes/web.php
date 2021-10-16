<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\BookmarksController;

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

Route::get('/post/{post}', [PostController::class, 'index'])->name('view_post');

Route::group(['middleware' => ['prevent-back-history', 'guest']], function () {
    Route::get('/', [GuestController::class, 'index'])->name('welcome');

    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'attempt'])->name('login.attempt');
});

Route::group(['middleware' => ['prevent-back-history', 'auth']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/bookmarks', [BookmarksController::class, 'index'])->name('bookmarks');

    Route::get('/explore', [ExploreController::class, 'index'])->name('explore');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    Route::post('/post/create', [PostController::class, 'store'])->name('post.create');

    Route::group(['middleware' => ['checkUser']], function () {
        Route::get('/post/{post}/edit', [PostController::class, 'edit'])->name('post.edit');
        Route::patch('/post/{post}', [PostController::class, 'update'])->name('post.update');
        Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');
    });

    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');
});