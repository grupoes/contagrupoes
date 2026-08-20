<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Login::index');
$routes->post('acceder', 'Login::acceder');
$routes->get('close', 'Login::cerrar');

$routes->get('pdts', 'Pdts::index');
$routes->get('traer_detalle/(:num)', 'Pdts::traer_detalle/$1');
$routes->post('traer_data', 'Pdts::traer_data');

$routes->get('pdt-anual', 'Pdts::anual');
$routes->post('traer_data_anual', 'Pdts::traer_data_anual');

$routes->get('codigo-qr', 'Codigoqr::index');

$routes->get('perfil', 'Perfil::index');
$routes->post('cambiar_contrasena', 'Perfil::cambiar_contrasena');

$routes->get('leer_pdf', 'Maqueta_compra::leer_pdf');
$routes->get('maqueta-compras/(:num)', 'Maqueta_compra::view_maqueta/$1');
$routes->post('generar_maqueta', 'Maqueta_compra::generar_maqueta');
$routes->post('procesar_pdf', 'Maqueta_compra::procesar_archivo');
$routes->get('tipo_cambio/(:any)', 'Maqueta_compra::tipo_cambio/$1');
$routes->post('maqueta_ventas', 'Maqueta_compra::maqueta_ventas');

$routes->post('procesar_rh', 'Maqueta_compra::procesar_rh');
$routes->post('uploadExcel', 'Maqueta_compra::uploadExcel');
$routes->get('existe_ruc_dni/(:num)/(:num)', 'Maqueta_compra::existe_ruc_dni/$1/$2');

$routes->post('comprobar-duplicidad', 'Maqueta_compra::comprobar_duplicidad');

$routes->get('api/tipo-cambio/(:any)', 'Apis::tipo_cambio/$1');

$routes->post('consultar_compra', 'Maqueta_compra::consultar_compra');
$routes->get('completar_glosa', 'Maqueta_compra::completar_glosa');
$routes->get('sugerir_glosa', 'Maqueta_compra::sugerir_glosa');
$routes->get('completar_ruc', 'Maqueta_compra::completar_ruc');
$routes->get('descargar-maqueta-xlsx/(:any)', 'Maqueta_compra::descargarExcelMaqueta/$1');
$routes->get('descargar-maqueta-periodo/(:any)', 'Maqueta_compra::downloadMaquetaPeriodo/$1');
$routes->get('descargar-maqueta-registros/(:any)', 'Maqueta_compra::downloadMaquetaRegistro/$1');
$routes->get('descargar-honorarios/(:any)', 'Maqueta_compra::descargarHonorarios/$1');

$routes->post('downloadMaqueta', 'Maqueta_compra::download_maqueta');
$routes->post('downloadAbancarizados', 'Maqueta_compra::comprobantes_abancarizados');
$routes->post('getRegistros', 'Maqueta_compra::get_registros');
$routes->post('traer-maqueta-por-registros', 'Maqueta_compra::postMaquetaRegistros');

$routes->get('facturar', 'Facturar::index');
$routes->post('facturando', 'Facturar::facturando');
$routes->get('listaEmpresas', 'Facturar::lista');

$routes->post('verificar_sunat', 'Maqueta_compra::verificar_sunat');
$routes->post('loadVaucher', 'Maqueta_compra::load_vaucher');
$routes->post('consulta_bancarizados', 'Maqueta_compra::consulta_bancarizados');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
