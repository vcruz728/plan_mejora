<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Planes\PlanesMejoraController;
use App\Http\Controllers\Generales\UsuariosController;
use App\Http\Controllers\Generales\CatalogosController;

use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;

use App\Models\Catalogos\Procedencias;
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


Route::get('/dashboard', function () {
	$procedencias = \App\Models\Catalogos\Procedencias::orderBy('descripcion')->get();
	return view('dashboard', compact('procedencias'));
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
	Route::get('/admin/agregar/nueva', [PlanesMejoraController::class, 'viewAlta']);
	Route::post('/admin/guarda/nuevo/plan/mejora', [PlanesMejoraController::class, 'addNuevo']);
	Route::get('/admin/planes-mejora', function () {
		$procedencias = Procedencias::orderBy('descripcion')->get();
		return view('dashboard', compact('procedencias'));
	})->middleware(['auth', 'verified']);


	// LISTA + DATOS
	Route::get('/admin/lista/usuario', [UsuariosController::class, 'listaUsuarios']);

	Route::get('/admin/get/usuarios/sistema', [UsuariosController::class, 'getUsuarios']); // acepta filtros

	Route::get('/admin/get/informacion/usuario/{id}', [UsuariosController::class, 'getInfoUser']);
	// EDITAR (vista + post)
	Route::get('/edita/usuario/{id}', [UsuariosController::class, 'viewEdit']);
	//Route::post('/admin/edita/usuario', [UsuariosController::class, 'editUser']); // ya la tienes
	Route::post('/admin/edita/usuario/{id}', [UsuariosController::class, 'editUser']);

	// NUEVO (vista + post)
	Route::get('/admin/nuevo/usuario', [UsuariosController::class, 'viewAlta']);
	Route::post('/admin/guarda/nuevo/usuario', [UsuariosController::class, 'addNuevo']);

	// RESET PASSWORD (con modal o “one-click” si mandas action=reset)
	Route::post('/admin/usuario/resetea-password/{id}', [UsuariosController::class, 'resetPassword']);

	// ELIMINAR
	Route::delete('/admin/usuario/{id}', [UsuariosController::class, 'destroyUsuario']);

	Route::get('/admin/consultores', [UsuariosController::class, 'viewAltaRev']);
	Route::get('admin/get/usuarios/sistema/consultores', [UsuariosController::class, 'getConsultores']);
	Route::post('/admin/edita/consultor/{id}', [UsuariosController::class, 'editConsultor']);
	Route::post('admin/guarda/nuevo/consultor', [UsuariosController::class, 'saveConsultor']);
	Route::delete('/admin/consultor/{id}', [UsuariosController::class, 'destroyConsultor']);


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
	Route::post('/sube/evidencia/complemento', [PlanesMejoraController::class, 'uploadComplementEvidence']);
	Route::delete('elimina/archivo/complemento/{id_plan}', [PlanesMejoraController::class, 'delArchivoComplemento']);
	Route::post('/guarda/indicador-clave/plan', [PlanesMejoraController::class, 'saveIndicador']);
	Route::delete('/elimina/archivo/acciones/{id}', [PlanesMejoraController::class, 'delArchivo']);

	//Actividades de control
	Route::get('/get/actividades/control/{idPlan}', [PlanesMejoraController::class, 'getActividadesControl']);
	Route::get('/get/detalle/actividad/{id}',     [PlanesMejoraController::class, 'detalleActividadControl']);
	Route::post('/guarda/actividad-control',       [PlanesMejoraController::class, 'saveActividadControl']);
	Route::post('/edita/actividad-control',        [PlanesMejoraController::class, 'editActividadControl']);
	Route::delete('/elimina/actividad-control/{id}', [PlanesMejoraController::class, 'delActividadControl']);

	Route::get('/reportes/mejoras/export', [\App\Http\Controllers\ReporteController::class, 'exportMejoras'])
		->name('reportes.mejoras.export');
});



require __DIR__ . '/auth.php';
