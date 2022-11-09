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
    $routes->match(['get', 'post'], 'user/showAll', 'Backend\User::showAll');
    $routes->add('role', 'Backend\Role::index');
    $routes->match(['get', 'post'], 'role/showAll', 'Backend\Role::showAll');
    $routes->add('menu', 'Backend\Menu::index');
    $routes->match(['get', 'post'], 'menu/showAll', 'Backend\Menu::showAll');
    $routes->add('submenu', 'Backend\Submenu::index');
    $routes->match(['get', 'post'], 'submenu/showAll', 'Backend\Submenu::showAll');
    $routes->add('brand', 'Backend\Brand::index');
    $routes->match(['get', 'post'], 'brand/showAll', 'Backend\Brand::showAll');
    $routes->add('category', 'Backend\Category::index');
    $routes->match(['get', 'post'], 'category/showAll', 'Backend\Category::showAll');
    $routes->add('subcategory', 'Backend\Subcategory::index');
    $routes->match(['get', 'post'], 'subcategory/showAll', 'Backend\Subcategory::showAll');
    $routes->add('type', 'Backend\Type::index');
    $routes->match(['get', 'post'], 'type/showAll', 'Backend\Type::showAll');
    $routes->add('product', 'Backend\Product::index');
    $routes->match(['get', 'post'], 'product/showAll', 'Backend\Product::showAll');
    $routes->add('branch', 'Backend\Branch::index');
    $routes->match(['get', 'post'], 'branch/showAll', 'Backend\Branch::showAll');
    $routes->add('room', 'Backend\Room::index');
    $routes->match(['get', 'post'], 'room/showAll', 'Backend\Room::showAll');
    $routes->add('division', 'Backend\Division::index');
    $routes->match(['get', 'post'], 'division/showAll', 'Backend\Division::showAll');
    $routes->add('employee', 'Backend\Employee::index');
    $routes->match(['get', 'post'], 'employee/showAll', 'Backend\Employee::showAll');
    $routes->add('supplier', 'Backend\Supplier::index');
    $routes->match(['get', 'post'], 'supplier/showAll', 'Backend\Supplier::showAll');
    $routes->add('quotation', 'Backend\Quotation::index');
    $routes->match(['get', 'post'], 'quotation/showAll', 'Backend\Quotation::showAll');
    $routes->add('receipt', 'Backend\Receipt::index');
    $routes->match(['get', 'post'], 'receipt/showAll', 'Backend\Receipt::showAll');
    $routes->add('status', 'Backend\Status::index');
    $routes->match(['get', 'post'], 'status/showAll', 'Backend\Status::showAll');
    $routes->add('service', 'Backend\Service::index');
    $routes->match(['get', 'post'], 'service/showAll', 'Backend\Service::showAll');
    $routes->add('movement', 'Backend\Movement::index');
    $routes->match(['get', 'post'], 'movement/showAll', 'Backend\Movement::showAll');
    $routes->add('inventory', 'Backend\Inventory::index');
    $routes->match(['get', 'post'], 'inventory/showAll', 'Backend\Inventory::showAll');
    $routes->add('groupasset', 'Backend\GroupAsset::index');
    $routes->match(['get', 'post'], 'groupasset/showAll', 'Backend\GroupAsset::showAll');
    $routes->add('sequence', 'Backend\Sequence::index');
    $routes->match(['get', 'post'], 'sequence/showAll', 'Backend\Sequence::showAll');
    $routes->add('opname', 'Backend\Opname::index');
    $routes->add('internal', 'Backend\Internal::index');
    $routes->match(['get', 'post'], 'internal/showAll', 'Backend\Internal::showAll');
    $routes->add('reference', 'Backend\Reference::index');
    $routes->match(['get', 'post'], 'reference/showAll', 'Backend\Reference::showAll');
    $routes->add('rpt_assetdetail', 'Backend\Rpt_AssetDetail::index');
    $routes->match(['get', 'post'], 'rpt_assetdetail/showAll', 'Backend\Rpt_AssetDetail::showAll');
    $routes->add('notificationtext', 'Backend\NotificationText::index');
    $routes->match(['get', 'post'], 'notificationtext/showAll', 'Backend\NotificationText::showAll');
    $routes->add('mail', 'Backend\Mail::index');
    $routes->match(['get', 'post'], 'mail/showAll', 'Backend\Mail::showAll');
    $routes->add('wscenario', 'Backend\WScenario::index');
    $routes->match(['get', 'post'], 'wscenario/showAll', 'Backend\WScenario::showAll');
    $routes->add('responsible', 'Backend\Responsible::index');
    $routes->match(['get', 'post'], 'responsible/showAll', 'Backend\Responsible::showAll');
    $routes->add('barcode', 'Backend\Barcode::index');
    $routes->match(['get', 'post'], 'barcode/showAll', 'Backend\Barcode::showAll');
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
