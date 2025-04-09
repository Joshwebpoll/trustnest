<?php

namespace App\Http\Controllers;

use App\Models\CpMember;
use App\Models\CpMembers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MembersUserController extends Controller
{
    public function getMemberDetails()
    {
        try {
            $members = CpMember::where('user_id', Auth::user()->id)->first();

            return response()->json([
                'status' => true,
                "message" => Crypt::decryptString($members->total_shares),
                "messages" => Crypt::decryptString($members->total_savings)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
