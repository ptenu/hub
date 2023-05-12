<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AccountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('index');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'requestPassword']);
Route::post('/authenticate', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Account
|--------------------------------------------------------------------------
*/
Route::prefix('/account')
    ->controller(AccountController::class)
    ->middleware('auth')
    ->group(function () {
    Route::get('/', 'index');
    // Address
    Route::get('/update-address', 'updateAddress');
    Route::post('/update-address', 'saveAddress');
    Route::post('/update-address/save', 'saveInterest');
    // Personal details
    Route::get('/update-details', 'detailsForm');
    Route::post('/update-details', 'saveDetails');
    // Tenancy information
    Route::get('/tenancy', 'viewTenancies');
    Route::get('/tenancy/update', 'updateTenancy');
    Route::post('/tenancy/update', 'saveTenancy');
});
