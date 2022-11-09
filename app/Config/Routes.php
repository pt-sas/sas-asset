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

$routes->get('logout', 'Backend\Auth::logout');

$routes->get('/', 'Backend\Dashboard::index', ['filter' => 'auth']);

$routes->group('sas', ['filter' => 'auth'], function ($routes) {
    $routes->add('/', 'Backend\Dashboard::index');

    $routes->add('user', 'Backend\User::index');
    $routes->match(['get', 'post'], 'user/showAll', 'Backend\User::showAll');
    $routes->post('user/create', 'Backend\User::create');
    $routes->get('user/show/(:any)', 'Backend\User::show/$1');
    $routes->get('user/destroy/(:any)', 'Backend\User::destroy/$1');

    $routes->add('role', 'Backend\Role::index');
    $routes->match(['get', 'post'], 'role/showAll', 'Backend\Role::showAll');
    $routes->post('role/create', 'Backend\Role::create');
    $routes->get('role/show/(:any)', 'Backend\Role::show/$1');
    $routes->get('role/destroy/(:any)', 'Backend\Role::destroy/$1');

    $routes->add('menu', 'Backend\Menu::index');
    $routes->match(['get', 'post'], 'menu/showAll', 'Backend\Menu::showAll');
    $routes->post('menu/create', 'Backend\Menu::create');
    $routes->get('menu/show/(:any)', 'Backend\Menu::show/$1');
    $routes->get('menu/destroy/(:any)', 'Backend\Menu::destroy/$1');

    $routes->add('submenu', 'Backend\Submenu::index');
    $routes->match(['get', 'post'], 'submenu/showAll', 'Backend\Submenu::showAll');
    $routes->post('submenu/create', 'Backend\Submenu::create');
    $routes->get('submenu/show/(:any)', 'Backend\Submenu::show/$1');
    $routes->get('submenu/destroy/(:any)', 'Backend\Submenu::destroy/$1');

    $routes->add('brand', 'Backend\Brand::index');
    $routes->match(['get', 'post'], 'brand/showAll', 'Backend\Brand::showAll');
    $routes->post('brand/create', 'Backend\Brand::create');
    $routes->get('brand/show/(:any)', 'Backend\Brand::show/$1');
    $routes->get('brand/destroy/(:any)', 'Backend\Brand::destroy/$1');
    $routes->get('brand/getSeqCode', 'Backend\Brand::getSeqCode');

    $routes->add('category', 'Backend\Category::index');
    $routes->match(['get', 'post'], 'category/showAll', 'Backend\Category::showAll');
    $routes->post('category/create', 'Backend\Category::create');
    $routes->get('category/show/(:any)', 'Backend\Category::show/$1');
    $routes->get('category/destroy/(:any)', 'Backend\Category::destroy/$1');
    $routes->get('category/getSeqCode', 'Backend\Category::getSeqCode');

    $routes->add('subcategory', 'Backend\Subcategory::index');
    $routes->match(['get', 'post'], 'subcategory/showAll', 'Backend\Subcategory::showAll');
    $routes->post('subcategory/create', 'Backend\Subcategory::create');
    $routes->get('subcategory/show/(:any)', 'Backend\Subcategory::show/$1');
    $routes->get('subcategory/destroy/(:any)', 'Backend\Subcategory::destroy/$1');
    $routes->get('subcategory/getSeqCode', 'Backend\Subcategory::getSeqCode');

    $routes->add('type', 'Backend\Type::index');
    $routes->match(['get', 'post'], 'type/showAll', 'Backend\Type::showAll');
    $routes->post('type/create', 'Backend\Type::create');
    $routes->get('type/show/(:any)', 'Backend\Type::show/$1');
    $routes->get('type/destroy/(:any)', 'Backend\Type::destroy/$1');
    $routes->get('type/getSeqCode', 'Backend\Type::getSeqCode');

    $routes->add('product', 'Backend\Product::index');
    $routes->match(['get', 'post'], 'product/showAll', 'Backend\Product::showAll');
    $routes->post('product/create', 'Backend\Product::create');
    $routes->get('product/show/(:any)', 'Backend\Product::show/$1');
    $routes->get('product/destroy/(:any)', 'Backend\Product::destroy/$1');
    $routes->get('product/getSeqCode', 'Backend\Product::getSeqCode');

    $routes->add('branch', 'Backend\Branch::index');
    $routes->match(['get', 'post'], 'branch/showAll', 'Backend\Branch::showAll');
    $routes->post('branch/create', 'Backend\Branch::create');
    $routes->get('branch/show/(:any)', 'Backend\Branch::show/$1');
    $routes->get('branch/destroy/(:any)', 'Backend\Branch::destroy/$1');
    $routes->get('branch/getSeqCode', 'Backend\Branch::getSeqCode');

    $routes->add('room', 'Backend\Room::index');
    $routes->match(['get', 'post'], 'room/showAll', 'Backend\Room::showAll');
    $routes->post('room/create', 'Backend\Room::create');
    $routes->get('room/show/(:any)', 'Backend\Room::show/$1');
    $routes->get('room/destroy/(:any)', 'Backend\Room::destroy/$1');
    $routes->get('room/getSeqCode', 'Backend\Room::getSeqCode');

    $routes->add('division', 'Backend\Division::index');
    $routes->match(['get', 'post'], 'division/showAll', 'Backend\Division::showAll');
    $routes->post('division/create', 'Backend\Division::create');
    $routes->get('division/show/(:any)', 'Backend\Division::show/$1');
    $routes->get('division/destroy/(:any)', 'Backend\Division::destroy/$1');
    $routes->get('division/getSeqCode', 'Backend\Division::getSeqCode');

    $routes->add('employee', 'Backend\Employee::index');
    $routes->match(['get', 'post'], 'employee/showAll', 'Backend\Employee::showAll');
    $routes->post('employee/create', 'Backend\Employee::create');
    $routes->get('employee/show/(:any)', 'Backend\Employee::show/$1');
    $routes->get('employee/destroy/(:any)', 'Backend\Employee::destroy/$1');
    $routes->get('employee/getSeqCode', 'Backend\Employee::getSeqCode');

    $routes->add('supplier', 'Backend\Supplier::index');
    $routes->match(['get', 'post'], 'supplier/showAll', 'Backend\Supplier::showAll');
    $routes->post('supplier/create', 'Backend\Supplier::create');
    $routes->get('supplier/show/(:any)', 'Backend\Supplier::show/$1');
    $routes->get('supplier/destroy/(:any)', 'Backend\Supplier::destroy/$1');
    $routes->get('supplier/getSeqCode', 'Backend\Supplier::getSeqCode');

    $routes->add('quotation', 'Backend\Quotation::index');
    $routes->match(['get', 'post'], 'quotation/showAll', 'Backend\Quotation::showAll');
    $routes->post('quotation/create', 'Backend\Quotation::create');
    $routes->get('quotation/show/(:any)', 'Backend\Quotation::show/$1');
    $routes->get('quotation/destroy/(:any)', 'Backend\Quotation::destroy/$1');
    $routes->post('quotation/tableLine/(:any)', 'Backend\Quotation::tableLine/$1');
    $routes->get('quotation/destroyLine/(:any)', 'Backend\Quotation::destroyLine/$1');
    $routes->get('quotation/getSeqCode', 'Backend\Quotation::getSeqCode');

    $routes->add('receipt', 'Backend\Receipt::index');
    $routes->match(['get', 'post'], 'receipt/showAll', 'Backend\Receipt::showAll');
    $routes->post('receipt/create', 'Backend\Receipt::create');
    $routes->get('receipt/show/(:any)', 'Backend\Receipt::show/$1');
    $routes->get('receipt/destroy/(:any)', 'Backend\Receipt::destroy/$1');
    $routes->get('receipt/destroyLine/(:any)', 'Backend\Receipt::destroyLine/$1');
    $routes->get('receipt/getSeqCode', 'Backend\Receipt::getSeqCode');

    $routes->add('status', 'Backend\Status::index');
    $routes->match(['get', 'post'], 'status/showAll', 'Backend\Status::showAll');
    $routes->post('status/create', 'Backend\Status::create');
    $routes->get('status/show/(:any)', 'Backend\Status::show/$1');
    $routes->get('status/destroy/(:any)', 'Backend\Status::destroy/$1');
    $routes->get('status/getSeqCode', 'Backend\Status::getSeqCode');

    $routes->add('service', 'Backend\Service::index');
    $routes->match(['get', 'post'], 'service/showAll', 'Backend\Service::showAll');
    $routes->post('service/create', 'Backend\Service::create');
    $routes->get('service/show/(:any)', 'Backend\Service::show/$1');
    $routes->get('service/destroy/(:any)', 'Backend\Service::destroy/$1');
    $routes->post('service/tableLine', 'Backend\Service::tableLine');
    $routes->get('service/destroyLine/(:any)', 'Backend\Service::destroyLine/$1');
    $routes->get('service/getSeqCode', 'Backend\Service::getSeqCode');

    $routes->add('movement', 'Backend\Movement::index');
    $routes->match(['get', 'post'], 'movement/showAll', 'Backend\Movement::showAll');
    $routes->post('movement/create', 'Backend\Movement::create');
    $routes->get('movement/show/(:any)', 'Backend\Movement::show/$1');
    $routes->get('movement/destroy/(:any)', 'Backend\Movement::destroy/$1');
    $routes->post('movement/tableLine', 'Backend\Movement::tableLine');
    $routes->get('movement/destroyLine/(:any)', 'Backend\Movement::destroyLine/$1');
    $routes->get('movement/getSeqCode', 'Backend\Movement::getSeqCode');

    $routes->add('inventory', 'Backend\Inventory::index');
    $routes->match(['get', 'post'], 'inventory/showAll', 'Backend\Inventory::showAll');
    $routes->post('inventory/create', 'Backend\Inventory::create');
    $routes->get('inventory/show/(:any)', 'Backend\Inventory::show/$1');
    $routes->get('inventory/destroy/(:any)', 'Backend\Inventory::destroy/$1');
    $routes->get('inventory/getSeqCode', 'Backend\Inventory::getSeqCode');

    $routes->add('groupasset', 'Backend\GroupAsset::index');
    $routes->match(['get', 'post'], 'groupasset/showAll', 'Backend\GroupAsset::showAll');
    $routes->post('groupasset/create', 'Backend\GroupAsset::create');
    $routes->get('groupasset/show/(:any)', 'Backend\GroupAsset::show/$1');
    $routes->get('groupasset/destroy/(:any)', 'Backend\GroupAsset::destroy/$1');
    $routes->get('groupasset/getSeqCode', 'Backend\GroupAsset::getSeqCode');

    $routes->add('sequence', 'Backend\Sequence::index');
    $routes->match(['get', 'post'], 'sequence/showAll', 'Backend\Sequence::showAll');
    $routes->post('sequence/create', 'Backend\Sequence::create');
    $routes->get('sequence/show/(:any)', 'Backend\Sequence::show/$1');
    $routes->get('sequence/destroy/(:any)', 'Backend\Sequence::destroy/$1');

    $routes->add('opname', 'Backend\Opname::index');

    $routes->add('internal', 'Backend\Internal::index');
    $routes->match(['get', 'post'], 'internal/showAll', 'Backend\Internal::showAll');
    $routes->post('internal/create', 'Backend\Internal::create');
    $routes->get('internal/show/(:any)', 'Backend\Internal::show/$1');
    $routes->get('internal/destroy/(:any)', 'Backend\Internal::destroy/$1');
    $routes->post('internal/tableLine/(:any)', 'Backend\Internal::tableLine/$1');
    $routes->get('internal/destroyLine/(:any)', 'Backend\Internal::destroyLine/$1');
    $routes->get('internal/getSeqCode', 'Backend\Internal::getSeqCode');

    $routes->add('reference', 'Backend\Reference::index');
    $routes->match(['get', 'post'], 'reference/showAll', 'Backend\Reference::showAll');
    $routes->post('reference/create', 'Backend\Reference::create');
    $routes->get('reference/show/(:any)', 'Backend\Reference::show/$1');
    $routes->get('reference/destroy/(:any)', 'Backend\Reference::destroy/$1');
    $routes->post('reference/tableLine', 'Backend\Reference::tableLine');
    $routes->get('reference/destroyLine/(:any)', 'Backend\Reference::destroyLine/$1');

    $routes->add('rpt_assetdetail', 'Backend\Rpt_AssetDetail::index');
    $routes->match(['get', 'post'], 'rpt_assetdetail/showAll', 'Backend\Rpt_AssetDetail::showAll');

    $routes->add('notificationtext', 'Backend\NotificationText::index');
    $routes->match(['get', 'post'], 'notificationtext/showAll', 'Backend\NotificationText::showAll');
    $routes->post('notificationtext/create', 'Backend\NotificationText::create');
    $routes->get('notificationtext/show/(:any)', 'Backend\NotificationText::show/$1');
    $routes->get('notificationtext/destroy/(:any)', 'Backend\NotificationText::destroy/$1');

    $routes->add('mail', 'Backend\Mail::index');
    $routes->match(['get', 'post'], 'mail/showAll', 'Backend\Mail::showAll');
    $routes->post('mail/create', 'Backend\Mail::create');
    $routes->post('mail/createTestEmail', 'Backend\Mail::createTestEmail');

    $routes->add('wscenario', 'Backend\WScenario::index');
    $routes->match(['get', 'post'], 'wscenario/showAll', 'Backend\WScenario::showAll');
    $routes->post('wscenario/create', 'Backend\WScenario::create');
    $routes->get('wscenario/show/(:any)', 'Backend\WScenario::show/$1');
    $routes->get('wscenario/destroy/(:any)', 'Backend\WScenario::destroy/$1');
    $routes->post('wscenario/tableLine', 'Backend\WScenario::tableLine');
    $routes->get('wscenario/destroyLine/(:any)', 'Backend\WScenario::destroyLine/$1');

    $routes->add('responsible', 'Backend\Responsible::index');
    $routes->match(['get', 'post'], 'responsible/showAll', 'Backend\Responsible::showAll');
    $routes->post('responsible/create', 'Backend\Responsible::create');
    $routes->get('responsible/show/(:any)', 'Backend\Responsible::show/$1');
    $routes->get('responsible/destroy/(:any)', 'Backend\Responsible::destroy/$1');

    $routes->add('barcode', 'Backend\Barcode::index');
    $routes->match(['get', 'post'], 'barcode/showAll', 'Backend\Barcode::showAll');
    $routes->post('barcode/create', 'Backend\Barcode::create');
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
