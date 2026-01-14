<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\VideoPostController;
use Illuminate\Support\Facades\Route;

Route::apiResource('news', NewsController::class)->only(['index', 'store', 'show']);
Route::apiResource('video-posts', VideoPostController::class)->only(['index', 'store', 'show']);

Route::apiResource('comments', CommentController::class)->only(['store', 'update', 'destroy']);
