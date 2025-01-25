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
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderitemController;
use App\Http\Controllers\OrderstatusController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentmethodController;
use App\Http\Controllers\RedsysController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShippingmethodController;
use App\Models\Shippingmethod;

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

    /* CATEGORÍAS DE PRODUCTO */
    Route::resource('/productcategories', ProductcategoryController::class)->except(['index'. 'show']);

    /* MARCAS DE PRODUCTO */
    Route::resource('/brand', BrandController::class)->except(['index', 'show']);

    /* PRODUCTOS */
    Route::resource('/product', ProductController::class)->except(['index', 'show']);

    /* CUPONES */
    Route::resource('/coupons', CouponController::class);

    /* MÉTODOS DE PAGO */
    Route::get('/metodopago', [PaymentmethodController::class, 'index']);
    Route::get('/metodopago/{slug}', [PaymentmethodController::class, 'show']);
    Route::patch('/switchactivo/{slug}', [PaymentmethodController::class, 'switchActivo']); // Sólo se actualizará el 'activo' del método de pago
    Route::put('/metodopago/{slug}', [PaymentmethodController::class, 'update']);

    /* PEDIDOS */
    Route::resource('/pedidos', OrderController::class)->except(['store']);
    Route::post('pedidosadmin', [OrderController::class, 'storeAdmin']);
    Route::post('/emailpedido', [OrderController::class, 'sendEmail']);

    /* ESTADOS DE LOS PEDIDOS */
    Route::get('/estadospedido', [OrderstatusController::class, 'index']);

    /* PEDIDOS ITEM*/
    Route::get('pedidositem/{idPedido}', [OrderitemController::class, 'indexByOrderId']);
    Route::resource('/pedidoitem', OrderitemController::class);

    /* VENTAS */
    Route::get('ventasporbeneficio', [SaleController::class, 'indexByBenefits']);
    Route::get('ventasporcantidad', [SaleController::class, 'indexByQuantity']);
    
    /* CONFIGURACIÓN */
    Route::resource('/ajustes', SettingController::class)->only(['index', 'update']);
    Route::post('/ajuste', [SettingController::class, 'showbyname']);
});

// Grupo de rutas con middleware Sanctum y email verificado (Para clientes)
Route::middleware('auth:sanctum', 'verified')->group(function () {

    /* USUARIOS */
    Route::get('/dataclient', [AuthController::class, 'dataclient'])->name('client.data');
    Route::post('/actualizarcliente', [AuthController::class, 'actualizarCliente']); // Para actualizar el usuario desde el panel del cliente
    Route::post('/cerrarsesioncliente', [AuthController::class, 'logout']);

    /* PEDIDOS */
    Route::get('/pedidoscliente', [OrderController::class, 'indexByClient']);
    Route::post('/pedidocliente', [OrderController::class, 'showToClient']);

    /* PEDIDOS ITEM */
    Route::post('/pedidoitemclient', [OrderitemController::class, 'getPedidosItemClient']);

    /* MÉTODOS DE ENVÍO */
    Route::resource('/metodosenvio', ShippingmethodController::class)->except(['index']);
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
    Route::resource('/productcategories', ProductcategoryController::class)->only(['index', 'show']);

    /* MARCAS DE PRODUCTO */
    Route::resource('/brand', BrandController::class)->only(['index', 'show']);

    /* PRODUCTOS */
    Route::resource('/product', ProductController::class)->only(['index', 'show']);
    Route::get('/productospublicados', [ProductController::class, 'indexPublished']);
    Route::get('/novedades', [ProductController::class, 'indexNovedades']);
    Route::get('/productsByCategory/{slug}', [ProductController::class, 'indexPorCategoria']);
    Route::get('/productsByBrand/{id}', [ProductController::class, 'indexPorMarca']);
    Route::post('/relatedProducts', [ProductController::class, 'productosRelacionados']);
    Route::post('/filtrarProductos', [ProductController::class, 'filtrarProductos']);
    Route::get('/buscarProductos', [ProductController::class, 'buscarProductos']);
    Route::get('/obtenerProductosId/{idArray}', [ProductController::class, 'indexById']);

    /* CUPONES */
    Route::post('/codigocupon', [CouponController::class, 'showByCode']);

    /* MÉTODOS DE PAGO */
    Route::get('/pagosdisponibles', [PaymentmethodController::class, 'indexClient']); // Sólo para el frontend
    Route::get('/transferencia', [PaymentmethodController::class, 'showTransferencia']);

    /* PEDIDOS */
    Route::resource('/pedidos', OrderController::class)->only(['store']);

    /* MÉTODOS DE ENVÍO */
    Route::resource('/metodosenvio', ShippingmethodController::class)->only(['index']);

    /* REDSYS */
    Route::post('/pagotarjeta', [RedsysController::class, 'pagoTarjeta'])->name('redsys.pagar');