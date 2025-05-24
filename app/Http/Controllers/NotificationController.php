<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getUserNotifications()
    {
        $user = Auth::user(); // or auth()->user()

        $notifications = $user->notifications; // All notifications
        $unread = $user->unreadNotifications;  // Only unread notifications

        return response()->json([
            'all' => $notifications,
            'unread' => $unread,
        ]);
    }
}
