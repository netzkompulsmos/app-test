<?php

use App\Models\Image;
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
        'message' => 'Laravel Image Upload API is functioning smoothly inside Docker!',
        'database' => [
            'connected' => $dbConnected,
            'driver' => $dbDriver,
            'database' => config('database.connections.' . $dbDriver . '.database'),
            'username' => config('database.connections.' . $dbDriver . '.username'),
            'host' => config('database.connections.' . $dbDriver . '.host'),
            'error' => $dbError,
        ],
        'total_images' => $dbConnected ? Image::count() : 0,
        'timestamp' => now()->toISOString(),
    ]);
});

// Get all uploaded images
Route::get('/images', function () {
    try {
        $images = Image::orderBy('created_at', 'desc')->get();
        return response()->json($images);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Upload a new image
Route::post('/images', function (Request $request) {
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
        'title' => 'required|string|max:255',
        'author' => 'nullable|string|max:100',
    ]);

    $file = $request->file('image');
    $extension = $file->getClientOriginalExtension();
    $filename = time() . '_' . uniqid() . '.' . $extension;

    $destinationPath = public_path('uploads');
    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0777, true);
    }

    $file->move($destinationPath, $filename);

    $bytes = file_exists($destinationPath . '/' . $filename) ? filesize($destinationPath . '/' . $filename) : 0;
    $size = $bytes >= 1048576
        ? number_format($bytes / 1048576, 2) . ' MB'
        : number_format($bytes / 1024, 1) . ' KB';

    $image = Image::create([
        'title' => $request->input('title'),
        'author' => $request->input('author') ?: 'Nho Net',
        'filename' => $filename,
        'url' => '/uploads/' . $filename,
        'size' => $size,
    ]);

    return response()->json($image, 201);
});

// Delete an image
Route::delete('/images/{id}', function ($id) {
    $image = Image::find($id);
    if (!$image) {
        return response()->json(['message' => 'Image not found'], 404);
    }

    $filePath = public_path('uploads/' . $image->filename);
    if (file_exists($filePath)) {
        @unlink($filePath);
    }

    $image->delete();
    return response()->json(['message' => 'Image deleted successfully']);
});

// Messages fallback API
Route::get('/messages', function () {
    try {
        $messages = Message::orderBy('created_at', 'desc')->take(50)->get();
        return response()->json($messages);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::post('/messages', function (Request $request) {
    $validated = $request->validate([
        'author' => 'required|string|max:100',
        'content' => 'required|string|max:1000',
    ]);

    $message = Message::create($validated);
    return response()->json($message, 201);
});
