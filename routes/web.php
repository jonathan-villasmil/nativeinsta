<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Public redirect
Route::get('/', function () {
    return auth()->check() ? redirect()->route('feed') : redirect()->route('login');
});

// Serve storage files directly (needed for NativePHP embedded browser)
Route::get('/img/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    $mime = mime_content_type($fullPath);
    return response()->file($fullPath, ['Content-Type' => $mime]);
})->where('path', '.*')->name('img');


// Authenticated routes
Route::middleware('auth')->group(function () {
    // Feed
    Route::get('/feed', [FeedController::class, 'index'])->name('feed');

    // Explore
    Route::get('/explore', [PostController::class, 'explore'])->name('explore');

    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Posts
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

    // Follow
    Route::post('/follow/{user}', [FollowController::class, 'store'])->name('follow.store');
    Route::delete('/follow/{user}', [FollowController::class, 'destroy'])->name('follow.destroy');

    // Likes
    Route::post('/likes/{post}', [LikeController::class, 'toggle'])->name('likes.toggle');

    // Comments
    Route::post('/comments/{post}', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});
