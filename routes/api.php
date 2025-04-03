<?php

use App\Http\Controllers\ReservedAccountController;
use App\Http\Controllers\SavingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/verify_email', [UserController::class, 'verifyEmail']);
Route::post('/resend_otp', [UserController::class, 'resendOtpEmail']);
Route::get('/profile', [UserController::class, 'profileDetails']);
Route::post('/forgot_password', [UserController::class, 'forgotPassword']);
Route::post('/reset_password', [UserController::class, 'ResetPassword']);
Route::post('/password_reset', [UserController::class, 'PasswordReset']);

Route::group(["middleware" => ['auth:sanctum', "is_admin"]], function () {
    Route::get('/profile', [UserController::class, 'profileDetails']);
    Route::post('/update_profile', [UserController::class, 'UpdateprofileDetails']);
    Route::get('/bank_details', [ReservedAccountController::class, 'getUniqueBankAccount']);
    Route::get('/savings', [SavingController::class, 'getSaving']);
    Route::get('/get_wallet', [WalletController::class, 'getUserWallet']);
    Route::post('/logout', [UserController::class, 'logout']);
});
