<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\CpMember;
use App\Models\CpMembers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MembersController extends Controller
{
    public function getMemberDetails(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $query = CpMember::query();
            if ($search) {
                $query->where(
                    function ($q) use ($search) {
                        $q->where('membership_number', 'like', "%$search%")
                            ->orWhere('status', 'like', "%$search%");
                    }
                );
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status); // assuming "active", "inactive", etc.
            }
            $getLoan = $query->paginate($perPage);
            return response()->json([
                "status" => true,
                "members" => MemberResource::collection($getLoan)->response()->getData(true),

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
