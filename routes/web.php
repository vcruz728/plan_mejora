<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Planes\PlanesMejoraController;
use App\Http\Controllers\Generales\UsuariosController;
use App\Http\Controllers\Generales\CatalogosController;

use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;

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
// Flujo "Olvidé mi contraseña" (guest)
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
	->middleware('guest')->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
	->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
	->middleware('guest')->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
	->middleware('guest')->name('password.store');

// Cambio de contraseña estando logueado (perfil)
Route::put('/password', [PasswordController::class, 'update'])
	->middleware('auth')->name('password.update');

Route::get('/', function () {
	return redirect()->route('login');
});

Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
	->name('password.request');

Route::get('/dashboard', function () {
	return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



	Route::get('/get/planes-mejora', [PlanesMejoraController::class, 'getPlanes']);
	Route::get('/get/acciones/plan/{id}', [PlanesMejoraController::class, 'getAcciones']);
	Route::get('/get/info/programa-educativo/{id}', [PlanesMejoraController::class, 'getinfoPrograma']);
	Route::delete('/admin/elimina-meta/{id}', [PlanesMejoraController::class, 'delMejora']);


	// Admin
	Route::get('/admin/edita/plan-mejora/{id}', [PlanesMejoraController::class, 'adminEdita']);
	Route::post('/admin/guarda/actualizacion/plan-mejora', [PlanesMejoraController::class, 'editPlan']);
	Route::get('/admin/ver/plan-mejora/{id}', [PlanesMejoraController::class, 'adminVer']);
	Route::get('/admin/agregar/nuvea', [PlanesMejoraController::class, 'viewAlta']);
	Route::post('/admin/guarda/nuevo/plan/mejora', [PlanesMejoraController::class, 'addNuevo']);

	Route::get('/admin/nuevo/usuario', [UsuariosController::class, 'viewAlta']);
	Route::post('/admin/guarda/nuevo/usuario', [UsuariosController::class, 'addNuevo']);
	Route::get('/edita/usuario/{id}', [UsuariosController::class, 'viewEdit']);
	Route::post('/admin/edita/usuario', [UsuariosController::class, 'editUser']);
	Route::get('/admin/lista/usuario', [UsuariosController::class, 'listaUsuarios']);
	Route::get('/admin/get/usuarios/sistema', [UsuariosController::class, 'getUsuarios']);
	Route::get('/admin/get/informacion/usuario/{id}', [UsuariosController::class, 'getInfoUser']);
	Route::post('/admin/edita/usuario/{id}', [UsuariosController::class, 'editUsuario']);
	Route::post('/admin/usuario/resetea-password/{id}', [UsuariosController::class, 'resetPassword']);
	Route::get('/admin/consultores', [UsuariosController::class, 'viewAltaRev']);
	Route::get('admin/get/usuarios/sistema/consultores', [UsuariosController::class, 'getConsultores']);
	Route::post('/admin/edita/consultor/{id}', [UsuariosController::class, 'editConsultor']);

	Route::post('admin/guarda/nuevo/consultor', [UsuariosController::class, 'saveConsultor']);

	Route::get('/admin/catalogo/procedencias', [CatalogosController::class, 'viewProcedencias']);
	Route::get('/admin/get/catalogo/procedencia', [CatalogosController::class, 'getProcedencias']);
	Route::post('/admin/edita/catalogo/procedencia/{id}', [CatalogosController::class, 'editProcedencia']);
	Route::post('/admin/catalogo/nueva/procedencia', [CatalogosController::class, 'addProcedencia']);
	Route::delete('/admin/catalogo/elimina/procedencia/{id}', [CatalogosController::class, 'delProcedencia']);
	Route::get('/admin/get/objetivos-pdi/{id}', [PlanesMejoraController::class, 'getObjetivosPdi']);
	Route::get('/admin/get/estrategias-pdi/{id}', [PlanesMejoraController::class, 'getEstrategiasPdi']);
	Route::get('/admin/get/metas-pdi/{id}', [PlanesMejoraController::class, 'getMetasPdi']);

	Route::get('/admin/catalogo/ambito-siemec', [CatalogosController::class, 'ambitoSiemec']);
	Route::get('/admin/get/catalogo/ambitos-siemec', [CatalogosController::class, 'getAmbitosSiemec']);
	Route::post('/admin/edita/catalogo/ambito-siemec/{id}', [CatalogosController::class, 'editAmbitoSiemec']);
	Route::post('/admin/catalogo/nuevo/ambito-siemec', [CatalogosController::class, 'addAmbitoSiemec']);
	Route::delete('/admin/catalogo/elimina/ambito-siemec/{id}', [CatalogosController::class, 'delAmbitoSiemec']);

	Route::get('/admin/catalogo/criterio-siemec', [CatalogosController::class, 'criterioSiemec']);
	Route::get('/admin/get/catalogo/criterios-siemec', [CatalogosController::class, 'getCriteriosSiemec']);
	Route::post('/admin/edita/catalogo/criterio-siemec/{id}', [CatalogosController::class, 'editCriterioSiemec']);
	Route::post('/admin/catalogo/nuevo/criterio-siemec', [CatalogosController::class, 'addCriterioSiemec']);
	Route::delete('/admin/catalogo/elimina/criterio-siemec/{id}', [CatalogosController::class, 'delCriterioSiemec']);

	Route::get('/admin/get/ods-pdi/{id}', [PlanesMejoraController::class, 'getOdsPdi']);

	Route::get('/admin/get/des-o-dependencias/{valor}', [PlanesMejoraController::class, 'getDesDep']);
	Route::get('/admin/get/unidades/{id}', [PlanesMejoraController::class, 'getUnidades']);
	Route::get('/admin/get/sedes/{id}', [PlanesMejoraController::class, 'getSedes']);
	Route::get('/admin/get/programas/{id}', [PlanesMejoraController::class, 'getProgramas']);
	Route::get('/admin/get/niveles/{id}', [PlanesMejoraController::class, 'getNiveles']);
	Route::get('/admin/get/modalidades/{id}', [PlanesMejoraController::class, 'getModalidades']);
	Route::get('/admin/get/siglas-procedencia/{id}', [CatalogosController::class, 'getSigla']);




	// Unidadas académica
	Route::get('/edita/plan-mejora/{id}', [PlanesMejoraController::class, 'edita']);
	Route::post('/guarda/nueva-accion', [PlanesMejoraController::class, 'saveAccion']);
	Route::delete('/elimina/accion/{id}', [PlanesMejoraController::class, 'delAccion']);
	Route::get('/get/detalle/accion/{id}', [PlanesMejoraController::class, 'detalleAccion']);
	Route::post('/edita/accion', [PlanesMejoraController::class, 'editAccion']);
	Route::post('/guarda/complemento/plan', [PlanesMejoraController::class, 'saveComplemento']);
	Route::post('/guarda/indicador-clave/plan', [PlanesMejoraController::class, 'saveIndicador']);
	Route::delete('/elimina/archivo/acciones/{id}', [PlanesMejoraController::class, 'delArcivo']);
});



require __DIR__ . '/auth.php';
