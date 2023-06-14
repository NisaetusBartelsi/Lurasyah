<?php

use App\Http\Controllers\KirimEmailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/sa', [KirimEmailController::class, 'index']);

Route::post('/send-reset-link-email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/oi', [ForgotPasswordController::class, 'oi']);
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');