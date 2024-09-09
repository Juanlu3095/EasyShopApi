<?php

use App\Http\Controllers\BrandController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\JobcategoryController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProductcategoryController;
use App\Http\Controllers\ProductController;

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
Route::get('/cvs/empleo/{idEmpleo}', [CvController::class, 'indexByJob'])->name('showCVsbyJob'); // Obtiene los CVs inscritos a una oferta de empleo específica

Route::resource('/messages', MessageController::class);

Route::resource('/newsletters', NewsletterController::class);
Route::get('exportarnews', [NewsletterController::class, 'export'])->name('exportnews');

Route::resource('/productcategories', ProductcategoryController::class);
Route::resource('/brand', BrandController::class);
Route::resource('/product', ProductController::class);
Route::resource('/image', ImageController::class);