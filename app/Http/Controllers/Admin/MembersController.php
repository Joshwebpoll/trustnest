<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CpMember;
use App\Models\CpMembers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MembersController extends Controller
{
    public function getMemberDetails()
    {
        try {
            $members = CpMember::all();

            return response()->json([
                'status' => true,
                "message" => $members,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
