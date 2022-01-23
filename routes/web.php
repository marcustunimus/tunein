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

Route::get('/posts/{post}', [PostController::class, 'index'])->where('post', '^[0-9]+$')->name('view.post');

Route::post('/posts/{post}/likesInfo', [PostController::class, 'likesInfo'])->where('post', '^[0-9]+$')->name('post.likesInfo');

Route::get('/profile/{user:username}', [ProfileController::class, 'index'])->whereAlphaNumeric('user')->name('profile');

Route::post('/profile/{user:username}/followersInfo', [ProfileController::class, 'followersInfo'])->whereAlphaNumeric('user')->name('profile.followersInfo');

Route::post('/posts/{post}/viewComments', [PostController::class, 'viewComments'])->where('post', '^[0-9]+$')->name('view.post.comments');

Route::post('/posts/{post}/viewMoreComments', [PostController::class, 'viewMoreComments'])->where('post', '^[0-9]+$')->name('view.post.more.comments');

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

    Route::post('/profile/{user:username}/follow', [ProfileController::class, 'follow'])->whereAlphaNumeric('user')->name('profile.follow');

    Route::post('/posts/create', [PostController::class, 'store'])->name('post.create');

    Route::group(['middleware' => ['checkUser']], function () {
        Route::get('/post/{post}/edit', [PostController::class, 'edit'])->where('post', '^[0-9]+$')->name('post.edit');
        Route::patch('/post/{post}', [PostController::class, 'update'])->where('post', '^[0-9]+$')->name('post.update');
        Route::delete('/post/{post}', [PostController::class, 'destroy'])->where('post', '^[0-9]+$')->name('post.destroy');

        Route::get('/profile/{user:username}/settings', [ProfileController::class, 'settings'])->whereAlphaNumeric('user')->name('profile.settings');
        Route::post('/profile/{user:username}/settings', [ProfileController::class, 'storeSettings'])->whereAlphaNumeric('user')->name('profile.settings.store');
    });

    Route::post('/posts/{post}/like', [PostController::class, 'like'])->where('post', '^[0-9]+$')->name('post.like');

    Route::post('/posts/{post}/comment', [PostController::class, 'store'])->where('post', '^[0-9]+$')->name('post.comment');

    Route::post('/posts/{post}/bookmark', [PostController::class, 'bookmark'])->where('post', '^[0-9]+$')->name('post.bookmark');

    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');
});