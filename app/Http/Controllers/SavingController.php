<?php

namespace App\Http\Controllers;

use App\Models\Saving;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavingController extends Controller
{
    public function getSaving()
    {
        try {
            $getUserSaving = Saving::where("user_id", Auth::user()->id)->get();
            return response()->json([
                'status' => true,
                'savings' => $getUserSaving
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
