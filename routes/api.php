<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CashController;
use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\ProfileUpdateController;
use App\Http\Controllers\API\TransferFundsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
/* Here is the protected route for the application, user need to login
/* to be able to access all routes in the route. */
Route::middleware('auth:sanctum')->group(function () {

    /* this for admin section protecting admin pages route section */

    /* ends here */
    //protected route goes here //

    Route::post('save_coupon', [CouponController::class, 'saveCoupon']);
    Route::post('transfer_funds', [TransferFundsController::class, 'transferFunds']);
    Route::post('cash_out', [CashController::class, 'cashOut']);
    Route::get('fetch_wallet', [CashController::class, 'walletDetails']);
    Route::get('fetch_history', [CashController::class, 'fetchHistory']);
    Route::get('fetch_history_nav', [CashController::class, 'fetchNav']);
    Route::get('user_transaction', [CashController::class, 'userTransaction']);
    Route::post('save_setting1', [ProfileUpdateController::class, 'saveSetting']);
    Route::post('save_setting2', [ProfileUpdateController::class, 'saveSetting2']);
    Route::get('fetch_settings', [ProfileUpdateController::class, 'fetchSetting']);
    Route::get('fetch_users', [ProfileUpdateController::class, 'fetchUsers']);
    Route::post('update_user', [ProfileUpdateController::class, 'updateUserProfile']);
    Route::post('update_user_image', [ProfileUpdateController::class, 'updateUserProfileImage']);
    Route::post('update_password', [ProfileUpdateController::class, 'updateUserPassword']);
    Route::post('send_ticket', [ProfileUpdateController::class, 'submitTicket']);
    Route::get('get_user', [AuthController::class, 'getLoginUser']);
    Route::get('load_user', [ProfileUpdateController::class, 'getUser']);
    Route::get('check_login', [ProfileUpdateController::class, 'getLogin']);
    Route::get('search/{key}', [ProfileUpdateController::class, 'search']);
    Route::delete('delete-history/{id}', [ProfileUpdateController::class, 'delete']);
});
// general route link goes here...

Route::post('register', [AuthController::class, 'registerUser']);
Route::post('login', [AuthController::class, 'loginUser']);
//Route::post('login2', [AuthController::class, 'loginUser2']);
Route::post('signup', [AuthController::class, 'signupUser']);



/* this for all users logout route */
Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
});