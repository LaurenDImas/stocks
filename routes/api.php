<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FullFillOrderController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\OutItemController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TransferInOutController;
use App\Models\FullFillOrder;
use App\Models\Item;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
*/


Route::group(['prefix' => 'auth'], function () {
    // API LOGIN
    Route::post('/login', [AuthController::class, 'login']);
    // API REGISTER
    Route::post('/register', [AuthController::class, 'register']);
    // API LIST USERS
});

Route::group(['middleware' => 'jwt.verify'], function () {
    // PROFILE
    Route::group(['prefix' => 'auth'], function () {
        // GET DATA 
        Route::get('/user', [AuthController::class, 'userProfile']);    
        // LOGOUT
        Route::post('/logout', [AuthController::class, 'logout']);
    });
    
    // USERS
    Route::group(['prefix' => 'user'], function () {
        // LIST
        Route::get('/', [AuthController::class, 'index']);   
        // CREATE
        Route::post('/', [AuthController::class, 'register']);  
        // SHOW 
        Route::get('/{id}', [AuthController::class, 'userProfile']);   
        // UPDATE
        Route::put('/{id}', [AuthController::class, 'update']);   
        // DELETE
        Route::delete('/{id}', [AuthController::class, 'destroy']);    
    });

    // STORE
    Route::group(['prefix' => 'store'], function () {
        // LIST
        Route::get('/', [StoreController::class, 'index']);   
        // CREATE
        Route::post('/', [StoreController::class, 'store']);  
        // SHOW 
        Route::get('/{id}', [StoreController::class, 'show']);   
        // UPDATE
        Route::put('/{id}', [StoreController::class, 'update']);   
        // DELETE
        Route::delete('/{id}', [StoreController::class, 'destroy']);    
    });

    // ITEM
    Route::group(['prefix' => 'item'], function () {
        // LIST
        Route::get('/', [ItemController::class, 'index']);   
        // CREATE
        Route::post('/', [ItemController::class, 'store']);  
        // SHOW 
        Route::get('/{id}', [ItemController::class, 'show']);   
        // UPDATE
        Route::put('/{id}', [ItemController::class, 'update']);   
        // DELETE
        Route::delete('/{id}', [ItemController::class, 'destroy']);    
    });

    // Stock Opname
    Route::group(['prefix' => 'stock-opname'], function () {
        // LIST
        Route::get('/', [StockOpnameController::class, 'index']);   
        // CREATE
        Route::post('/', [StockOpnameController::class, 'store']);  
    });

    // order-item
    Route::group(['prefix' => 'order-item'], function () {
        // LIST
        Route::get('/', [OrderItemController::class, 'index']);   
        // CREATE
        Route::post('/', [OrderItemController::class, 'store']); 
    });

    // order-item
    Route::group(['prefix' => 'out-item'], function () {  
        // GET SCAN BARCODE
        Route::get('/{barcode}', [OutItemController::class, 'scanBarcode']); 
        // CREATE
        Route::post('/', [OutItemController::class, 'store']); 
    });

    // order-item
    Route::group(['prefix' => 'transfer-io'], function () {  
        // GET SCAN BARCODE
        Route::get('/stores', [TransferInOutController::class, 'stores']); 
        Route::get('/items/{storeID}', [TransferInOutController::class, 'items']); 
        
        // CREATE
        Route::post('/', [TransferInOutController::class, 'store']); 
    });

    // order-item
    Route::group(['prefix' => 'fullfill-order'], function () {  
        // GET SCAN BARCODE
        Route::get('/', [FullFillOrderController::class, 'index']); 
        
        // CREATE
        Route::post('/', [FullFillOrderController::class, 'store']); 
    });

    // order-item
    Route::group(['prefix' => 'order-history'], function () {
        // LIST
        Route::get('/', [OrderItemController::class, 'history']);   
        // CREATE
        Route::put('/{orderHistory}', [OrderItemController::class, 'update']); 
    });
});