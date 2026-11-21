<?php

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Health status check endpoint
Route::get('/status', function () {
    $dbConnected = false;
    $dbDriver = config('database.default');
    $dbError = null;

    try {
        DB::connection()->getPdo();
        $dbConnected = true;
    } catch (\Throwable $e) {
        $dbError = $e->getMessage();
    }

    return response()->json([
        'status' => 'ok',
        'creator' => 'Nho Net',
        'message' => 'Laravel Backend API is functioning smoothly inside Docker!',
        'database' => [
            'connected' => $dbConnected,
            'driver' => $dbDriver,
            'database' => config('database.connections.' . $dbDriver . '.database'),
            'username' => config('database.connections.' . $dbDriver . '.username'),
            'host' => config('database.connections.' . $dbDriver . '.host'),
            'error' => $dbError,
        ],
        'timestamp' => now()->toISOString(),
    ]);
});

// Get all messages
Route::get('/messages', function () {
    try {
        $messages = Message::orderBy('created_at', 'desc')->take(50)->get();
        return response()->json($messages);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
});

// Post a new message
Route::post('/messages', function (Request $request) {
    $validated = $request->validate([
        'author' => 'required|string|max:100',
        'content' => 'required|string|max:1000',
    ]);

    $message = Message::create($validated);

    return response()->json($message, 201);
});
