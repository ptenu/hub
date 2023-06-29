<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

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
})->name('index')->domain(env('APP_URL'));

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
Route::get('/dashboard', [AccountController::class, 'dashboard'])
    ->middleware('auth')
    ->domain(env('APP_URL'));
Route::prefix('/account')
    ->controller(AccountController::class)
    ->middleware('auth')
    ->domain(env('APP_URL'))
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
        // Membership
        Route::get('/membership', 'membership');
        Route::get('/membership/change-payment-day', 'paymentDay');
        Route::post('/membership/change-payment-day', 'paymentDay');
        Route::get('/membership/change-rate', 'rate');
        Route::post('/membership/change-rate', 'rate');
        Route::get('/membership/update-payment-method', 'getPaymentMethod');
        // Contact preferences
        Route::get('/contact-preferences', 'getContactPreferences')->name('account.contact');
        Route::get('/contact-preferences/new-email', 'addEmail')->name('account.new-email');
        Route::post('/contact-preferences/new-email', 'addEmail');
        Route::get('/contact-preferences/new-phone-number', 'addTelephone')->name('account.new-tel');
        Route::post('/contact-preferences/new-phone-number', 'addTelephone');
        Route::post('/contact-preferences/verify', 'verify')->name('account.verify');
        Route::delete('/contact-preferences/delete/email:{email}', 'deleteEmailAddress')->name('account.delete.email');
        Route::delete('/contact-preferences/delete/tel:{number}', 'deleteTelephone')->name('account.delete.tel');
    });
Route::get('/id/verify/{endpoint}/{token}', [AccountController::class, 'verifyToken'])
    ->domain(env('APP_URL'))
    ->name('verify-email');

