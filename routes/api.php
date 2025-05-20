<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use Illuminate\Support\Facades\Route;

Route::post('signup',[AuthController::class,'SignUp']);
Route::post('login',[AuthController::class,'Login']);
// Route::post('login', [AuthController::class, 'Login'])->name('login');
// Route::get('login', function () {
//     return response()->json([
//         'message' => 'Use POST method to login'
//     ], 405); // Method Not Allowed
// })->name('login.get');


Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout',[AuthController::class,'Logout']);
    Route::apiResource('posts',PostController::class);
});
