<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminLoanResource;
use App\Mail\LoanUpdateByAdmin;
use App\Models\CpLoan;
use App\Models\CpMember;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoanControllerAdmin extends Controller
{
    public function getAllLoan()
    {
        try {
            $getAllLoan = CpLoan::all();
            return response()->json([
                'status' => true,
                'loan' =>  AdminLoanResource::collection($getAllLoan),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }


    public function approveLoan(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [

                'guarantor_user_id' => 'required|string',
                'status' => 'required|in:pending,approved,disbursed,rejected,defaulted,completed,defaulted',


            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $admin = Auth::user();
            $findLoan = CpLoan::find($id);
            if (!$findLoan) {
                return response()->json([
                    'status' => true,
                    "message" => 'Loan not found'

                ], 404);
            }

            $lookupGurantor = User::find($request->guarantor_user_id);
            $getUserLoan = User::find($findLoan->user_id);
            if (!$lookupGurantor) {
                return response()->json([
                    'status' => true,
                    "message" => 'User not found'

                ], 404);
            }
            $findLoan->update([
                "status" => $request->status,
                "guarantor_user_id" => $request->guarantor_user_id,
                "guarantor_name" => $lookupGurantor->name,
                "guarantor_email" => $lookupGurantor->email,
                "approved_by" => $admin->id,
                "approved_at" => now()
            ]);
            Mail::to($getUserLoan->email)->send(new LoanUpdateByAdmin($findLoan, $getUserLoan));

            return response()->json([
                'status' => true,
                "message" => "Loan updated successfully",

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
