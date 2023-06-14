<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DomisiliController;
use App\Http\Controllers\ImageController;

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

Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/registrasi-user', [AuthController::class, 'registrasi_user']);
Route::post('/registrasi-admin/{id}', [AuthController::class, 'registrasi_admin']);
Route::get('/province', [DomisiliController::class, 'province']);
Route::get('/regency', [DomisiliController::class, 'regency']);
Route::get('/district', [DomisiliController::class, 'district']);
Route::post('/index', [ImageController::class, 'index']);
Route::post('/create', [ImageController::class, 'create']);
Route::post('/store', [ImageController::class, 'store']);
Route::post('/edit/{id}', [ImageController::class, 'edit']);
Route::post('/update/{id}', [ImageController::class, 'update']);
Route::post('/delete/{id}', [ImageController::class, 'delete']);

