<?php

namespace App\Http\Controllers;

use App\Http\Resources\RepaymentResource;
use App\Models\CpRepayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepaymentUserController extends Controller
{
    public function repayLoan(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $from = $request->input('from');
            $to = $request->input('to');
            $query = CpRepayment::query();
            $query->where('user_id', Auth::user()->id);
            if ($search) {
                $query->where(
                    function ($q) use ($search) {
                        $q->where('transaction_reference', 'like', "%$search%")
                            ->orWhere('status', 'like', "%$search%");
                    }
                );
            }
            // if ($from && $to) {
            //     $query->whereBetween('created_at', [$from, $to]);
            // }
            if ($from) {
                $query->whereDate('created_at', '>=', $request->from);
            }

            if ($to) {
                $query->whereDate('created_at', '<=', $request->to);
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status); // assuming "active", "inactive", etc.
            }
            $getRepayment = $query->paginate($perPage);
            return response()->json([
                "status" => true,
                "repayments" => RepaymentResource::collection($getRepayment)->response()->getData(true),

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
