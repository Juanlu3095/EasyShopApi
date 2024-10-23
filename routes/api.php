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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Product;

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

// Gestión de Auth
Route::post('/userlogin', [AuthController::class, 'loginUser']); // Ruta para iniciar sesión para usuario
Route::post('/adminlogin', [AuthController::class, 'loginAdmin']); // Ruta para iniciar sesión para administrador
Route::post('/registro', [AuthController::class, 'registroCliente']); // Ruta para crear usuarios con rol de cliente
Route::middleware('auth:sanctum')->post('/comprobarusuario', [AuthController::class, 'validarTokenA']); // Ruta para comprobar si el usuario está logueado
Route::middleware('auth:sanctum')->post('/cerrarsesion', [AuthController::class, 'logout']); // Ruta para cerrar sesión y eliminar token. Importante el middleware

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verificarEmail'])->middleware(['signed'])->name('verification.verify'); // El middleware signed es para que no se modifique la url y para comprobar que no haya caducado, para que sea usada por el usuario correcto 

// Grupo de rutas con middleware SANCTUM, email verificado y rol admin (Para administradores)
Route::middleware('auth:sanctum', 'admin', 'verified')->group(function () {

    /* USUARIOS */
    Route::resource('/usuario', UserController::class);

    /* ROLES */
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

    /* CATEGORÍAS DE EMPLEO*/
    Route::resource('/jobcategories', JobcategoryController::class)->except(['index', 'show']);

    /* EMPLEO */
    Route::resource('/jobs', JobController::class)->except(['index', 'show']);
    Route::delete('/jobs/selected/{id}', [JobController::class, 'destroySelected'])->name('destroySelectedJobs'); // Permite borrar jobs seleccionados

    /* CVs */
    Route::resource('/cvs', CvController::class)->except(['store']);
    Route::get('/cvs/empleo/{idEmpleo}', [CvController::class, 'indexByJob'])->name('showCVsbyJob'); // Obtiene los CVs inscritos a una oferta de empleo específica

    /* MENSAJES */
    Route::resource('/messages', MessageController::class)->except(['store']);

    /* NEWSLETTERS */
    Route::resource('/newsletters', NewsletterController::class)->except(['store']);
    Route::get('exportarnews', [NewsletterController::class, 'export'])->name('exportnews');

    /* IMÁGENES */
    Route::resource('/image', ImageController::class);
});

// Grupo de rutas con middleware Sanctum y email verificado (Para clientes)
Route::middleware('auth:sanctum', 'verified')->group(function () {

    /* USUARIOS */
    Route::get('/dataclient', [AuthController::class, 'dataclient'])->name('client.data');
});

// Rutas sin protección con sanctum (Para usuarios que no necesitan estar logueados)

    /* PROVINCIAS */
    Route::get('/provinces', [ProvinceController::class, 'getProvinces'])->name('getProvinces'); // La direccion sería api/provinces

    /* CATEGORÍAS DE EMPLEO*/
    Route::resource('/jobcategories', JobcategoryController::class)->only(['index', 'show']);

    /* EMPLEO */
    Route::post('/jobs/filter', [JobController::class, 'filter'])->name('filterJobs'); // Permite filtrar jobs, es post porque se envia una request
    Route::resource('/jobs', JobController::class)->only(['index', 'show']);

    /* CVs */
    Route::resource('/cvs', CvController::class)->only(['store']);

    /* MENSAJES */
    Route::resource('/messages', MessageController::class)->only(['store']);

    /* NEWSLETTERS */
    Route::resource('/newsletters', NewsletterController::class)->only(['store']);

    /* CATEGORÍAS DE PRODUCTO */
    Route::resource('/productcategories', ProductcategoryController::class);

    /* MARCAS DE PRODUCTO */
    Route::resource('/brand', BrandController::class);

    /* PRODUCTOS */
    Route::resource('/product', ProductController::class);
    Route::get('/novedades', [ProductController::class, 'indexNovedades']);

    /* IMÁGENES */
    //Route::resource('/image', ImageController::class); // En principio esta clase se maneja desde el panel de admin sólo, habrá que verlo cuando se empiece con los productos.