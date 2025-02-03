<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RedsysController;

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

/* Route::get('/', function () {
    return view('welcome');
})->name('welcome'); */

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'ForgotPassword'])->middleware('guest')->name('password.email'); // Maneja form para enviar email

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'ResetPassword'])->middleware('guest')->name('password.update'); // Permite cambiar contraseña

Route::get('/pagotarjeta/ok', [RedsysController::class, 'ok'])->name('redsys.ok'); // Donde se trata la solicitud con pago Redsys correcto
Route::get('/pagotarjeta/ko', [RedsysController::class, 'ko'])->name('redsys.ko'); // Donde se trata la solicitud con pago Redsys incorrecto
Route::post('/pagotarjeta/notification', [RedsysController::class, 'notificaction'])->name('redsys.notification'); // Donde se tratan datos más sensibles con pagos ok
