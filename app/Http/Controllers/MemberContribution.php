<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use App\Models\CpContribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberContribution extends Controller
{
    public function getContributions()
    {
        try {
            $user = Auth::user();
            $trackUser = AccountDetail::where("user_id", $user->id)->first();
            $contribution = CpContribution::where('account_number', $trackUser->account_number)->get();
            return response()->json([
                "status" => true,
                "contribution" => $contribution
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
