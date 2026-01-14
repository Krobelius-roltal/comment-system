<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\VideoPostController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // News routes
    Route::post('/news', [NewsController::class, 'store']);
    Route::get('/news/{id}', [NewsController::class, 'show']);

    // Video Posts routes
    Route::post('/video-posts', [VideoPostController::class, 'store']);
    Route::get('/video-posts/{id}', [VideoPostController::class, 'show']);

    // Comments routes
    Route::get('/comments', [CommentController::class, 'index']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::get('/comments/{id}', [CommentController::class, 'show']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::patch('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
});
