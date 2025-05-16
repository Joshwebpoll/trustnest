<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\InterestResource;
use App\Models\CpInterestRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InterestController extends Controller


{

    public function getInterest(Request $request)
    {
        try {
            // $perPage = $request->get('per_page', 10);
            // //  $search = $request->input('search');
            // $query = CpInterestRate::query();
            // // if ($search) {
            // //     $query->where(
            // //         function ($q) use ($search) {
            // //             $q->where('membership_number', 'like', "%$search%")
            // //                 ->orWhere('status', 'like', "%$search%");
            // //         }
            // //     );
            // // }

            // // if ($status = $request->input('status')) {
            // //     $query->where('status', $status); // assuming "active", "inactive", etc.
            // // }
            $getInterest = CpInterestRate::first();
            return response()->json([
                "status" => true,
                "interest" => $getInterest,

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
                'min_amount' => $request->min_amount,
                'max_amount' => $request->max_amount,
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

                'min_amount' => 'nullable|numeric|min:0',
                'max_amount' => 'nullable|numeric|min:0',
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
            $updateInterestRates->fill($validator->validated());

            if (!$updateInterestRates->isDirty()) {
                return response()->json([
                    'status' => true,
                    "message" => "No changes detected"
                ], 200);
            }
            $updateInterestRates->update([
                'interest_rate' => $request->interest_rate,
                'min_amount' => $request->min_amount,
                'max_amount' => $request->max_amount

            ]);
            return response()->json([
                'status' => true,
                "message" => "Interest rate updated succesfully"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
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
