<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\JobcategoryController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\JobController;

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

// Agrupación de rutas con protección de sanctum
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas sin protección con sactum
Route::get('/provinces', [ProvinceController::class, 'getProvinces'])->name('getProvinces'); // La direccion sería api/provinces
Route::resource('/jobcategories', JobcategoryController::class);

Route::resource('/jobs', JobController::class);
Route::delete('/jobs/selected/{id}', [JobController::class, 'destroySelected'])->name('destroySelectedJobs'); // Permite borrar jobs seleccionados

Route::resource('/cvs', CvController::class);