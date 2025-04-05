<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountDetail;
use App\Models\Saving;
use App\Models\WalletUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class SavingController extends Controller
{
    public function savedeposit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                'account_number' => 'required|string|max:20|exists:account_details,account_number',
                'amount_deposited' => 'required|numeric|min:100',
                'saving_type' => 'required|string|in:saving,current',
                'status' => 'required|string|in:pending,completed',
                'deposit_type' => 'required|string|in:cash,transfer',
                'deposit_date' => 'required',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $saving_id = 'SVD-' . strtoupper(uniqid() . mt_rand(1000, 9999));
            $transaction_id = Str::uuid();
            // $admin_id = Auth::user()->id;
            //deposit money into the saving table
            $saving_result = Saving::create([
                "transaction_id" => $saving_id,
                'account_number' => $request->account_number,
                'amount_deposited' => Crypt::encryptString($request->amount_deposited),
                'saving_type' => $request->saving_type,
                'status' => $request->status,
                'transaction_reference' => $transaction_id,
                'deposit_type' => $request->deposit_type,
                'processed_by' => "1",
                'deposit_date' => $request->deposit_date,

            ]);

            //    $users_account =
            //Get the account number
            $getAccount = AccountDetail::where("account_number", $saving_result->account_number)->first();
            //Get the wallet balance
            $getWalletBalance = WalletUser::where('user_id', $getAccount->user_id)->first();
            $decryptBalance = Crypt::decryptString($getWalletBalance->wallet_balance);

            $getSavings = Saving::where("transaction_reference", $transaction_id)->first();
            $decryptSavings = Crypt::decryptString($getSavings->amount_deposited);
            $addBalanceToWallet = bcadd($decryptBalance, $decryptSavings, 2);

            $getWalletBalance->update([
                "wallet_balance" => Crypt::encryptString($addBalanceToWallet)
            ]);


            return response()->json([
                'status' => true,
                "message" => "Deposit was successful",

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
