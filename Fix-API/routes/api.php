<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DomisiliController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ForgotPasswordAPIController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/otp', [AuthController::class, 'verifikasi_user']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/registrasi-user', [AuthController::class, 'registrasi_user']);
Route::post('/change-role/{id}',[AuthController::class,'ChangeRole']);
Route::get('/auth/google', [AuthController::class, 'redirect']);
Route::get('/auth/google/callback', [AuthController::class, 'callback']);



Route::get('/province', [DomisiliController::class, 'province']);
Route::get('/regency', [DomisiliController::class, 'regency']);
Route::get('/district', [DomisiliController::class, 'district']);
Route::get('/village', [DomisiliController::class, 'village']);



Route::post('/edit-profile/{id}', [ProfileController::class, 'edit']);
Route::post('/update-profile/{id}', [ProfileController::class, 'update']);
Route::post('/delete-profile/{id}', [ProfileController::class, 'delete']);


Route::post('/company-images/{id}', [CompanyController::class, 'CompanyImages']);
Route::post('/delete-company-images/{id}', [CompanyController::class, 'deleteCompanyImages']);
Route::post('/comment-images/{id}', [CompanyController::class, 'addcomment']);
Route::post('/like-images/{id}', [CompanyController::class, 'addlike']);
Route::post('/create-admin/{id}', [CompanyController::class, 'CreateAdmin']);

Route::post('/email-forgot-pass', [ForgotPasswordAPIController::class, 'SendGmailUser']);
Route::post('/token-link/{id}', [ForgotPasswordAPIController::class, 'GetLinkToken']);
Route::post('/form-reset-password', [ForgotPasswordAPIController::class, 'ChangePassword']);

Route::get('/images', [ShowController::class, 'showAllImages']);