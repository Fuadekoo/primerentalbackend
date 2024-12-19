<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Property\PropertyController;
use App\Http\Controllers\Property\HomeTypeController;
use App\Http\Controllers\Feedback\FeedbackController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Filter\SearchController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Message\MessageController;
use App\Http\Controllers\UserController;

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

 Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

Route::middleware('auth:sanctum')->post('/get-user-by-id', [AuthController::class, 'getUserById']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/properties/search_by_price', [SearchController::class, 'search_by_price']);
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

// filter properties by price
Route::get('/properties/search_by_price', [SearchController::class, 'search_by_price']);
Route::get('/getproperty',[PropertyController::class, 'getproperty']); // get the property and search it

// filter the hometypes
Route::get('/hometypesearch', [HomeTypeController::class, 'index']); // Fetch all home types



/**
 * AUTHENTICATED ROUTES (ACCESSIBLE TO BOTH ADMIN AND CUSTOMER)
 */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/updateUser', [AuthController::class, 'updateUser']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/photo', [AuthController::class, 'updateProfilePhoto']);
    Route::put('/profile/info', [AuthController::class, 'updateProfileInfo']);
    Route::get('/getprofilephoto', [AuthController::class, 'getProfilePhoto']);
    // Route::post('/sendMessage', [ChatController::class, 'sendMessage']);
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

    // route for messagecontroller
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/reply-message/{userId}', [MessageController::class, 'replyMessage']);  //this is a reply message by admin
        Route::get('/users-with-messages', [MessageController::class, 'getUsersWithMessages']);
        Route::get('/listreply', [MessageController::class, 'getUsersWithMessages']);  //this is a list of reply message
        Route::get('/getmessage/:id', [MessageController::class, 'fetchMessages']); //this is a get message by customer
        Route::get('/messages/with-user/{userId}', [MessageController::class, 'getMessagesWithUser']); // New route
    });

    // ADMIN PROPERTY ROUTES

    Route::prefix('properties')->group(function () {
        Route::post('/', [PropertyController::class, 'store']); // Add property
        Route::put('/{id}', [PropertyController::class, 'update']); // Update property
        Route::delete('/{id}', [PropertyController::class, 'destroy']); // Delete property
        Route::post('/{id}/status', [PropertyController::class, 'changestatus']); // Change status

    });

    Route::get('/getUsers', [UserController::class, 'getUsers']);
    Route::put('/toggleBlockUser/{userId}/block', [UserController::class, 'toggleBlockUser']);

    // admin home type routes
    Route::prefix('hometypes')->group(function () {
        Route::get('/', [HomeTypeController::class, 'index']); // Fetch all home types
        Route::post('/', [HomeTypeController::class, 'store']); // Add home type
        Route::get('/{id}', [HomeTypeController::class, 'show']); // Fetch single home type
        Route::put('/{id}', [HomeTypeController::class, 'update']); // Update home type
        Route::delete('/{id}', [HomeTypeController::class, 'destroy']); // Delete home type
    });

    // ADMIN BOOKING ROUTES
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']); // Fetch all bookings
        Route::put('/{booking}/status', [BookingController::class, 'updateStatus']); // Update booking status
    });

    // admin dashboard routes
    Route::get('/dashboard', [SearchController::class, 'dashboard']);

    Route::get('/admin/settings', function () {
        return response()->json(['message' => 'Admin Settings Page']);
    });
});

/**
 * CUSTOMER-ONLY ROUTES
 */
Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {

    // customer feedback routes
    Route::prefix('feedback')->group(function () {
        Route::put('/{id}/{propertyId}', [FeedbackController::class, 'update']); // Update feedback
        Route::delete('/{id}/{propertyId}', [FeedbackController::class, 'destroy']); // Delete feedback
        Route::post('/{propertyId}', [FeedbackController::class, 'store']); // Add feedback
        Route::get('/{propertyId}', [FeedbackController::class, 'index']); // Fetch feedback
        Route::get('/myfeedback/{propertyId}', [FeedbackController::class, 'myfedback']); // Fetch my feedback


    });

    // CUSTOMER BOOKING ROUTES
    Route::prefix('bookings')->group(function () {
        Route::post('/properties/{property}', [BookingController::class, 'store']); // Add booking
        Route::get('/mybookings', [BookingController::class, 'mybookings']); // Fetch my bookings
        Route::put('/{booking}', [BookingController::class, 'update']); // Update booking
        Route::delete('/{booking}', [BookingController::class, 'destroy']); // Delete booking
    });

    Route::get('/customer/profile', function () {
        return response()->json(['message' => 'Customer Profile Page']);
    });

    Route::get('/customer/orders', function () {
        return response()->json(['message' => 'Customer Orders Page']);
    });



    // route for messagecontroller
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/send-message', [MessageController::class, 'sendMessage']);  //this is a send message by customer
        Route::get('/messages', [MessageController::class, 'getMessages']); //this is a get message by customer
        Route::get('/messages/for-user', [MessageController::class, 'getMessagesForUser']); // New route
    });

});
