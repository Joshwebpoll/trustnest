<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Mail\registrationEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserSettingsController extends Controller
{
    public function editUser(Request $request, $id)
    {
        try {

            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // $validator = Validator::make($request->all(), [
            //     'surname' => 'required|string|max:255',
            //     'name' => 'required|string|max:255',
            //     'lastname' => 'required|string|max:255',
            //     'email' => 'required|string|email|max:255|unique:users',
            //     'username' => 'required|string',
            //     'phone_number' => 'required|unique:users',
            //     'address' => 'required|string',
            //     'city' => 'required|string',
            //     'state' => 'required|string',
            //     'country' => 'required|string',
            //     'date_of_birth' => 'required',
            // ]);

            // if ($validator->fails()) {
            //     return response()->json($validator->errors(), 422);
            // }

            $user->update($request->all());
            return response()->json(["status" => true, "message" => "User updated successfully"], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function deleteUser($id)
    {

        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['status' => true, 'message' => 'User not found'], 404);
            }
            $user->delete($id);
            return response()->json(['status' => true, 'message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function getAllUsers(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $query = User::query();
            if ($search) {
                $query->where(
                    function ($q) use ($search) {
                        $q->where('email', 'like', "%$search%")
                            ->orWhere('name', 'like', "%$search%");
                    }
                );
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status); // assuming "active", "inactive", etc.
            }
            $getUsers = $query->paginate($perPage);
            return response()->json([
                "status" => true,
                "users" => UserResource::collection($getUsers)->response()->getData(true),

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
