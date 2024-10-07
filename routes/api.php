<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Bill\BillController;
use \App\Http\Controllers\Bill\UsersController;
use \App\Http\Controllers\Bill\BudjetsController;
use \App\Http\Controllers\Bill\CardController;
use \App\Http\Controllers\Bill\TransactionController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

/*
 * Connectwise
 */

Route::post('auth-user', [AuthController::class, 'userAuthentication']);
Route::post('billable-options', [AuthController::class, 'billableOptions']);
Route::post('common', [AuthController::class, 'common_api']);
Route::get('agreements', [AuthController::class, 'agreements']);


/*
 * Global Common
 */

/*
 * Transaction
 */
Route::get('get-transaction-list', [TransactionController::class, 'get_transaction_list']);

/*
 * Users
 */
Route::get('user-list', [UsersController::class, 'user_list']);
//Route::post('create-user', [UsersController::class, 'create_user_with_role']);
Route::get('get-current-user-details', [UsersController::class, 'get_current_user_details']);
Route::get('get-user-details/{user_id}', [UsersController::class, 'get_user_details']);
//Route::delete('delete-user/{user_id}', [UsersController::class, 'delete_user']);
//Route::patch('update-user/{user_id}', [UsersController::class, 'update_user']);

/*
 * Budjet
 */
Route::get('budjet-list', [BudjetsController::class, 'budjet_list']);
//Route::post('create-budjet', [BudjetsController::class, 'create_budjet']);
//Route::put('add-user-to-budjet', [BudjetsController::class, 'add_user_to_budjet']);

/*
 * Virtual Credit Card
 */
//Route::post('create-card', [CardController::class, 'create_virtual_card']);
Route::get('card-list', [CardController::class, 'card_list']);
Route::get('get-card-details/{card_id}', [CardController::class, 'get_card_details']);
//Route::get('get-pan-jwt/{card_id}', [CardController::class, 'get_pan_jwt']);
//Route::post('get-jwt-to-pan', [CardController::class, 'get_jwt_to_pan']);
