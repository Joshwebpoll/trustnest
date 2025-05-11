<?php

namespace App\Http\Controllers;

use App\Http\Resources\SingleMemberResource;
use App\Models\CpMember;
use App\Models\CpMembers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PhpOffice\PhpSpreadsheet\Reader\Xls\RC4;

class MembersUserController extends Controller
{
    public function getMemberDetails(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $from = $request->input('from');
            $to = $request->input('to');
            $query = CpMember::query();
            $query->where('user_id', Auth::user()->id);
            if ($search) {
                $query->where(
                    function ($q) use ($search) {
                        $q->where('transaction_reference', 'like', "%$search%")
                            ->orWhere('status', 'like', "%$search%");
                    }
                );
            }

            if ($from) {
                $query->whereDate('created_at', '>=', $request->from);
            }

            if ($to) {
                $query->whereDate('created_at', '<=', $request->to);
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status); // assuming "active", "inactive", etc.
            }
            $getMember = $query->paginate($perPage);
            return response()->json([
                "status" => true,
                "members" => SingleMemberResource::collection($getMember)->response()->getData(true),

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
