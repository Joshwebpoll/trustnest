<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CpInterestRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InterestController extends Controller


{

    public function getInterest()
    {
        try {
            $interestRates = CpInterestRate::all();
            return response()->json([
                'status' => true,
                "interest" => $interestRates
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
    public function createInterest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                // 'min_amount' => 'required|numeric|min:0',
                // 'max_amount' => 'required|numeric|min:0',
                'interest_rate' => 'required|numeric|min:0',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            CpInterestRate::create([
                // 'min_amount' => $request->min_amount,
                // 'max_amount' => $request->max_amount,
                'interest_rate' => $request->interest_rate,
            ]);
            return response()->json([
                'status' => true,
                'message' => "Interest created successfully"
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function updateInterest(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [

                // 'min_amount' => 'required|numeric|min:0',
                // 'max_amount' => 'required|numeric|min:0',
                'interest_rate' => 'required|numeric|min:0',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $updateInterestRates = CpInterestRate::find($id);
            if (!$updateInterestRates) {
                return response()->json([
                    'status' => false,
                    "message" => "Record not found"
                ], 404);
            }
            $updateInterestRates->update([
                'interest_rate' => $request->interest_rate
            ]);
            return response()->json([
                'status' => true,
                "message" => "updated succesfully"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function deleteInterest($id)
    {
        try {



            $deleteInterestRates = CpInterestRate::find($id);
            if (!$deleteInterestRates) {
                return response()->json([
                    'status' => false,
                    "message" => "Record not found"
                ], 404);
            }
            $deleteInterestRates->delete();
            return response()->json([
                'status' => true,
                "message" => "deleted successfully succesfully"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
