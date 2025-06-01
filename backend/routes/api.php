<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ForumPostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DownloadController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::post('/register', [ApiController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [ApiController::class, 'login'])->middleware('throttle:10,1');

Route::post('/forgot-password', [ApiController::class, 'forgotPassword']);
Route::post('/reset-password', [ApiController::class, 'resetPassword']);


Route::group([
    "middleware" => "auth:api"
], function () {
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::get('/logout', [ApiController::class, 'logout']);
    
    Route::get('/getUserProfile', [UserController::class, 'getUserProfile']);
    Route::post('/updateProfile', [UserController::class, 'updateProfile']);
    Route::delete('/deleteProfile', [UserController::class, 'deleteProfile']);
    
    // Adminam
    Route::resource('users', UserController::class)->only(['index', 'show', 'update', 'destroy']);
    
    Route::resource('forum-posts', ForumPostController::class)->except(['create', 'edit']);
    
    
    Route::resource('comments', CommentController::class)->except(['create', 'edit']);
    

    Route::post('/uploadDownloadableFile', [DownloadController::class, 'uploadFile']);
    Route::post('/deleteDownloadableFile', [DownloadController::class, 'deleteFile']);
   
});
Route::get('/getDownloadableFiles', [DownloadController::class, 'getDownloadableFiles']);
Route::get('/download/{filename}', [DownloadController::class, 'downloadFile'])->name('download.file');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset']);