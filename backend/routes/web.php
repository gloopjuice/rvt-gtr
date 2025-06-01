<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/reset-password/{token}', function ($token) {
    return view('reset-password', ['token' => $token]);
})->name('password.reset');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-clickjacking', function () {
    return 'Clickjacking Protection Middleware is working!';
});

use Illuminate\Support\Facades\Mail;

Route::get('/test-mail', function () {
    Mail::raw('This is a test email from Laravel', function ($message) {
        $message->to('girts.gagaini@gmail.com')
                ->subject('Test Email');
    });

    return 'Email sent!';
});

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/password/reset/success', function () {
    return view('password-reset-success');
})->name('password.reset.success');