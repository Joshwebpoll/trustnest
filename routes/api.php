<?php

use App\Http\Controllers\Admin\AdminCreateUsers;
use App\Http\Controllers\Admin\ContributionController;
use App\Http\Controllers\Admin\SavingController as AdminSavingController;
use App\Http\Controllers\Admin\InterestController as AdminInterestController;
use App\Http\Controllers\Admin\LoanControllerAdmin;
use App\Http\Controllers\Admin\MembersController;
use App\Http\Controllers\Admin\RepaymentController;
use App\Http\Controllers\Admin\UserSettingsController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberContribution;
use App\Http\Controllers\MembersUserController;
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

Route::group(["middleware" => ['auth:sanctum', 'is_user']], function () {
    Route::get('/profile', [UserController::class, 'profileDetails']);
    Route::post('/update_profile', [UserController::class, 'UpdateprofileDetails']);
    Route::get('/bank_details', [ReservedAccountController::class, 'getUniqueBankAccount']);
    Route::get('/savings', [SavingController::class, 'getSaving']);
    Route::get('/transfer_deposit', [SavingController::class, 'getTransferDeposit']);
    Route::get('/user/loan_repayment', [RepaymentController::class, 'repayLoan']);
    Route::get('/get_wallet', [WalletController::class, 'getUserWallet']);
    Route::get('/bank_account', [UserController::class, 'getUserAccount']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/request_loan', [LoanController::class, 'requestLoan']);
    Route::get('/user/get_loan', [LoanController::class, 'getUserLoan']);
    Route::get('/get_members', [MembersUserController::class, 'getMemberDetails']);
    Route::get('/user/get_contribution', [MemberContribution::class, 'getContributions']);
});
Route::group(["middleware" => ['auth:sanctum', "is_admin"]], function () {
    Route::put('/admin/users/{id}', [UserSettingsController::class, 'editUser']);
    Route::delete('/admin/users/{id}', [UserSettingsController::class, 'deleteUser']);
    Route::get('/admin/users_con', [UserSettingsController::class, 'getConUsers']);
    Route::get('/admin/users', [UserSettingsController::class, 'getAllUsers']);
    Route::post('/admin/deposit', [AdminSavingController::class, 'savedeposit']);
    Route::post('/admin/contribution', [ContributionController::class, 'saveContribution']);
    Route::get('/admin/contribution', [ContributionController::class, 'getContribution']);
    Route::get('/admin/get_contribution/{id}', [ContributionController::class, 'getSingleContribution']);

    Route::get('/admin/get_members', [MembersController::class, 'getMemberDetails']);
    Route::get('/admin/get_loan', [LoanControllerAdmin::class, 'getAllLoan']);
    Route::get('/admin/get_loan/{id}', [LoanControllerAdmin::class, 'getSingleLoan']);
    Route::post('/admin/create_loan', [LoanControllerAdmin::class, 'createLoanUser']);
    Route::get('/admin/loan_repayment', [RepaymentController::class, 'repayLoan']);
    Route::get('/admin/get_single/{id}', [RepaymentController::class, 'getSingleRepayment']);
    Route::post('/admin/loan_repayment', [RepaymentController::class, 'createLoanRepayment']);
    Route::post('/payment_webhooks', [webHookController::class, 'paymentWebhook']);
    Route::get('admin/get_interest', [AdminInterestController::class, 'getInterest']);
    Route::post('admin/add_interest', [AdminInterestController::class, 'createInterest']);

    Route::put('admin/update_interest/{id}', [AdminInterestController::class, 'updateInterest']);
    Route::delete('admin/delete_interest/{id}', [AdminInterestController::class, 'deleteInterest']);
    Route::post('admin/create_user', [AdminCreateUsers::class, 'createUsers']);
    Route::get('admin/get_single_user/{id}', [AdminCreateUsers::class, 'getSingleUser']);
    Route::put('admin/approve_loan/{id}', [LoanControllerAdmin::class, 'approveLoan']);
});

Route::get('/admin/excel_contribution', [ContributionController::class, 'exportContribution']);
// Route::put('/admin/users/{id}', [UserSettingsController::class, 'editUser']);
// Route::delete('/admin/users/{id}', [UserSettingsController::class, 'deleteUser']);
// Route::post('/admin/deposit', [AdminSavingController::class, 'savedeposit']);
// Route::post('/admin/contribution', [ContributionController::class, 'saveContribution']);
// Route::get('/admin/contribution', [ContributionController::class, 'getContribution']);
// Route::get('/admin/get_members', [MembersController::class, 'getMemberDetails']);
// Route::get('/admin/get_loan', [LoanControllerAdmin::class, 'getAllLoan']);
// Route::get('/admin/loan_repayment', [RepaymentController::class, 'repayLoan']);
// Route::post('/admin/loan_repayment', [RepaymentController::class, 'createLoanRepayment']);
// Route::post('/payment_webhooks', [webHookController::class, 'paymentWebhook']);
// Route::get('admin/get_interest', [AdminInterestController::class, 'getInterest']);
// Route::post('admin/add_interest', [AdminInterestController::class, 'createInterest']);
// Route::post('admin/create_user', [AdminCreateUsers::class, 'createUsers']);
// Route::put('admin/update_interest/{id}', [AdminInterestController::class, 'updateInterest']);
// Route::delete('admin/delete_interest/{id}', [AdminInterestController::class, 'deleteInterest']);
