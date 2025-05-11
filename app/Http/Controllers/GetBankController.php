<?php

namespace App\Http\Controllers;

use App\Helper\VerifyUserAccountDetails;
use App\Http\Resources\BankDetailsResource;
use App\Http\Resources\UserAccountNumberResource;
use App\Models\CpAccountNumber;
use App\Models\CpBankName;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GetBankController extends Controller
{
    public function getBanks()
    {
        try {

            $getBanks = CpBankName::orderBy('bank_name')->get();

            return response()->json([
                'status' => true,
                'banks' => BankDetailsResource::collection($getBanks),

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function verifyBank(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'bank_account_number' => 'required|unique:cp_account_numbers,bank_account_number',
            'bank_code' => 'required|numeric',
            "bank_account_name" => 'required|string'


        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $getUsername = User::where('id', Auth::user()->id)->first();

            if (empty($getUsername->name) || empty($getUsername->surname) || empty($getUsername->lastname)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please update your full details in the profile section',

                ], 400);
            }

            $result = VerifyUserAccountDetails::verifyBankDetails($request->bank_account_number, $request->bank_code, $getUsername);
            if ($result) {
                $getBankName = CpBankName::where('bank_code', $request->bank_code)->where('status', 'enable')->first();
                CpAccountNumber::create(
                    [
                        "bank_account_name" => $result,
                        "bank_account_number" => $request->bank_account_number,
                        "bank_name" => $getBankName->bank_name,
                        "bank_code" => $getBankName->bank_code,
                        "user_id" => Auth::user()->id

                    ]
                );
                return response()->json([
                    'status' => true,
                    'message' => 'Bank added successfully',

                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserAccountNumber()
    {
        try {



            $getUserAccount = CpAccountNumber::where('user_id', Auth::user()->id)->first();
            if ($getUserAccount) {
                return response()->json([
                    'status' => true,
                    'account_details' => new UserAccountNumberResource($getUserAccount),

                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
