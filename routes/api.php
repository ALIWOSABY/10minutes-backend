<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TrainerController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\DailyTaskController;
use App\Http\Controllers\Api\ReferralController;

// Admin Controllers
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\TrainerManagementController;
use App\Http\Controllers\Api\Admin\CategoryManagementController;
use App\Http\Controllers\Api\Admin\SessionManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Countries
    Route::get('/countries', [CountryController::class, 'index']);
    Route::get('/countries/{id}', [CountryController::class, 'show']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/tree', [CategoryController::class, 'tree']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    // Trainers
    Route::get('/trainers', [TrainerController::class, 'index']);
    Route::get('/trainers/{slug}', [TrainerController::class, 'show']);
    Route::get('/trainers/{slug}/sessions', [TrainerController::class, 'sessions']);

    // Sessions
    Route::get('/sessions', [SessionController::class, 'index']);
    Route::get('/sessions/featured', [SessionController::class, 'featured']);
    Route::get('/sessions/most-viewed', [SessionController::class, 'mostViewed']);
    Route::get('/sessions/latest', [SessionController::class, 'latest']);
    Route::get('/sessions/{slug}', [SessionController::class, 'show']);
    Route::get('/sessions/{slug}/related', [SessionController::class, 'related']);

    // Reviews (public)
    Route::get('/sessions/{sessionId}/reviews', [ReviewController::class, 'index']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index']);
    Route::get('/settings/{key}', [SettingController::class, 'show']);

    // Credit packages (public)
    Route::get('/credits/packages', [CreditController::class, 'packages']);
});

// Protected routes (Customer)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::post('/bookings/{id}/start', [BookingController::class, 'startSession']);

    // Credits & Payments
    Route::get('/credits/balance', [CreditController::class, 'balance']);
    Route::post('/credits/purchase', [CreditController::class, 'purchase']);
    Route::get('/credits/transactions', [CreditController::class, 'transactions']);
    Route::get('/credits/payments', [CreditController::class, 'payments']);

    // Reviews
    Route::get('/reviews/my', [ReviewController::class, 'myReviews']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    Route::post('/reviews/{id}/helpful', [ReviewController::class, 'markHelpful']);

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);
    Route::get('/wishlist/check/{sessionId}', [WishlistController::class, 'check']);

    // Daily Tasks
    Route::get('/daily-tasks', [DailyTaskController::class, 'index']);
    Route::post('/daily-tasks/{taskId}/complete', [DailyTaskController::class, 'complete']);
    Route::get('/daily-tasks/history', [DailyTaskController::class, 'history']);
    Route::get('/daily-tasks/today-earnings', [DailyTaskController::class, 'todayEarnings']);

    // Referrals
    Route::get('/referrals', [ReferralController::class, 'index']);
});

// Admin routes
Route::prefix('v1/admin')->middleware(['auth:sanctum', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Users Management
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/users/{id}/adjust-credit', [UserController::class, 'adjustCredit']);

    // Trainers Management
    Route::get('/trainers', [TrainerManagementController::class, 'index']);
    Route::get('/trainers/{id}', [TrainerManagementController::class, 'show']);
    Route::post('/trainers', [TrainerManagementController::class, 'store']);
    Route::put('/trainers/{id}', [TrainerManagementController::class, 'update']);
    Route::delete('/trainers/{id}', [TrainerManagementController::class, 'destroy']);
    Route::patch('/trainers/{id}/toggle-status', [TrainerManagementController::class, 'toggleStatus']);

    // Categories Management
    Route::get('/categories', [CategoryManagementController::class, 'index']);
    Route::post('/categories', [CategoryManagementController::class, 'store']);
    Route::put('/categories/{id}', [CategoryManagementController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryManagementController::class, 'destroy']);
    Route::patch('/categories/{id}/toggle-status', [CategoryManagementController::class, 'toggleStatus']);

    // Sessions Management
    Route::get('/sessions', [SessionManagementController::class, 'index']);
    Route::post('/sessions', [SessionManagementController::class, 'store']);
    Route::put('/sessions/{id}', [SessionManagementController::class, 'update']);
    Route::delete('/sessions/{id}', [SessionManagementController::class, 'destroy']);
    Route::patch('/sessions/{id}/toggle-status', [SessionManagementController::class, 'toggleStatus']);
});
