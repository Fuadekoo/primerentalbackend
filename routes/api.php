<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Property\PropertyController;

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
/**
 * PUBLIC ROUTES (ACCESSIBLE TO ALL)
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/homepage', function () {
    return response()->json(['message' => 'Welcome to the Homepage']);
});
Route::get('/about', function () {
    return response()->json(['message' => 'About Page']);
});

Route::prefix('properties')->group(function () {
    Route::get('/', [PropertyController::class, 'index']); // Fetch all properties
    Route::get('/{id}', [PropertyController::class, 'show']); // Fetch single property
});

/**
 * AUTHENTICATED ROUTES (ACCESSIBLE TO BOTH ADMIN AND CUSTOMER)
 */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // SHARED PAGES FOR AUTHENTICATED USERS (BOTH ADMINS AND CUSTOMERS)
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Shared Dashboard for Authenticated Users']);
    });
});

/**
 * ADMIN-ONLY ROUTES
 */
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // ADMIN PROPERTY ROUTES
    Route::prefix('properties')->group(function () {
        Route::post('/', [PropertyController::class, 'store']); // Add property
        Route::put('/{id}', [PropertyController::class, 'update']); // Update property
        Route::delete('/{id}', [PropertyController::class, 'destroy']); // Delete property
        Route::post('/{id}/status', [PropertyController::class, 'changestatus']); // Change status
    });

    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Admin Dashboard']);
    });

    Route::get('/admin/settings', function () {
        return response()->json(['message' => 'Admin Settings Page']);
    });
});

/**
 * CUSTOMER-ONLY ROUTES
 */
Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    Route::get('/customer/profile', function () {
        return response()->json(['message' => 'Customer Profile Page']);
    });

    Route::get('/customer/orders', function () {
        return response()->json(['message' => 'Customer Orders Page']);
    });
});
