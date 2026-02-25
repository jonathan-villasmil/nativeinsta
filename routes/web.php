<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HashtagController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StoryController;
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


// Hashtags (public)
Route::get('/hashtags/{tag}', [HashtagController::class, 'show'])->name('hashtags.show');

// Username availability check (public)
Route::get('/check-username', function (\Illuminate\Http\Request $request) {
    $username = $request->input('username', '');
    if (strlen($username) < 3) {
        return response()->json(['available' => null, 'message' => 'Mínimo 3 caracteres']);
    }
    if (!preg_match('/^[a-zA-Z0-9_.]+$/', $username)) {
        return response()->json(['available' => false, 'message' => 'Solo letras, números, _ y .']);
    }
    $taken = \App\Models\User::where('username', $username)->exists();
    return response()->json([
        'available' => !$taken,
        'message'   => $taken ? 'Nombre de usuario ya en uso' : 'Disponible ✓',
    ]);
})->name('check-username');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Feed
    Route::get('/feed', [FeedController::class, 'index'])->name('feed');

    // Explore
    Route::get('/explore', [PostController::class, 'explore'])->name('explore');

    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Comment likes
    Route::post('/comment-likes/{comment}', [CommentLikeController::class, 'toggle'])->name('comment-likes.toggle');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

    // Stories
    Route::get('/stories/{user}', [StoryController::class, 'show'])->name('stories.show');
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
    Route::delete('/stories/{story}', [StoryController::class, 'destroy'])->name('stories.destroy');

    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');

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
    Route::post('/comments/{post}',    [CommentController::class, 'store'])  ->name('comments.store');
    Route::put('/comments/{comment}',  [CommentController::class, 'update']) ->name('comments.update');
    Route::delete('/comments/{comment}',[CommentController::class, 'destroy'])->name('comments.destroy');
});
