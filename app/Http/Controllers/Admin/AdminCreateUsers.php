<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\registrationEmail;
use App\Models\CpMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminCreateUsers extends Controller
{
    public function createUsers(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|unique:users,username|min:3|max:20|alpha_dash',
                'password' => 'required|string|min:6|confirmed',
                'lastname' => 'required|string|max:255',
                // 'address' => 'required|string',
                // 'city' => 'required|string',
                // 'state' => 'required|string',
                // 'country' => 'required|string',
                'role' => 'required|string|in:user,admin,editor',
                // 'status' => 'required|in:enable,disable',
                // 'gender' => 'required|string',
                // 'date_of_birth' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $otp = rand(100000, 999999);
            $otpExpiresAt = Carbon::now()->addMinutes(5);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                "username" => $request->username,
                "role" => $request->role

            ]);

            if ($request->role === 'user') {
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

                // Mail::to($request->email)->send(new registrationEmail($user));

            }
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(["status" => true, "message" => "Registration successfull, Please verify your email to proceed"], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
