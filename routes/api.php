<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\BlogRateController;
use App\Http\Controllers\Api\BlogCommentController;
use App\Http\Controllers\Api\MemberController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API Laravel hoạt động',
    ]);
});

Route::get('/blog', [BlogController::class, 'listBlogs']);
Route::get('/blog/{id}', [BlogController::class, 'detail']);
Route::post('/blog/{id}/rate', [BlogRateController::class, 'rate']);
Route::post('/blog/{id}/comment', [BlogCommentController::class, 'comment']);
Route::post('/register', [MemberController::class, 'register']);
Route::post('/login', [MemberController::class, 'login']);
