<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/webservices/ua', 'WebServicesController@ua');
Route::get('webservices/concesionaria', 'WebServicesController@concesionaria')->middleware('cors');
Route::get('/webservices/responsable', 'WebServicesController@responsable');
Route::get('/webservices/marca', 'WebServicesController@marca');
Route::get('/webservices/area', 'WebServicesController@area');
Route::get('/webservices/contratista', 'WebServicesController@contratista');
Route::get('/webservices/kilometraje', 'WebServicesController@kilometraje');
Route::get('/webservices/carroceria', 'WebServicesController@carroceria');
Route::get('/webservices/vehiculo', 'WebServicesController@vehiculo');
Route::get('/webservices/usuario', 'WebServicesController@usuario');
Route::get('/webservices/userconcesionaria', 'WebServicesController@userconcesionaria');
Route::get('/webservices/conductor', 'WebServicesController@conductor');
Route::get('/webservices/conductorconcesionaria', 'WebServicesController@conductorconcesionaria');
Route::get('/webservices/grifo', 'WebServicesController@grifo');
Route::get('/webservices/equipo', 'WebServicesController@equipo');
Route::get('/webservices/unidad', 'WebServicesController@unidad');
Route::get('/webservices/abastecimiento', 'WebServicesController@abastecimiento');
Route::get('/webservices/abastecimientocombustible', 'WebServicesController@abastecimientocombustible');
Route::get('/webservices/tipocombustible', 'WebServicesController@tipocombustible');
Route::get('/webservices/checklist', 'WebServicesController@checklist');
Route::get('/webservices/control', 'WebServicesController@control');
Route::get('/webservices/agente', 'WebServicesController@agente');
Route::get('/webservices/persona', 'WebServicesController@persona');
Route::get('/webservices/eliminarPersona', 'WebServicesController@eliminarPersona');
Route::get('/webservices/empresa', 'WebServicesController@empresa');

//Clear Cache facade value:
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});
//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});
//Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});
//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});
//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});
//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

Route::get('/', function () {
    //return view('welcome');
    return view('auth.login');
});
Route::get('/asd', function () {
    //return view('welcome');
    return view('auth.login');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');

Route::group(['middleware' => 'auth'], function () {
	Route::get('table-list', function () {
		return view('pages.table_list');
	})->name('table');

	Route::get('typography', function () {
		return view('pages.typography');
	})->name('typography');

	Route::get('icons', function () {
		return view('pages.icons');
	})->name('icons');

	Route::get('map', function () {
		return view('pages.map');
	})->name('map');

	Route::get('notifications', function () {
		return view('pages.notifications');
	})->name('notifications');

	Route::get('rtl-support', function () {
		return view('pages.language');
	})->name('language');

	Route::get('upgrade', function () {
		return view('pages.upgrade');
	})->name('upgrade');

	Route::post('opcionmenu/buscar', 'OpcionmenuController@buscar')->name('opcionmenu.buscar');
    Route::get('opcionmenu/eliminar/{id}/{listarluego}', 'OpcionmenuController@eliminar')->name('opcionmenu.eliminar');
    Route::resource('opcionmenu', 'OpcionmenuController', array('except' => array('show')));

});

Route::group(['middleware' => 'auth'], function () {
    Route::post('bitacora/buscar', 'BitacoraController@buscar')->name('bitacora.buscar');
    Route::get('bitacora/eliminar/{id}/{listarluego}', 'BitacoraController@eliminar')->name('bitacora.eliminar');
	Route::resource('bitacora', 'BitacoraController', array('except' => array('show')));
	
	Route::resource('user', 'UserController', ['except' => ['show']]);
	Route::post('user/buscar', 'UserController@buscar')->name('user.buscar');

	Route::get('user/eliminar/{id}/{listarluego}', 'UserController@eliminar')->name('user.eliminar');
	Route::get('user/activar/{id}/{listarluego}', 'UserController@activar')->name('user.activar');
	Route::get('user/reactivar/{id}', 'UserController@reactivar')->name('user.reactivar');
	Route::get('user/enviarCorreo', 'UserController@enviarCorreo')->name('user.enviarCorreo');

	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
	Route::post('profile/update', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::post('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);

	Route::post('opcionmenu/buscar', 'OpcionmenuController@buscar')->name('opcionmenu.buscar');
    Route::get('opcionmenu/eliminar/{id}/{listarluego}', 'OpcionmenuController@eliminar')->name('opcionmenu.eliminar');
	Route::resource('opcionmenu', 'OpcionmenuController', array('except' => array('show')));

	//Rutas ua
	Route::post('ua/buscar', 'UaController@buscar')->name('ua.buscar');
	Route::get('ua/eliminar/{id}/{listarluego}', 'UaController@eliminar')->name('ua.eliminar');
	Route::get('ua/search/{query}', 'UaController@searchAutocomplete')->name('ua.search');
	Route::get('ua/search2/{query}', 'UaController@searchAutocomplete2')->name('ua.search2');
	Route::get('ua/searchTodos/{query}', 'UaController@searchAutocompleteTodos')->name('ua.searchTodos');
	Route::post('ua/importar', 'UaController@importExcel')->name('ua.excel.import');
	Route::get('ua/exportar', 'UaController@exportExcel')->name('ua.excel.export');
	Route::get('ua/exportar2', 'UaController@exportExcel2')->name('ua.excel.export2');
	Route::post('ua/deletelist', 'UaController@destroyList')->name('ua.deletelist');
	Route::post('ua/deleteall', 'UaController@destroyAll')->name('ua.deleteall');
	Route::resource('ua', 'UaController');

	//Rutas unidad
	Route::post('unidad/buscar', 'UnidadController@buscar')->name('unidad.buscar');
	Route::get('unidad/eliminar/{id}/{listarluego}', 'UnidadController@eliminar')->name('unidad.eliminar');
	Route::resource('unidad', 'UnidadController');

	//Rutas configuracion
	Route::post('configuracion/buscar', 'ConfiguracionController@buscar')->name('configuracion.buscar');
	Route::get('configuracion/eliminar/{id}/{listarluego}', 'ConfiguracionController@eliminar')->name('configuracion.eliminar');
	Route::resource('configuracion', 'ConfiguracionController');

	//Rutas responsable
	Route::post('responsable/buscar', 'ResponsableController@buscar')->name('responsable.buscar');
	Route::get('responsable/eliminar/{id}/{listarluego}', 'ResponsableController@eliminar')->name('responsable.eliminar');
	Route::resource('responsable', 'ResponsableController');
	
	//Rutas abastecimiento de combustible
	Route::post('abastecimiento/buscar', 'AbastecimientoCombustibleController@buscar')->name('abastecimiento.buscar');
	Route::get('abastecimiento/eliminar/{id}/{listarluego}', 'AbastecimientoCombustibleController@eliminar')->name('abastecimiento.eliminar');
	Route::get('abastecimiento/search/grifo/{query}', 'AbastecimientoCombustibleController@searchAutocompleteGrifo')->name('abastecimiento.search.grifo');
	Route::get('abastecimiento/search/conductor/{query}', 'AbastecimientoCombustibleController@searchAutocompleteConductor')->name('abastecimiento.search.conductor');
	Route::get('abastecimiento/search/equipo/{query}', 'AbastecimientoCombustibleController@searchAutocompleteEquipo')->name('abastecimiento.search.equipo');
	Route::get('abastecimiento/exportar', 'AbastecimientoCombustibleController@exportExcel')->name('abastecimiento.excel.export');
	Route::get('abastecimiento/generateReport', 'AbastecimientoCombustibleController@generateReport')->name('abastecimiento.generateReport');
	Route::get('abastecimiento/exportExcelReport', 'AbastecimientoCombustibleController@exportExcelReport')->name('abastecimiento.exportExcelReport');
	Route::resource('abastecimiento', 'AbastecimientoCombustibleController');
	Route::post('abastecimiento/controldiario', 'AbastecimientoCombustibleController@exportControlDiario')->name('abastecimiento.controldiario');

    //Rutas Tipohora
	Route::post('tipohora/buscar', 'TipohoraController@buscar')->name('tipohora.buscar');
	Route::get('tipohora/eliminar/{id}/{listarluego}', 'TipohoraController@eliminar')->name('tipohora.eliminar');
	Route::resource('tipohora', 'TipohoraController', array('except' => array('show')));

	//Rutas controldiario
	Route::post('controldiario/buscar', 'ControlDiarioController@buscar')->name('controldiario.buscar');
	Route::post('controldiario/store2', 'ControlDiarioController@store2')->name('controldiario.store2');
	Route::get('controldiario/hequipoxua', 'ControlDiarioController@HEquipoxUa')->name('controldiario.hequipoxua');
	Route::get('controldiario/hequipoxuavertical', 'ControlDiarioController@HEquipoxUaVertical')->name('controldiario.hequipoxuavertical');
	Route::get('controldiario/hequipoxuapdf', 'ControlDiarioController@HEquipoxUapdf')->name('controldiario.hequipoxuapdf');
	Route::get('controldiario/transporteexcel', 'ControlDiarioController@transporteExcel')->name('controldiario.transporteexcel');
	Route::get('controldiario/equipodiaexcel', 'ControlDiarioController@equipodiaExcel')->name('controldiario.equipodiaexcel');
	Route::get('controldiario/eliminar/{id}/{listarluego}', 'ControlDiarioController@eliminar')->name('controldiario.eliminar');
	Route::get('controldiario/detail', 'ControlDiarioController@detail')->name('controldiario.detail');
	Route::post('controldiario/horometroFinal', 'ControlDiarioController@horometroFinal')->name('controldiario.horometroFinal');
	Route::resource('controldiario', 'ControlDiarioController', array('except' => array('show')));
	
	//Rutas controldiariovehicular
	Route::post('controldiariovehiculo/buscar', 'ControlDiarioVehiculoController@buscar')->name('controldiariovehiculo.buscar');
	Route::get('controldiariovehiculo/eliminar/{id}/{listarluego}', 'ControlDiarioVehiculoController@eliminar')->name('controldiariovehiculo.eliminar');
	Route::resource('controldiariovehiculo', 'ControlDiarioVehiculoController', array('except' => array('show')));

	//Rutas Grifo
	Route::post('grifo/buscar', 'GrifoController@buscar')->name('grifo.buscar');
	Route::get('grifo/eliminar/{id}/{listarluego}', 'GrifoController@eliminar')->name('grifo.eliminar');
	Route::resource('grifo', 'GrifoController', array('except' => array('show')));

	//Rutas tipocombustible
	Route::post('tipocombustible/buscar', 'TipocombustibleController@buscar')->name('tipocombustible.buscar');
	Route::get('tipocombustible/eliminar/{id}/{listarluego}', 'TipocombustibleController@eliminar')->name('tipocombustible.eliminar');
	Route::resource('tipocombustible', 'TipocombustibleController', array('except' => array('show')));

	//Rutas preciocombustible
	Route::post('preciocombustible/buscar', 'PreciocombustibleController@buscar')->name('preciocombustible.buscar');
	Route::get('preciocombustible/eliminar/{id}/{listarluego}', 'PreciocombustibleController@eliminar')->name('preciocombustible.eliminar');
	Route::get('preciocombustible/exportar', 'PreciocombustibleController@exportar')->name('preciocombustible.exportar');
	Route::resource('preciocombustible', 'PreciocombustibleController', array('except' => array('show')));

	//Rutas Categoria repuesto
	Route::post('categoriarepuesto/buscar', 'CategoriarepuestoController@buscar')->name('categoriarepuesto.buscar');
	Route::get('categoriarepuesto/eliminar/{id}/{listarluego}', 'CategoriarepuestoController@eliminar')->name('categoriarepuesto.eliminar');
	Route::resource('categoriarepuesto', 'CategoriarepuestoController', array('except' => array('show')));

	//Rutas Carroceria
	Route::post('carroceria/buscar', 'CarroceriaController@buscar')->name('carroceria.buscar');
	Route::get('carroceria/eliminar/{id}/{listarluego}', 'CarroceriaController@eliminar')->name('carroceria.eliminar');
	Route::resource('carroceria', 'CarroceriaController', array('except' => array('show')));

	//Rutas Equipo
	Route::post('equipo/buscar', 'EquipoController@buscar')->name('equipo.buscar');
	Route::get('equipo/eliminar/{id}/{listarluego}', 'EquipoController@eliminar')->name('equipo.eliminar');
	Route::get('equipo/search/{query}', 'EquipoController@searchAutocomplete')->name('equipo.search');
	Route::get('equipo/exportar', 'EquipoController@exportar')->name('equipo.exportar');
	Route::get('equipo/exportarDoc', 'EquipoController@exportarDoc')->name('equipo.exportarDoc');
	Route::resource('equipo', 'EquipoController', array('except' => array('show')));
	
	//Rutas Vehiculo
	Route::post('vehiculo/buscar', 'VehiculoController@buscar')->name('vehiculo.buscar');
	Route::get('vehiculo/eliminar/{id}/{listarluego}', 'VehiculoController@eliminar')->name('vehiculo.eliminar');
	Route::get('vehiculo/search/{query}', 'VehiculoController@searchAutocomplete')->name('vehiculo.search');
	Route::get('vehiculo/exportar', 'VehiculoController@exportar')->name('vehiculo.exportar');
	Route::get('vehiculo/exportarDoc', 'VehiculoController@exportarDoc')->name('vehiculo.exportarDoc');
	Route::get('vehiculo/exportarDoc2', 'VehiculoController@exportarDoc2')->name('vehiculo.exportarDoc2');
	Route::resource('vehiculo', 'VehiculoController', array('except' => array('show')));

	//Rutas Abastecimiento
	Route::post('abastecimientolugar/buscar', 'AbastecimientolugarController@buscar')->name('abastecimientolugar.buscar');
	Route::get('abastecimientolugar/eliminar/{id}/{listarluego}', 'AbastecimientolugarController@eliminar')->name('abastecimientolugar.eliminar');
	Route::resource('abastecimientolugar', 'AbastecimientolugarController', array('except' => array('show')));

	//Rutas VehiculoDocument
	Route::post('vehiculodocument/buscar', 'VehiculoDocumentController@buscar')->name('vehiculodocument.buscar');
	Route::get('vehiculodocument/eliminar/{id}/{listarluego}', 'VehiculoDocumentController@eliminar')->name('vehiculodocument.eliminar');
	Route::post('vehiculodocument/{id}', 'VehiculoDocumentController@update')->name('vehiculodocument.update');
	Route::get('vehiculodocument/notifiacion', 'VehiculoDocumentController@notifiacionCount');
	Route::get('vehiculodocument/notifiaciones', 'VehiculoDocumentController@notifiacionList');
	Route::get('vehiculodocument/exel/{id}', 'VehiculoDocumentController@notificacionExel');
	Route::get('vehiculodocument/downloadMultiple', 'VehiculoDocumentController@downloadMultiple')->name('vehiculo.downloadMultiple');
	Route::resource('vehiculodocument', 'VehiculoDocumentController', array('except' => array('show')));

	//Rutas EquipoDocument
	Route::post('equipodocument/buscar', 'EquipoDocumentController@buscar')->name('equipodocument.buscar');
	Route::get('equipodocument/eliminar/{id}/{listarluego}', 'EquipoDocumentController@eliminar')->name('equipodocument.eliminar');
	Route::post('equipodocument/{id}', 'EquipoDocumentController@update')->name('equipodocument.update');
	Route::get('equipodocument/notifiacion', 'EquipoDocumentController@notifiacionCount');
	Route::get('equipodocument/notifiaciones', 'EquipoDocumentController@notifiacionList');
	Route::get('equipodocument/exel/{id}', 'EquipoDocumentController@notificacionExel');
	Route::get('equipodocument/downloadMultiple', 'EquipoDocumentController@downloadMultiple')->name('equipo.downloadMultiple');
	Route::resource('equipodocument', 'EquipoDocumentController', array('except' => array('show')));

	//Rutas kilometraje
	Route::post('reportkilometraje/buscar', 'ReportKilometrajeController@buscar')->name('reportkilometraje.buscar');
	Route::resource('reportkilometraje', 'ReportKilometrajeController', array('except' => array('show','store','delete')));
	Route::get('reportkilometraje/exportar', 'ReportKilometrajeController@exportExcel')->name('reportkilometraje.excel.export');
	Route::get('reportkilometraje/edit2/{id}', 'ReportKilometrajeController@edit2')->name('reportkilometraje.edit2');
	Route::post('reportkilometraje/update2/{id}', 'ReportKilometrajeController@update2')->name('reportkilometraje.update2');
	Route::get('reportkilometraje/edit3/{id}', 'ReportKilometrajeController@edit3')->name('reportkilometraje.edit3');
	Route::post('reportkilometraje/update3/{id}', 'ReportKilometrajeController@update3')->name('reportkilometraje.update3');
	Route::get('reportkilometraje/edit4/{id}', 'ReportKilometrajeController@edit4')->name('reportkilometraje.edit4');
	Route::post('reportkilometraje/update4/{id}', 'ReportKilometrajeController@update4')->name('reportkilometraje.update4');

	//Rutas horometraje
	Route::post('reportehorometraje/buscar', 'ReporteHorometrajeController@buscar')->name('reportehorometraje.buscar');
	Route::resource('reportehorometraje', 'ReporteHorometrajeController', array('except' => array('show','store','delete')));
	Route::get('reportehorometraje/exportar', 'ReporteHorometrajeController@exportExcel')->name('reportehorometraje.excel.export');
	Route::get('reportehorometraje/edit2/{id}', 'ReporteHorometrajeController@edit2')->name('reportehorometraje.edit2');
	Route::post('reportehorometraje/update2/{id}', 'ReporteHorometrajeController@update2')->name('reportehorometraje.update2');
	Route::get('reportehorometraje/edit3/{id}', 'ReporteHorometrajeController@edit3')->name('reportehorometraje.edit3');
	Route::post('reportehorometraje/update3/{id}', 'ReporteHorometrajeController@update3')->name('reportehorometraje.update3');
	Route::get('reportehorometraje/edit4/{id}', 'ReporteHorometrajeController@edit4')->name('reportehorometraje.edit4');
	Route::post('reportehorometraje/update4/{id}', 'ReporteHorometrajeController@update4')->name('reportehorometraje.update4');

	//Rutas horometro
	Route::post('horometro/buscar', 'HorometroController@buscar')->name('horometro.buscar');
	Route::get('horometro/eliminar/{id}/{listarluego}', 'HorometroController@eliminar')->name('horometro.eliminar');
	Route::resource('horometro', 'HorometroController', array('except' => array('show')));

	//Rutas para las lider
	Route::post('lider/buscar', 'LiderController@buscar')->name('lider.buscar');
	Route::get('lider/eliminar/{id}/{listarluego}', 'LiderController@eliminar')->name('lider.eliminar');
	Route::resource('lider', 'LiderController', array('except' => array('show')));

	//tipo user
	Route::post('tipouser/buscar', 'TipoUserController@buscar')->name('tipouser.buscar');
	Route::get('tipouser/eliminar/{id}/{listarluego}', 'TipoUserController@eliminar')->name('tipouser.eliminar');
	Route::resource('tipouser', 'TipoUserController', array('except' => array('show')));
	
		
	//Rutas para las marcas (brands)
	Route::post('marcas/buscar', 'BrandController@buscar')->name('marcas.buscar');
	Route::get('marcas/eliminar/{id}/{listarluego}', 'BrandController@eliminar')->name('marcas.eliminar');
	Route::resource('marcas', 'BrandController', array('except' => array('show')));
	Route::get('marcas/activar/{id}/{listarluego}', 'BrandController@activar')->name('marcas.activar');
	Route::get('marcas/reactivar/{id}', 'BrandController@reactivar')->name('marcas.reactivar');

	//Periodo 
	Route::post('periodo/buscar', 'PeriodoController@buscar')->name('periodo.buscar');
	Route::get('periodo/eliminar/{id}/{listarluego}', 'PeriodoController@eliminar')->name('periodo.eliminar');
	Route::resource('periodo', 'PeriodoController', array('except' => array('show')));

	//Rutas para los repuesto
	Route::post('repuestos/buscar', 'RepuestoController@buscar')->name('repuestos.buscar');
	Route::get('repuestos/eliminar/{id}/{listarluego}', 'RepuestoController@eliminar')->name('repuestos.eliminar');
	Route::resource('repuestos', 'RepuestoController', array('except' => array('show')));
	Route::get('repuestos/activar/{id}/{listarluego}', 'RepuestoController@activar')->name('repuestos.activar');
	Route::get('repuestos/reactivar/{id}', 'RepuestoController@reactivar')->name('repuestos.reactivar');
	//Rutas para los conductores
	Route::post('conductores/buscar', 'ConductorController@buscar')->name('conductores.buscar');
	Route::get('conductores/eliminar/{id}/{listarluego}', 'ConductorController@eliminar')->name('conductores.eliminar');
	Route::resource('conductores', 'ConductorController', array('except' => array('show')));
	Route::get('existeconductor', 'ConductorController@existeConductor')->name('conductores.existeconductor');
	Route::get('conductores/activar/{id}/{listarluego}', 'ConductorController@activar')->name('conductores.activar');
	Route::get('conductores/reactivar/{id}', 'ConductorController@reactivar')->name('conductores.reactivar');

	//Rutas para Conductordocumentos
	Route::resource('conductordocument', 'ConductordocumentController', array('except' => array('show')));
	Route::post('conductordocument/buscar', 'ConductordocumentController@buscar')->name('conductordocument.buscar');
	Route::get('conductordocument/eliminar/{id}/{listarluego}', 'ConductordocumentController@eliminar')->name('conductordocument.eliminar');
	
	//Rutas para Regrepvehdocumentos
	Route::resource('regrepvehdocument', 'RegrepvehdocumentController', array('except' => array('show')));
	Route::post('regrepvehdocument/buscar', 'RegrepvehdocumentController@buscar')->name('regrepvehdocument.buscar');
	Route::get('regrepvehdocument/eliminar/{id}/{listarluego}', 'RegrepvehdocumentController@eliminar')->name('regrepvehdocument.eliminar');
	
	//Rutas para Regmanvehdocumentos
	Route::resource('regmanvehdocument', 'RegmanvehdocumentController', array('except' => array('show')));
	Route::post('regmanvehdocument/buscar', 'RegmanvehdocumentController@buscar')->name('regmanvehdocument.buscar');
	Route::get('regmanvehdocument/eliminar/{id}/{listarluego}', 'RegmanvehdocumentController@eliminar')->name('regmanvehdocument.eliminar');
	
	//Rutas para las kilometrajes (Kilometrajes)
	Route::post('kilometrajes/buscar', 'KilometrajeController@buscar')->name('kilometrajes.buscar');
	Route::get('kilometrajes/eliminar/{id}/{listarluego}', 'KilometrajeController@eliminar')->name('kilometrajes.eliminar');
	Route::resource('kilometrajes', 'KilometrajeController', array('except' => array('show')));
	Route::get('kilometrajes/activar/{id}/{listarluego}', 'KilometrajeController@activar')->name('kilometrajes.activar');
	Route::get('kilometrajes/reactivar/{id}', 'KilometrajeController@reactivar')->name('kilometrajes.reactivar');

	//Rutas para las horometrajes (Horometrajes)
	Route::post('horometrajes/buscar', 'HorometrajeController@buscar')->name('horometrajes.buscar');
	Route::get('horometrajes/eliminar/{id}/{listarluego}', 'HorometrajeController@eliminar')->name('horometrajes.eliminar');
	Route::resource('horometrajes', 'HorometrajeController', array('except' => array('show')));
	Route::get('horometrajes/activar/{id}/{listarluego}', 'HorometrajeController@activar')->name('horometrajes.activar');
	Route::get('horometrajes/reactivar/{id}', 'HorometrajeController@reactivar')->name('horometrajes.reactivar');

	//Rutas para las areas
	Route::post('areas/buscar', 'AreaController@buscar')->name('areas.buscar');
	Route::get('areas/eliminar/{id}/{listarluego}', 'AreaController@eliminar')->name('areas.eliminar');
	Route::resource('areas', 'AreaController', array('except' => array('show')));

	//Rutas para las contratistas
	Route::post('contratistas/buscar', 'ContratistaController@buscar')->name('contratistas.buscar');
	Route::get('contratistas/eliminar/{id}/{listarluego}', 'ContratistaController@eliminar')->name('contratistas.eliminar');
	Route::get('contratistas/exportar', 'ContratistaController@exportExcel')->name('contratistas.excel.export');
	Route::resource('contratistas', 'ContratistaController', array('except' => array('show')));
	
	//Rutas para las trabajos
	Route::post('trabajos/buscar', 'TrabajoController@buscar')->name('trabajos.buscar');
	Route::get('trabajos/eliminar/{id}/{listarluego}', 'TrabajoController@eliminar')->name('trabajos.eliminar');
	Route::resource('trabajos', 'TrabajoController', array('except' => array('show')));
	
	//Rutas para las trabajos
	Route::post('piepagina/buscar', 'PiepaginaController@buscar')->name('piepagina.buscar');
	Route::get('piepagina/eliminar/{id}/{listarluego}', 'PiepaginaController@eliminar')->name('piepagina.eliminar');
	Route::resource('piepagina', 'PiepaginaController', array('except' => array('show')));

	//Rutas para las tipo cambio
	Route::post('tipocambio/buscar', 'TipocambioController@buscar')->name('tipocambio.buscar');
	Route::get('tipocambio/eliminar/{id}/{listarluego}', 'TipocambioController@eliminar')->name('tipocambio.eliminar');
	Route::resource('tipocambio', 'TipocambioController', array('except' => array('show')));
	
	//Rutas para las concesionarias
	Route::post('concesionarias/buscar', 'ConcesionariaController@buscar')->name('concesionarias.buscar');
	Route::get('concesionarias/eliminar/{id}/{listarluego}', 'ConcesionariaController@eliminar')->name('concesionarias.eliminar');
	Route::get('concesionarias/exportar', 'ConcesionariaController@exportExcel')->name('concesionarias.excel.export');
	Route::resource('concesionarias', 'ConcesionariaController', array('except' => array('show')));
	
	//Rutas para checklist
	Route::resource('mantcorrprev', 'MantCorrPrev', array('except' => array('show')));
	Route::post('mantcorrprev/buscar', 'MantCorrPrev@buscar')->name('mantcorrprev.buscar');
	Route::get('mantcorrprev/eliminar/{id}/{listarluego}', 'MantCorrPrev@eliminar')->name('mantcorrprev.eliminar');
	Route::get('mantcorrprev/createrepuesto', 'MantCorrPrev@createrepuesto')->name('mantcorrprev.createrepuesto');
	Route::get('mantcorrprev/reporteexcel', 'MantCorrPrev@reporteexcel')->name('mantcorrprev.reporteexcel');
	Route::get('mantcorrprev/buscarporua', 'MantCorrPrev@buscarporua')->name('mantcorrprev.buscarporua');
	Route::get('existeunidad', 'MantCorrPrev@existeUnidad')->name('mantcorrprev.existeunidad');
	Route::get('mantcorrprev/recorridoexcel','MantCorrPrev@recorridoExcel')->name('mantcorrprev.recorridoexcel');
	Route::get('mantcorrprev/recorridoResumenexcel','MantCorrPrev@recorridoResumenexcel')->name('mantcorrprev.recorridoResumenexcel');
	Route::get('mantcorrprev/recorridoResumenDetalladoexcel','MantCorrPrev@recorridoResumenDetalladoexcel')->name('mantcorrprev.recorridoResumenDetalladoexcel');
	Route::get('mantcorrprev/recorridoResumenMesexcel','MantCorrPrev@recorridoResumenMesexcel')->name('mantcorrprev.recorridoResumenMesexcel');
	Route::get('mantcorrprev/pdf/generar','MantCorrPrev@generatePDF')->name('mantcorrprev.pdf.export');
	Route::get('mantcorrprev/searchConductor', 'MantCorrPrev@searchConductor');
	Route::get('mantcorrprev/searchUnidad', 'MantCorrPrev@searchUnidad');
	Route::get('mantcorrprev/exportar', 'MantCorrPrev@exportExcel')->name('mantcorrprev.excel.export');
	Route::get('mantcorrprev/exportarRevision', 'MantCorrPrev@exportExcelRevision')->name('mantcorrprev.excel.exportRevision');
	Route::get('mantcorrprev/exportarNoRegistrado', 'MantCorrPrev@exportExcelNoRegistrado')->name('mantcorrprev.excel.exportNoRegistrado');
	Route::get('mantcorrprev/justificar/{id}/{listarluego}', 'MantCorrPrev@justificar')->name('mantcorrprev.justificar');
	Route::post('mantcorrprev/guardarJustificar/{id}', 'MantCorrPrev@guardarJustificar')->name('mantcorrprev.guardarJustificar');

    //Rutas para Registro Repuesto Vehicular
	Route::resource('regrepveh', 'RegRepVehController', array('except' => array('show')));
	Route::post('regrepveh/buscar', 'RegRepVehController@buscar')->name('regrepveh.buscar');
	Route::get('regrepveh/eliminar/{id}/{listarluego}', 'RegRepVehController@eliminar')->name('regrepveh.eliminar');
	Route::get('regrepveh/createrepuesto', 'RegRepVehController@createrepuesto')->name('regrepveh.createrepuesto');
	Route::get('regrepveh/buscarporua', 'RegRepVehController@buscarporua')->name('regrepveh.buscarporua');
	Route::post('regrepveh/store', 'RegRepVehController@store')->name('regrepveh.createregrepveh');
	Route::get('userconcesionaria/concesionaria/{id}', 'UserConcesionariaController@concesionaria')->name('userconcesionaria.concesionaria');
	Route::get('regrepveh/search/repuesto/{query}', 'RegRepVehController@searchAutocompleteRepuesto')->name('regrepveh.search.repuesto');
	Route::get('regrepveh/search/vehiculo/{query}', 'RegRepVehController@searchAutocompleteVehiculo')->name('regrepveh.search.vehiculo');
	Route::get('regrepveh/pdf/generar','RegRepVehController@generatePDF')->name('regrepveh.pdf.export');
	Route::get('regrepveh/excel/generar', 'RegRepVehController@exportExcel')->name('regrepveh.excel.export');
	Route::get('regrepveh/excel2/generar', 'RegRepVehController@exportExcel2')->name('regrepveh.excel2.export');
	Route::get('regrepveh/excel3/generar', 'RegRepVehController@exportExcel3')->name('regrepveh.excel3.export');
	Route::get('regrepveh/excel4/generar', 'RegRepVehController@exportExcelList')->name('regrepveh.excel4.export');
	Route::get('regrepveh/excel/repuesto', 'RegRepVehController@exportExcelRepuesto')->name('regrepveh.excelRepuesto.export');
	Route::get('regrepveh/excel/excelResumenUsuarioDia', 'RegRepVehController@exportResumenUsuarioDia')->name('regrepveh.excelResumenUsuarioDia');
	Route::get('regrepveh/excel/excelResumenUsuarioMes', 'RegRepVehController@exportResumenUsuarioMes')->name('regrepveh.excelResumenUsuarioMes');
	Route::post('regrepveh/comboChecklist', 'RegRepVehController@comboChecklist')->name('regrepveh.comboChecklist');
	Route::resource('regrepveh', 'RegRepVehController');

    //Rutas para Registro Trabajo Vehicular
	Route::resource('regmanveh', 'RegManVehController', array('except' => array('show')));
	Route::post('regmanveh/buscar', 'RegManVehController@buscar')->name('regmanveh.buscar');
	Route::get('regmanveh/eliminar/{id}/{listarluego}', 'RegManVehController@eliminar')->name('regmanveh.eliminar');
	Route::get('regmanveh/createtrabajo', 'RegManVehController@createtrabajo')->name('regmanveh.createtrabajo');
	Route::get('regmanveh/buscarporua', 'RegManVehController@buscarporua')->name('regmanveh.buscarporua');
	Route::post('regmanveh/store', 'RegManVehController@store')->name('regmanveh.createregmanveh');
	Route::get('userconcesionaria/concesionaria/{id}', 'UserConcesionariaController@concesionaria')->name('userconcesionaria.concesionaria');
	Route::get('regmanveh/search/trabajo/{query}', 'RegManVehController@searchAutocompleteTrabajo')->name('regmanveh.search.trabajo');
	Route::get('regmanveh/pdf/generar','RegManVehController@generatePDF')->name('regmanveh.pdf.export');
	Route::post('regmanveh/comboChecklist', 'RegManVehController@comboChecklist')->name('regmanveh.comboChecklist');
	Route::post('regmanveh/comboRegRepVeh', 'RegManVehController@comboRegRepVeh')->name('regmanveh.comboRegRepVeh');
	Route::get('regmanveh/excel/generar', 'RegManVehController@exportExcelList')->name('regmanveh.excel.export');
	Route::get('regmanveh/excel/generarTodos', 'RegManVehController@exportExcelTodos')->name('regmanveh.excelTodos.export');
	Route::get('regmanveh/diagnostico', 'RegManVehController@diagnostico')->name('regmanveh.diagnostico');
	Route::get('regmanveh/docmat', 'RegManVehController@docmat')->name('regmanveh.docmat');
	Route::resource('regmanveh', 'RegManVehController');

	Route::post('taller/buscar', 'TallerController@buscar')->name('taller.buscar');
    Route::get('taller/eliminar/{id}/{listarluego}', 'TallerController@eliminar')->name('taller.eliminar');
	Route::resource('taller', 'TallerController', array('except' => array('show')));

	Route::resource('diagnostico', 'DiagnosticoController', array('except' => array('show')));
	Route::post('diagnostico/buscar', 'DiagnosticoController@buscar')->name('diagnostico.buscar');
	Route::get('diagnostico/eliminar/{id}/{listarluego}', 'DiagnosticoController@eliminar')->name('diagnostico.eliminar');
	Route::post('diagnostico/recorrido', 'DiagnosticoController@recorrido')->name('diagnostico.recorrido');
	Route::get('diagnostico/excel', 'DiagnosticoController@excel')->name('diagnostico.excel');


	Route::post('grupomenu/buscar', 'GrupomenuController@buscar')->name('grupomenu.buscar');
    Route::get('grupomenu/eliminar/{id}/{listarluego}', 'GrupomenuController@eliminar')->name('grupomenu.eliminar');
	Route::resource('grupomenu', 'GrupomenuController', array('except' => array('show')));

	Route::post('mensaje/buscar', 'MensajeController@buscar')->name('mensaje.buscar');
    Route::get('mensaje/eliminar/{id}/{listarluego}', 'MensajeController@eliminar')->name('mensaje.eliminar');
	Route::resource('mensaje', 'MensajeController', array('except' => array('show')));

	Route::get('controldiario/generateReport', 'ControlDiarioController@generateReport')->name('controldiario.generateReport');
	Route::get('controldiario/generateReportMedicionEquipos', 'ControlDiarioController@generateReportMedicionEquipos')->name('controldiario.generateReportMedicionEquipos');
	Route::get('controldiario/exportExcelReport', 'ControlDiarioController@exportExcelReport')->name('controldiario.exportExcelReport');
	Route::post('controldiario/exportExcelReportMedicionEquipos', 'ControlDiarioController@exportExcelReportMedicionEquipos')->name('controldiario.exportExcelReportMedicionEquipos');
	Route::post('controldiariovehiculo/exportExcelReportMedicionVehiculo', 'ControlDiarioVehiculoController@exportExcelReportMedicionVehiculo')->name('controldiariovehiculo.exportExcelReportMedicionVehiculo');
	
	Route::get('controldiariovehiculo/generateReportMedicionVehiculo', 'ControlDiarioVehiculoController@generateReportMedicionVehiculo')->name('controldiariovehiculo.generateReportMedicionVehiculo');
	
	Route::get('controldiario/getEquipos', 'ControlDiarioController@getEquipos')->name('controldiario.getEquipos');
	Route::get('controldiariovehiculo/getVehiculos', 'ControlDiarioVehiculoController@getVehiculos')->name('controldiariovehiculo.getVehiculos');

	Route::post('mantenimiento/buscar', 'MantenimientoController@buscar')->name('mantenimiento.buscar');
    Route::get('mantenimiento/eliminar/{id}/{listarluego}', 'MantenimientoController@eliminar')->name('mantenimiento.eliminar');
	Route::resource('mantenimiento', 'MantenimientoController', array('except' => array('show')));

	//Reporte KPI
	Route::post('reportekpi/buscar', 'ReporteKPIController@buscar')->name('reportekpi.buscar');
	Route::get('reportekpi/eliminar/{id}/{listarluego}', 'ReporteKPIController@eliminar')->name('reportekpi.eliminar');
	Route::resource('reportekpi', 'ReporteKPIController', array('except' => array('show')));
	Route::post('reportekpi/importar', 'ReporteKPIController@importExcel')->name('reportekpi.excel.import');
	Route::get('reportekpi/exportarMantPreventivo', 'ReporteKPIController@exportExcelMantPreventivo')->name('reportekpi.excel.exportarMantPreventivo');
	Route::get('reportekpi/exportarMantCorrectivo', 'ReporteKPIController@exportExcelMantCorrectivo')->name('reportekpi.excel.exportarMantCorrectivo');
	Route::get('reportekpi/exportarCombustibleLiviano', 'ReporteKPIController@exportExcelCombustibleLiviano')->name('reportekpi.excel.exportarCombustibleLiviano');
	Route::get('reportekpi/exportarCombustiblePesado', 'ReporteKPIController@exportExcelCombustiblePesado')->name('reportekpi.excel.exportarCombustiblePesado');
	Route::get('reportekpi/exportarOtros', 'ReporteKPIController@exportExcelOtros')->name('reportekpi.excel.exportarOtros');
	Route::get('reportekpi/exportarGastoLiviano', 'ReporteKPIController@exportExcelGastoLiviano')->name('reportekpi.excel.exportarGastoLiviano');
	Route::get('reportekpi/exportarGastoPesado', 'ReporteKPIController@exportExcelGastoPesado')->name('reportekpi.excel.exportarGastoPesado');
	Route::get('reportekpi/exportarRendimientoLiviano', 'ReporteKPIController@exportExcelRendimientoLiviano')->name('reportekpi.excel.exportarRendimientoLiviano');
	Route::get('reportekpi/exportarRendimientoPesado', 'ReporteKPIController@exportExcelRendimientoPesado')->name('reportekpi.excel.exportarRendimientoPesado');
	Route::post('reportekpi/graficoMantPreventivo', 'ReporteKPIController@graficoMantPreventivo')->name('reportekpi.excel.graficoMantPreventivo');
	Route::post('reportekpi/graficoMantCorrectivo', 'ReporteKPIController@graficoMantCorrectivo')->name('reportekpi.excel.graficoMantCorrectivo');
	Route::post('reportekpi/graficoCombustibleLiviano', 'ReporteKPIController@graficoCombustibleLiviano')->name('reportekpi.excel.graficoCombustibleLiviano');
	Route::post('reportekpi/graficoCombustiblePesado', 'ReporteKPIController@graficoCombustiblePesado')->name('reportekpi.excel.graficoCombustiblePesado');
	Route::post('reportekpi/graficoOtros', 'ReporteKPIController@graficoOtros')->name('reportekpi.excel.graficoOtros');
	Route::post('reportekpi/graficoGastoLiviano', 'ReporteKPIController@graficoGastoLiviano')->name('reportekpi.excel.graficoGastoLiviano');
	Route::post('reportekpi/graficoGastoPesado', 'ReporteKPIController@graficoGastoPesado')->name('reportekpi.excel.graficoGastoPesado');
	Route::post('reportekpi/graficoRendimientoLiviano', 'ReporteKPIController@graficoRendimientoLiviano')->name('reportekpi.excel.graficoRendimientoLiviano');
	Route::post('reportekpi/graficoRendimientoPesado', 'ReporteKPIController@graficoRendimientoPesado')->name('reportekpi.excel.graficoRendimientoPesado');

	//Rutas para las garita
	Route::post('garita/buscar', 'GaritaController@buscar')->name('garita.buscar');
	Route::get('garita/eliminar/{id}/{listarluego}', 'GaritaController@eliminar')->name('garita.eliminar');
	Route::resource('garita', 'GaritaController', array('except' => array('show')));

	//Rutas para los agentes
	Route::post('agente/buscar', 'AgenteController@buscar')->name('agente.buscar');
	Route::get('agente/eliminar/{id}/{listarluego}', 'AgenteController@eliminar')->name('agente.eliminar');
	Route::resource('agente', 'AgenteController', array('except' => array('show')));

	//Rutas para los persona
	Route::post('persona/buscar', 'PersonaController@buscar')->name('persona.buscar');
	Route::get('persona/eliminar/{id}/{listarluego}', 'PersonaController@eliminar')->name('persona.eliminar');
	Route::resource('persona', 'PersonaController', array('except' => array('show')));

	//Rutas para las empresa
	Route::post('empresa/buscar', 'EmpresaController@buscar')->name('empresa.buscar');
	Route::get('empresa/eliminar/{id}/{listarluego}', 'EmpresaController@eliminar')->name('empresa.eliminar');
	Route::resource('empresa', 'EmpresaController', array('except' => array('show')));

	//Rutas para las lugar
	Route::post('lugar/buscar', 'LugarController@buscar')->name('lugar.buscar');
	Route::get('lugar/eliminar/{id}/{listarluego}', 'LugarController@eliminar')->name('lugar.eliminar');
	Route::resource('lugar', 'LugarController', array('except' => array('show')));

	//Rutas para los controles
	Route::post('control/buscar', 'ControlController@buscar')->name('control.buscar');
	Route::get('control/eliminar/{id}/{listarluego}', 'ControlController@eliminar')->name('control.eliminar');
	Route::resource('control', 'ControlController', array('except' => array('show')));
	Route::get('control/search/contratista/{query}', 'ControlController@searchAutocompleteContratista')->name('control.search.contratista');
	Route::get('control/search/persona/{query}', 'ControlController@searchAutocompletePersona')->name('control.search.persona');
	Route::get('control/search/conductor/{query}', 'ControlController@searchAutocompleteConductor')->name('control.search.conductor');
	Route::get('control/search/lugar/{query}', 'ControlController@searchAutocompleteLugar')->name('control.search.lugar');
	Route::get('control/wp', 'ControlController@wp')->name('control.wp');
	Route::get('control/searchUnidad', 'ControlController@searchUnidad');
	Route::get('control/exportar', 'ControlController@exportar')->name('control.exportar');

	//Rutas para los docmat
	Route::post('docmat/buscar', 'DocmatController@buscar')->name('docmat.buscar');
	Route::get('docmat/eliminar/{id}/{listarluego}', 'DocmatController@eliminar')->name('docmat.eliminar');
	Route::resource('docmat', 'DocmatController', array('except' => array('show')));
	Route::get('docmat/exportar', 'DocmatController@exportar')->name('docmat.exportar');
	Route::get('docmat/pdf/generar','DocmatController@generatePDF')->name('docmat.pdf.export');

	//Rutas Velocidad Vehiculo
	Route::post('velocidadvehiculo/buscar', 'VelocidadVehiculoController@buscar')->name('velocidadvehiculo.buscar');
	Route::get('velocidadvehiculo/eliminar/{id}/{listarluego}', 'VelocidadVehiculoController@eliminar')->name('velocidadvehiculo.eliminar');
	Route::post('velocidadvehiculo/importar', 'VelocidadVehiculoController@importExcel')->name('velocidadvehiculo.excel.import');
	Route::get('velocidadvehiculo/exportar', 'VelocidadVehiculoController@exportExcel')->name('velocidadvehiculo.excel.export');
	Route::get('velocidadvehiculo/exportar2', 'VelocidadVehiculoController@exportExcel2')->name('velocidadvehiculo.excel.export2');
	Route::get('velocidadvehiculo/exportar3', 'VelocidadVehiculoController@exportExcel3')->name('velocidadvehiculo.excel.export3');
	Route::get('velocidadvehiculo/exportar4', 'VelocidadVehiculoController@exportExcelDetallado')->name('velocidadvehiculo.excel.export4');
	Route::resource('velocidadvehiculo', 'VelocidadVehiculoController');

	//Rutas Doc. Vencidos
	Route::post('documentovencido/buscar', 'DocumentoVencidoController@buscar')->name('documentovencido.buscar');
	Route::resource('documentovencido', 'DocumentoVencidoController');

	//Rutas para los pasajes
	Route::post('pasaje/buscar', 'PasajeController@buscar')->name('pasaje.buscar');
	Route::get('pasaje/eliminar/{id}/{listarluego}', 'PasajeController@eliminar')->name('pasaje.eliminar');
	Route::get('pasaje/search/contratista/{query}', 'PasajeController@searchAutocompleteContratista')->name('pasaje.search.contratista');
	Route::get('pasaje/search/persona/{query}', 'PasajeController@searchAutocompletePersona')->name('pasaje.search.persona');
	Route::get('control/search/conductor/{query}', 'PasajeController@searchAutocompleteConductor')->name('control.search.conductor');
	Route::get('pasaje/search/lugar/{query}', 'PasajeController@searchAutocompleteLugar')->name('pasaje.search.lugarpasaje');
	Route::resource('pasaje', 'PasajeController', array('except' => array('show')));
	Route::get('pasaje/exportar', 'PasajeController@exportar')->name('pasaje.exportar');

	//Rutas Rastreo Vehiculo
	Route::post('rastreovehiculo/buscar', 'RastreoVehiculoController@buscar')->name('rastreovehiculo.buscar');
	Route::resource('rastreovehiculo', 'RastreoVehiculoController', array('except' => array('show')));
	Route::get('rastreovehiculo/exportar', 'RastreoVehiculoController@exportar')->name('rastreovehiculo.exportar');
});

Route::get('/clear', function() {
    $run = Artisan::call('config:clear');
    $run = Artisan::call('cache:clear');
    $run = Artisan::call('config:cache');
    return "Cleared!";

});