<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});
// Route::middleware('web')->group(function () {
//     // Route::post('/login', [AuthController::class, 'login']);
//     // Route::post('/logout', [AuthController::class, 'logout']);
//     Route::post('/register', [UserController::class, 'register']);
//     Route::post('/login', [UserController::class, 'login']);
// });


Route::post('/logs', [UserController::class, 'login']);
// Route::post('/log', function (Request $request) {
//     // For session-based authentication
//     if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
//         return 'jeiie';
//     }

//     return response()->json([
//         'message' => 'Successfully logged out!',
//     ]);
// });
