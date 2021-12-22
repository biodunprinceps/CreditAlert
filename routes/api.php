<?php

use Illuminate\Http\Request;
use App\services\SchedulerServices;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\TestController;

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

Route::prefix('v1')->group(function () {


    Route::prefix('admin')->group(function () {
        Route::post('register',[AdminAuthController::class, 'createAdmin']);
        Route::post('login',[AdminAuthController::class, 'credentials']);
        Route::post('password/change',[AdminAuthController::class, 'changePassword']);
        Route::post('profile/edit',[AdminAuthController::class, 'editAdmin']);
        Route::get('profile',[AdminAuthController::class, 'adminProfile']);
    });


    Route::prefix('loan')->group(function () {
        Route::post('apply',[LoanController::class, 'apply']);
        Route::post('passport/upload',[LoanController::class, 'storePassport']);
        Route::post('one',[LoanController::class, 'oneLoan']);
        Route::post('all',[LoanController::class, 'allLoansByStatus']);
        Route::post('submit',[LoanController::class, 'submitApplication']);
        Route::post('status/change',[LoanController::class, 'changeLoanStatus']);
    });

    Route::prefix('user')->group(function () {
        Route::post('info/add',[LoanController::class, 'saveCustomerInfo']);
    });



    Route::get('test', function () {
        return SchedulerServices::processPayment();
    });

    // Route::prefix('test')->group(function () {
    //     Route::post('new',[TestController::class, 'test']);
    // });
});
