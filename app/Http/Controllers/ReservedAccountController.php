<?php

namespace App\Http\Controllers;

use App\Models\UniqueBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservedAccountController extends Controller
{
    public function getUniqueBankAccount()
    {
        try {
            $user = UniqueBankAccount::where('user_id', Auth::user()->id)->with('accounts')->get();
            return response()->json([
                "status" => true,
                "account" => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
