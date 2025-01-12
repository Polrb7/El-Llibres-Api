<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Users
    Route::apiResource('users', UserController::class);

    // Likes
    Route::apiResource('likes', LikeController::class);

    // Auth
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');

    // Books
    Route::apiResource('books', BookController::class);
    Route::get('/users/{user}/books', [BookController::class, 'showBooksByUser']);

    // Reviews
    Route::apiResource('reviews', ReviewController::class);
    Route::get('/books/{book}/reviews', [ReviewController::class, 'showReviewsInBook']);

    // Comments
    Route::apiResource('comments', CommentController::class);
    Route::get('/reviews/{review}/comments', [CommentController::class, 'showCommentsInReview']);
});
