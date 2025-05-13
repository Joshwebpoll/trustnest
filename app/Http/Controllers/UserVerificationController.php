<?php

namespace App\Http\Controllers;

use App\Helper\Verification;
use App\Models\CpNinVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserVerificationController extends Controller
{
    public function verifyBvn(Request $request)
    {
        try {
            $request->merge([
                'bvn_phone_number' => preg_replace('/\D/', '', $request->bvn_phone_number),
            ]);

            $validator = Validator::make($request->all(), [

                'bvn' => ['required', 'digits:11'],
                'bvn_phone_number' => ['required', 'digits:11'],
                'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
                'gender' => ['required', 'in:male,female,other'],


            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            };

            $userName = User::where('id', Auth::user()->id)->first();
            $names = $userName->name . " " . $userName->surname . " " . $userName->lastname;
            $data = [
                "bvn" => $request->bvn,
                "name" => $names,
                "dateOfBirth" => $request->date_of_birth,
                "mobileNo" => $request->bvn_phone_number
            ];

            $result = new Verification();
            $finalResult = $result->bvnVerification($data);

            $phoneNumber = $request->bvn_phone_number;

            $res = $userName->update([
                'bvn' => $finalResult,
                "phone_number" => $phoneNumber,
                'gender' => $request->gender
            ]);
            if ($res) {
                return response()->json([
                    'status' => true,
                    'message' => 'BVN verification successful. Your identity has been successfully confirmed.'


                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function verifyNin(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [

                'nin' => ['required', 'digits:11'],
                'nin_phone_number' => ['required', 'digits:11'],
                'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
                'gender' => ['required', 'in:male,female,other'],


            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            };
            $checkUserNin = CpNinVerification::where('user_id', Auth::user()->id)->exists();
            if ($checkUserNin) {
                return response()->json([
                    'status' => true,
                    'message' => 'Nin Already Verified'


                ], 500);
            }

            $userName = User::where('id', Auth::user()->id)->first();
            $names = $userName->name . " " . $userName->surname . " " . $userName->lastname;
            $data = [
                "nin" => $request->nin,
            ];

            $result = new Verification();
            $finalResult = $result->ninVerification($data);
            $nin = $finalResult["responseBody"]["nin"];
            $lastName = $finalResult["responseBody"]["lastName"];
            $firstName = $finalResult["responseBody"]["firstName"];
            $middleName = $finalResult["responseBody"]["middleName"];
            $dateOfBirth = $finalResult["responseBody"]["dateOfBirth"];
            $gender = $finalResult["responseBody"]["gender"];
            $mobileNumber = $finalResult["responseBody"]["mobileNumber"];

            CpNinVerification::create([
                'nin' => $nin,
                'last_name' => $lastName,
                'first_name' => $firstName,
                'middle_name' =>  $middleName,
                'date_of_birth' => $dateOfBirth,
                'gender' =>  $gender,
                'mobile_number' => $mobileNumber,
                "user_id" => Auth::user()->id
            ]);

            $res = $userName->update([
                'nin' => $nin,

            ]);

            return response()->json([
                'status' => true,
                'message' => 'NIN verification successful. Your identity has been successfully confirmed.'


            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
