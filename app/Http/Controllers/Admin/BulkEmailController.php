<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BulkEmailJob;
use App\Models\User;
use App\Notifications\AdminMessage;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class BulkEmailController extends Controller
{
    public function sendBulkEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject' => 'required | string',
                'message' => 'required | string',
                'users' => 'required|array'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }


            BulkEmailJob::dispatch($validator->validated());
            return response()->json([
                'status' => true,
                'message' => 'Email sent successfully'
            ], 200);
            // }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
