<?php

use App\Http\Controllers\Admin\ContributionController;
use App\Http\Controllers\Admin\SavingController as AdminSavingController;
use App\Http\Controllers\Admin\InterestController as AdminInterestController;
use App\Http\Controllers\Admin\UserSettingsController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReservedAccountController;
use App\Http\Controllers\SavingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WebHook\webHookController;

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

Route::group(["middleware" => ['auth:sanctum']], function () {
    Route::get('/profile', [UserController::class, 'profileDetails']);
    Route::post('/update_profile', [UserController::class, 'UpdateprofileDetails']);
    Route::get('/bank_details', [ReservedAccountController::class, 'getUniqueBankAccount']);
    Route::get('/savings', [SavingController::class, 'getSaving']);
    Route::get('/transfer_deposit', [SavingController::class, 'getTransferDeposit']);

    Route::get('/get_wallet', [WalletController::class, 'getUserWallet']);
    Route::get('/bank_account', [UserController::class, 'getUserAccount']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/request_loan', [LoanController::class, 'requestLoan']);
});
// Route::group(["middleware" => ['auth:sanctum', "is_admin"]], function () {
//     Route::get('/admin/users/{id}', [UserSettingsController::class, 'editUser']);
// });
Route::put('/admin/users/{id}', [UserSettingsController::class, 'editUser']);
Route::delete('/admin/users/{id}', [UserSettingsController::class, 'deleteUser']);
Route::post('/admin/deposit', [AdminSavingController::class, 'savedeposit']);
Route::post('/admin/contribution', [ContributionController::class, 'saveContribution']);
Route::post('/payment_webhooks', [webHookController::class, 'paymentWebhook']);
Route::get('admin/get_interest', [AdminInterestController::class, 'getInterest']);
Route::post('admin/add_interest', [AdminInterestController::class, 'createInterest']);
Route::put('admin/update_interest/{id}', [AdminInterestController::class, 'updateInterest']);
Route::delete('admin/delete_interest/{id}', [AdminInterestController::class, 'deleteInterest']);
