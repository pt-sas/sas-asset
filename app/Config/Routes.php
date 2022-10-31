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
$routes->get('auth', 'Backend\Auth::index', ['filter' => 'auth']);
// $routes->addPlaceholder('auth', 'permission');

$routes->get('logout', 'Backend\Auth::logout');

$routes->get('/', 'Backend\Dashboard::index', ['filter' => 'auth']);

$routes->group('sas', ['filter' => 'auth'], function ($routes) {
    $routes->add('/', 'Backend\Dashboard::index');
    $routes->add('user', 'Backend\User::index');
    $routes->add('role', 'Backend\Role::index');
    $routes->add('menu', 'Backend\Menu::index');
    $routes->add('submenu', 'Backend\Submenu::index');
    $routes->add('brand', 'Backend\Brand::index');
    $routes->add('category', 'Backend\Category::index');
    $routes->add('subcategory', 'Backend\Subcategory::index');
    $routes->add('type', 'Backend\Type::index');
    $routes->add('product', 'Backend\Product::index');
    $routes->add('branch', 'Backend\Branch::index');
    $routes->add('room', 'Backend\Room::index');
    $routes->add('division', 'Backend\Division::index');
    $routes->add('employee', 'Backend\Employee::index');
    $routes->add('supplier', 'Backend\Supplier::index');
    $routes->add('quotation', 'Backend\Quotation::index');
    $routes->add('receipt', 'Backend\Receipt::index');
    $routes->add('status', 'Backend\Status::index');
    $routes->add('service', 'Backend\Service::index');
    $routes->add('movement', 'Backend\Movement::index');
    $routes->add('inventory', 'Backend\Inventory::index');
    $routes->add('groupasset', 'Backend\GroupAsset::index');
    $routes->add('sequence', 'Backend\Sequence::index');
    $routes->add('opname', 'Backend\Opname::index');
    $routes->add('internal', 'Backend\Internal::index');
    $routes->add('reference', 'Backend\Reference::index');
    $routes->add('rpt_assetdetail', 'Backend\Rpt_AssetDetail::index');
    $routes->add('notificationtext', 'Backend\NotificationText::index');
});

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
