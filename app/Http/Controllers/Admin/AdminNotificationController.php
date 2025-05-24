<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminMessage;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class AdminNotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'type' => 'required|string',
            ]);



            if ($request->type === 'single') {
                $user = User::findOrFail($request->user_id);
                $user->notify(new AdminMessage($request->title, $request->message));
            }

            if ($request->type === 'all') {
                $users = User::all();
                foreach ($users as $user) {
                    $user->notify(new AdminMessage($request->title, $request->message));
                }
            }

            return response()->json(['status' => true, 'message' => 'Notification sent successfully']);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAllUserNotification(Request $request)
    {

        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $query = DatabaseNotification::query();
            $query->latest();
            $query->with('notifiable');
            if ($search) {
                $query->where(
                    function ($q) use ($search) {
                        $q->where('transaction_id', 'like', "%$search%")
                            ->orWhere('account_number', 'like', "%$search%");
                    }
                );
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status); // assuming "active", "inactive", etc.
            }
            $Notification = $query->paginate($perPage);
            return response()->json([
                "status" => true,
                'notification' => $Notification

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
        // $notifications = DatabaseNotification::latest()->paginate(10);

        // return $notifications;
    }
}
