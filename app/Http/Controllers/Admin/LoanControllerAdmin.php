<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CpLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class LoanControllerAdmin extends Controller
{
    public function getAllLoan()
    {
        try {
            $getAllLoan = CpLoan::find(10);
            return response()->json([
                'status' => true,
                "total_payable" => Crypt::decryptString($getAllLoan->total_payable),
                "remaining_balance" => Crypt::decryptString($getAllLoan->remaining_balance),
                "total_paid" => Crypt::decryptString($getAllLoan->total_paid),
                "re" => Crypt::encryptString(0)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
