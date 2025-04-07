<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use App\Models\MonnifyPaymentTransaction;
use App\Models\Saving;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavingController extends Controller
{
    public function getSaving()
    {
        try {
            $getUserRelatedAccount = AccountDetail::find(Auth::user()->id);
            $getUserSaving = Saving::where("account_number", $getUserRelatedAccount->account_number)->get();
            return response()->json([
                'status' => true,
                'savings' => $getUserSaving
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function getTransferDeposit()
    {
        try {
            $getUserAccount = AccountDetail::where('user_id', Auth::user()->id)->first();
            $getUserDeposit = MonnifyPaymentTransaction::where("destination_account_number", $getUserAccount->account_number)->get();
            return response()->json([
                'status' => true,
                'savings' => $getUserDeposit
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
