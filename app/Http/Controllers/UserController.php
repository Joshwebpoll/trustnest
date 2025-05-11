<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\ResentEmail;
use App\Models\WalletUser;
// use Illuminate\Foundation\Auth\User;
use App\Helper\BankAccount;
use App\Jobs\RegisterEmailJob;
use Illuminate\Http\Request;
use App\Models\AccountDetail;
use App\Mail\registrationEmail;
use App\Models\CpMember;
use App\Models\CpMembers;
use App\Models\UniqueBankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{



    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|unique:users,username|min:3|max:20',
                'password' => 'required|min:6|confirmed',
                'phone_number' => 'required|unique:users',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $otp = rand(100000, 999999);
            $otpExpiresAt = Carbon::now()->addMinutes(5);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                "username" => $request->username,
                "otp_number" => $otp,
                "otp_expires_at" => $otpExpiresAt,
                "phone_number" => $request->phone_number
            ]);
            $membershipNumber = 'MEM' . str_pad(CpMember::count() + 1, 4, '0', STR_PAD_LEFT);
            $id_number = 'MEM-' . strtoupper(uniqid() . mt_rand(1000, 9999));
            CpMember::create([
                'user_id' => $user->id,
                'membership_number' => $membershipNumber,
                'full_name' => $user->name,
                'id_number' => $id_number,
                'phone' => $user->phone_number,
                'email' => $user->email,
                'joining_date' => now(),
                'total_shares' => Crypt::encryptString(0),
                'total_savings' => Crypt::encryptString(0),
                'status' => 'active',
            ]);

            //Mail::to($request->email)->send(new registrationEmail($user));
            RegisterEmailJob::dispatch($request->email, $user);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(["status" => true, "message" => "Registration successfull, Please verify your email to proceed"], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
    public function verifyEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'emailCode' => 'required|string',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }


            $getverificationEmail = User::where('otp_number', $request->emailCode)->first();
            if (!$getverificationEmail) {



                return response()->json([
                    'status' => false,
                    'message' => "Invalid otp, Please try again later"
                ], 404);
            }
            if ($getverificationEmail->otp_number !== $request->emailCode) {
                return response()->json(['message' => 'Invalid OTP'], 400);
            }
            if (Carbon::now()->greaterThan($getverificationEmail->otp_expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => "Otp as expired... Please resend otp to proceed"
                ], 400);
            }
            $getverificationEmail->update([
                "otp_number" => NULL,
                'otp_expires_at' => null,
                "is_verified" => 0
            ]);

            return response()->json([
                'status' => true,
                'message' => "Email verify successfully"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function resendOtpEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                'email' => 'required|string|email',


            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $getUserEmail = User::where('email', $request->email)->first();
            if (!$getUserEmail) {
                return response()->json([
                    'status' => false,
                    'message' => "Invalid email address, No user Exist"
                ], 400);
            }
            $otp = rand(100000, 999999);
            $otpExpiresAt = Carbon::now()->addMinutes(5);

            // $code = new User();
            //   $code->otp_number= $otp;
            //   $code->otp_expires_at= $otpExpiresAt;
            // $code->save();
            $getUserEmail->update([
                "otp_number" => $otp,
                "otp_expires_at" => $otpExpiresAt,
            ]);
            Mail::to($request->email)->send(new registrationEmail($getUserEmail));
            return response()->json([
                'status' => true,
                'message' => "Otp code sent to your email"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // 'username' => 'required|string|max:255',
                'email' => 'required|string|email',
                'password' => 'required|string',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(["status" => false, "message" => "Invalid email or password"], 401);
            }
            if ($user->is_verified !== 0) {
                return response()->json(["status" => false, "message" => "Please verify your email to continue"], 401);
            }
            if ($user->status !== "enable") {
                return response()->json(["status" => false, "message" => "Your account as been disable, Please chat the support"], 401);
            }


            //Create user unique account Number

            $createUniqueAcct = UniqueBankAccount::where("user_id", $user->id)->first();
            $getUserDetails = User::where('id', $user->id)->first();
            $accountReference = 'TRT-' . strtoupper(uniqid() . mt_rand(1000, 9999));
            $details = array(
                "accountReference" => $accountReference, // unique reference
                "accountName" => $getUserDetails->name,
                "currencyCode" => "NGN",
                "contractCode" => "514002352596",
                "customerEmail" => $getUserDetails->email,
                "bvn" => "22227075466",
                "customerName" => $getUserDetails->name,
                "getAllAvailableBanks" => true
            );
            if (!$createUniqueAcct) {
                $service = new BankAccount();
                $data = $service->createBankAccountForUsers($details);

                $uniqueAcct =  UniqueBankAccount::create([
                    "bank_id" => 'RSA-' . strtoupper(uniqid() . mt_rand(1000, 9999)),
                    'contract_code' => $data['responseBody']['contractCode'],
                    'account_reference' => $data['responseBody']['accountReference'],
                    'account_name' => $data['responseBody']['accountName'],
                    'currency_code' => $data['responseBody']['currencyCode'],
                    'customer_email' => $data['responseBody']['customerEmail'],
                    'customer_name' => $data['responseBody']['customerName'],
                    'collection_channel' => $data['responseBody']['collectionChannel'],
                    'reservation_reference' => $data['responseBody']['reservationReference'],
                    'reserved_account_type' => $data['responseBody']['reservedAccountType'],
                    'status' => $data['responseBody']['status'],
                    'created_on' => $data['responseBody']['createdOn'],
                    'bvn' => $data['responseBody']['bvn'],
                    'restrict_payment_source' => $data['responseBody']['restrictPaymentSource'],
                    "user_id" => $user->id
                ]);
                foreach ($data['responseBody']['accounts'] as $account) {
                    AccountDetail::create([
                        'unique_bank_account_id' => $uniqueAcct->id,
                        'bank_code' => $account['bankCode'],
                        'bank_name' => $account['bankName'],
                        'account_number' => $account['accountNumber'],
                        'account_name' => $account['accountName'],
                        "user_id" => $user->id
                    ]);
                }
            }

            $walletId = 'WLB-' . strtoupper(uniqid() . mt_rand(1000, 9999));
            // $wallet = WalletUser::firstOrCreate(
            //     ['wallet_id' => $walletId],
            //     ['wallet_balance' => "0"],
            //     ['balanace_before' => null],
            //     ['balanace_after' => null],
            //     ['user_id' => $user->id]

            // );

            $checkFirst = WalletUser::where("user_id", $user->id)->first();
            if (!$checkFirst) {
                $wallet = WalletUser::create([
                    'wallet_id' => $walletId,
                    'wallet_balance' => Crypt::encryptString(0),
                    'user_id' => $user->id
                ]);
            };
            // Clear old tokens when logging in
            $user->tokens()->delete();

            // $token = $user->createToken('auth_token', ['*'], now()->addHour(1))->plainTextToken;
            $token = $user->createToken('auth_token')->plainTextToken;
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return 'jeiie';
            }

            return response()->json([
                "message" => "successfully login",
                "status" => true,
                "user" => $user,
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function profileDetails()
    {
        try {
            // $currentUser = Auth::user();
            $currentUser = Auth::user();
            return response()->json([
                "status" => true,
                "messages" => $currentUser
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getActiveUser()
    {
        try {
            $user = User::where('id', Auth::user()->id)->where('status', "enable")->first();
            if ($user) {
                return response()->json([
                    'status' => true,
                    'user' => $user
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function UpdateprofileDetails(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $currentUser->id,
                'username' => 'required|string|min:3|max:20|alpha_dash',
                //'phone_number' => 'required|string',
                'surname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'phone_number' => ['required', 'regex:/^\+?[0-9]{7,15}$/'],
                'gender' => 'required|in:male,female,other',
                'date_of_birth' => 'required|date',
                'country' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $updateUser = User::where('id', $currentUser->id)->first();

            $updateUser->update([
                'name' => $request->name,
                'email' => $request->email,
                "username" => $request->username,
                "phone_number" => $request->phone_number,
                'surname' => $request->surname,
                'lastname' => $request->lastname,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'country' => $request->country,
                'state' => $request->state,
                'address' => $request->address,
                'city' => $request->city,

            ]);
            return response()->json([
                "status" => true,
                "message" => "Profile updated successfully"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                'email' => 'required|string|email'

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $users = User::where('email', $request->email)->first();
            if (!$users) {
                return response()->json([
                    'status' => false,
                    'message' => 'User does not exist, Please enter a correct email'
                ], 400);
            }

            $otp = rand(100000, 999999);
            $otpExpiresAt = Carbon::now()->addMinutes(10);
            $users->reset_password = $otp;
            $users->reset_password_expires = $otpExpiresAt;
            $users->save();

            // $users->update([
            //     "reset_password" => $otp,
            //     "reset_password_expires" => $otpExpiresAt,
            // ]);
            Mail::to($request->email)->send(new ResentEmail($users));
            return response()->json([
                'status' => true,
                'message' => 'Otp sent to your email successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "current_password" => 'required|min:6',
                'password' => 'required|min:6|confirmed',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $checkOldPassword  = User::where('id', Auth::user()->id)->first();
            if (!$checkOldPassword || !Hash::check($request->current_password, $checkOldPassword->password)) {
                return response()->json(['status' => false, 'message' => 'Old password is incorrect, Please try again later'], 500);
            }
            $checkOldPassword->update([
                "password" => Hash::make($request->password)
            ]);

            return response()->json(['status' => true, 'message' => 'Password updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function ResetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                'resetCode' => 'required|string'

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $users = User::where('reset_password', $request->resetCode)->first();
            if (!$users) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Code... please try again later'
                ], 400);
            }
            if ($users->reset_password  !== $request->resetCode) {
                return response()->json([
                    'status' => false,
                    'message' => 'Reset Otp as expire, Please try again'
                ], 400);
            }

            if (Carbon::now()->greaterThan($users->reset_password_expires)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Reset Otp as expire, Please try again'
                ], 400);
            }

            // $users->reset_password = null;
            $users->reset_password_expires = null;
            $users->save();



            return response()->json([
                'status' => true,
                'message' => 'Otp validated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function PasswordReset(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'resetCode' => 'required|string',
                'password' => 'required|string|min:6|confirmed',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $resetUserPass = User::where('reset_password', $request->resetCode)->first();
            if (!$resetUserPass) {
                return response()->json([
                    'status' => false,
                    'message' => "Invalid Token or expired token"
                ], 400);
            }
            $resetUserPass->update([
                'password' => Hash::make($request->password),
            ]);
            $resetUserPass->reset_password = null;
            $resetUserPass->save();
            return response()->json(["status" => true, 'message' => 'Password reset successful']);
        } catch (\Exception $e) {
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json(['status' => false, 'message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function getUserAccount(Request $request)
    {
        $users = Auth::user()->id;
    }


    public  function you(Request $request)
    {
        // For session-based authentication
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            // $request->session()->regenerate();
            $user = User::where('email', $request->email)->first();
            //  $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Successfully logged out!',
                // 'token' => $token
            ]);
        }
    }
}
