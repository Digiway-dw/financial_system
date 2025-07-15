<?php

use App\Application\Services\WorkSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public route for checking API availability
Route::get('/status', function () {
    Log::info('API status route accessed');
    return response()->json(['status' => 'API is working']);
});

// Add a simple test route that doesn't require auth
Route::get('/test', function () {
    Log::info('API test route accessed');
    return response()->json([
        'status' => 'API test route is working',
        'prefix' => request()->route()->getPrefix(),
        'middleware' => request()->route()->middleware(),
        'uri' => request()->route()->uri(),
        'action' => request()->route()->getActionName()
    ]);
});

// Authentication routes can be added here as needed

// Add a fallback route for debugging - will help diagnose if the API routes are being reached
Route::fallback(function () {
    return response()->json(['message' => 'API endpoint not found'], 404);
});
