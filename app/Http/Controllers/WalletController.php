<?php

namespace App\Http\Controllers;

use App\Models\Saving;
use App\Models\WalletUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function getUserWallet()
    {
        try {
            $userId = Auth::user()->id;
            $wallet = WalletUser::where('user_id', $userId)->first();
            return response()->json([
                'status' => true,
                'Wallet' =>  $wallet
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function saveMoneyToWallet(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "saving_id" => 'required|string',
                'amount_deposited' => 'required|string',
                'saving_type' => 'required|string',
                'savingtransaction_id' => 'required|string',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $saveManualMoney = Saving::create([
                "saving_id" => $request->saving_id,
                "amount_deposited" => $request->amount_deposited,
                'saving_type' => $request->saving_type,
                "savingtransaction_id" => $request->savingtransaction_id

            ]);
            if ($saveManualMoney) {
                $wallets = WalletUser::where('user_id', Auth::user()->id)->first();
            }
            return response()->json([
                'status' => false,
                'message' => "saved successfully"
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
