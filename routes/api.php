<?php

use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Item\ItemController;
use App\Http\Controllers\PagController;
use App\Http\Controllers\PbgController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Quotation\QuotationController;
use App\Http\Controllers\Quotation\QuotationItemController;
use App\Http\Controllers\RunController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\User\UserAccount;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserPermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
  return response()->json([
    "message" => "test"
  ], 500);
})->name('login');

Route::get('/run', [RunController::class, 'index']);
// Route::post('/login', [UserAccount::class, 'login']);

Route::prefix('internal')->group(function () {
  Route::post('/login', [\App\Http\Controllers\Internal\User\UserAccount::class, 'login']);
  Route::post('/logout', [\App\Http\Controllers\Internal\User\UserAccount::class, 'logout']);
  Route::put('/change_password', [\App\Http\Controllers\Internal\User\UserAccount::class, 'change_password']);

  Route::get('/check_user', [\App\Http\Controllers\Internal\User\UserAccount::class, 'checkUser']);

  Route::get('/users', [\App\Http\Controllers\Internal\User\UserController::class, 'index']);
  Route::get('/user', [\App\Http\Controllers\Internal\User\UserController::class, 'show']);
  Route::post('/user', [\App\Http\Controllers\Internal\User\UserController::class, 'store']);
  Route::put('/user', [\App\Http\Controllers\Internal\User\UserController::class, 'update']);
  Route::delete('/user', [\App\Http\Controllers\Internal\User\UserController::class, 'delete']);

  Route::get('/action_permissions', [\App\Http\Controllers\Internal\User\UserPermissionController::class, 'getActionPermissions']);
  Route::get('/data_permissions', [\App\Http\Controllers\Internal\User\UserPermissionController::class, 'getDataPermissions']);
  Route::get('/user/permissions', [\App\Http\Controllers\Internal\User\UserPermissionController::class, 'show']);
  Route::put('/user/permissions', [\App\Http\Controllers\Internal\User\UserPermissionController::class, 'update']);

  Route::get('/institutes', [\App\Http\Controllers\Internal\InstituteController::class, 'index']);
  Route::get('/institute', [\App\Http\Controllers\Internal\InstituteController::class, 'show']);
  Route::post('/institute', [\App\Http\Controllers\Internal\InstituteController::class, 'store']);
  Route::put('/institute', [\App\Http\Controllers\Internal\InstituteController::class, 'update']);
  Route::delete('/institute', [\App\Http\Controllers\Internal\InstituteController::class, 'delete']);

  Route::get('/members', [\App\Http\Controllers\Internal\MemberController::class, 'index']);
  Route::get('/member', [\App\Http\Controllers\Internal\MemberController::class, 'show']);
  Route::post('/member', [\App\Http\Controllers\Internal\MemberController::class, 'store']);
  Route::put('/member', [\App\Http\Controllers\Internal\MemberController::class, 'update']);
  Route::delete('/member', [\App\Http\Controllers\Internal\MemberController::class, 'delete']);
});

Route::prefix('main')->group(function () {
  Route::post('/login', [\App\Http\Controllers\Main\Member\MemberAccount::class, 'login']);
  Route::post('/logout', [\App\Http\Controllers\Internal\User\MemberAccount::class, 'logout']);
  Route::get('/check_user', [\App\Http\Controllers\Main\Member\MemberAccount::class, 'checkUser']);
  Route::put('/change_password', [\App\Http\Controllers\Main\Member\MemberAccount::class, 'change_password']);


  Route::get('/operator/member_institutes', [\App\Http\Controllers\Main\Operator\MemberInstituteController::class, 'index']);
  Route::post('/operator/member_institutes', [\App\Http\Controllers\Main\Operator\MemberInstituteController::class, 'store']);
  Route::delete('/operator/member_institutes', [\App\Http\Controllers\Main\Operator\MemberInstituteController::class, 'delete']);

  Route::get('/operator/members', [\App\Http\Controllers\Main\Operator\MemberController::class, 'index']);

  Route::get('/operator/class_rooms', [\App\Http\Controllers\Main\Operator\ClassRoomController::class, 'index']);
  Route::get('/operator/class_room', [\App\Http\Controllers\Main\Operator\ClassRoomController::class, 'show']);
  Route::post('/operator/class_room', [\App\Http\Controllers\Main\Operator\ClassRoomController::class, 'store']);
  Route::put('/operator/class_room', [\App\Http\Controllers\Main\Operator\ClassRoomController::class, 'update']);
  Route::delete('/operator/class_room', [\App\Http\Controllers\Main\Operator\ClassRoomController::class, 'delete']);

  // Route::get('/members', [\App\Http\Controllers\Main\Member\MemberController::class, 'index']);
  // Route::get('/member', [\App\Http\Controllers\Main\Member\MemberController::class, 'show']);
  // Route::post('/member', [\App\Http\Controllers\Main\Member\MemberController::class, 'store']);
  // Route::put('/member', [\App\Http\Controllers\Main\Member\MemberController::class, 'update']);
  // Route::delete('/member', [\App\Http\Controllers\Main\Member\MemberController::class, 'delete']);

  // Route::get('/action_permissions', [\App\Http\Controllers\Main\Member\MemberPermissionController::class, 'getActionPermissions']);
  // Route::get('/data_permissions', [\App\Http\Controllers\Main\Member\MemberPermissionController::class, 'getDataPermissions']);
  // Route::get('/member/permissions', [\App\Http\Controllers\Main\Member\MemberPermissionController::class, 'show']);
  // Route::put('/member/permissions', [\App\Http\Controllers\Main\Member\MemberPermissionController::class, 'update']);

  Route::get('/dashboard', [\App\Http\Controllers\Main\DashboardController::class, 'index']);
  // Route::get('/institute', [\App\Http\Controllers\Main\InstituteController::class, 'show']);
  // Route::post('/institute', [\App\Http\Controllers\Main\InstituteController::class, 'store']);
  // Route::put('/institute', [\App\Http\Controllers\Main\InstituteController::class, 'update']);
  // Route::delete('/institute', [\App\Http\Controllers\Main\InstituteController::class, 'delete']);

});

Route::get('files/{args?}', function ($args) {
  // $path = __DIR__.'/../../public/files/' . $args;
  $path = __DIR__ . '/../public/files/' . $args;
  if (file_exists($path)) {
    $p = explode("/", $path);
    $filename = array_pop($p);
    // return Response::file($path);
    return Response::make(file_get_contents($path), 200, [
      'Content-Type' => mct($filename),
      'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ]);
  }
})->where('args', '(.*)');

// Route::middleware('auth:api')->group(function () {

//   Route::put('/change_password', [UserAccount::class, 'change_password']);
//   Route::put('/change_name', [UserAccount::class, 'change_name']);



//   Route::get('/employees', [EmployeeController::class, 'index']);
//   Route::get('/employee', [EmployeeController::class, 'show']);
//   Route::post('/employee', [EmployeeController::class, 'store']);
//   Route::put('/employee', [EmployeeController::class, 'update']);
//   Route::delete('/employee', [EmployeeController::class, 'delete']);

//   Route::get('/units', [UnitController::class, 'index']);
//   Route::get('/unit', [UnitController::class, 'show']);
//   Route::post('/unit', [UnitController::class, 'store']);
//   Route::put('/unit', [UnitController::class, 'update']);
//   Route::delete('/unit', [UnitController::class, 'delete']);

//   Route::get('/quotations', [QuotationController::class, 'index']);
//   Route::get('/quotation', [QuotationController::class, 'show']);
//   Route::post('/quotation', [QuotationController::class, 'store']);
//   Route::put('/quotation', [QuotationController::class, 'update']);
//   Route::delete('/quotation', [QuotationController::class, 'delete']);

//   Route::get('/quotation_items', [QuotationItemController::class, 'index']);
//   Route::get('/quotation_item', [QuotationItemController::class, 'show']);
//   Route::post('/quotation_item', [QuotationItemController::class, 'store']);
//   Route::put('/quotation_item', [QuotationItemController::class, 'update']);
//   Route::delete('/quotation_item', [QuotationItemController::class, 'delete']);

//   Route::get('/customers', [CustomerController::class, 'index']);
//   Route::get('/customer', [CustomerController::class, 'show']);
//   Route::post('/customer', [CustomerController::class, 'store']);
//   Route::put('/customer', [CustomerController::class, 'update']);
//   Route::delete('/customer', [CustomerController::class, 'delete']);

//   Route::get('/items', [ItemController::class, 'index']);
//   Route::get('/item', [ItemController::class, 'show']);
//   Route::post('/item', [ItemController::class, 'store']);
//   Route::put('/item', [ItemController::class, 'update']);
//   Route::delete('/item', [ItemController::class, 'delete']);

//   Route::get('/projects', [ProjectController::class, 'index']);
//   Route::get('/project', [ProjectController::class, 'show']);
//   Route::post('/project', [ProjectController::class, 'store']);
//   Route::put('/project', [ProjectController::class, 'update']);
//   Route::delete('/project', [ProjectController::class, 'delete']);

//   Route::put('/project_material', [ProjectController::class, 'material']);
//   Route::put('/project_working_tool', [ProjectController::class, 'working_tool']);
//   Route::put('/project_worker', [ProjectController::class, 'workers']);
//   Route::put('/project_additional', [ProjectController::class, 'addition']);
//   Route::get('/material/download', [ProjectController::class, 'material_download']);


//   Route::get('/pags', [PagController::class, 'index']);
//   Route::get('/pag', [PagController::class, 'show']);
//   Route::post('/pag', [PagController::class, 'store']);
//   Route::put('/pag', [PagController::class, 'update']);
//   Route::delete('/pag', [PagController::class, 'delete']);
//   Route::get('/pag/cetak', [PagController::class, 'download']);

//   Route::get('/pbgs', [PbgController::class, 'index']);
//   Route::get('pbg', [PbgController::class, 'show']);
//   Route::post('pbg', [PbgController::class, 'store']);
//   Route::put('pbg', [PbgController::class, 'update']);
//   Route::delete('pbg', [PbgController::class, 'delete']);
//   Route::get('/pbg/cetak', [PbgController::class, 'download']);


//   Route::get('/roles', 'App\Http\Controllers\RoleController@index');
//   Route::get('/role', 'App\Http\Controllers\RoleController@show');
//   Route::post('/role', 'App\Http\Controllers\RoleController@store');
//   Route::put('/role', 'App\Http\Controllers\RoleController@update');
//   Route::get('/role/permission_list', 'App\Http\Controllers\RoleController@permission_list');

//   Route::get('/permissions', 'App\Http\Controllers\PermissionController@index');
//   Route::get('/permission', 'App\Http\Controllers\PermissionController@show');
//   Route::post('/permission', 'App\Http\Controllers\PermissionController@store');
//   Route::put('/permission', 'App\Http\Controllers\PermissionController@update');

//   Route::get('/permintaan_pembelians', 'App\Http\Controllers\PermintaanPembelianController@index');
//   Route::get('/permintaan_pembelian', 'App\Http\Controllers\PermintaanPembelianController@show');
//   Route::post('/permintaan_pembelian', 'App\Http\Controllers\PermintaanPembelianController@store');
//   Route::put('/permintaan_pembelian', 'App\Http\Controllers\PermintaanPembelianController@update');
//   Route::put('/permintaan_pembelian/reject', 'App\Http\Controllers\PermintaanPembelianController@reject');
//   Route::put('/permintaan_pembelian/process_acc', 'App\Http\Controllers\PermintaanPembelianController@process_acc');
//   Route::get('/permintaan_pembelian/sub_download', 'App\Http\Controllers\PermintaanPembelianController@sub_download');

//   // Route::get('/product_categories','App\Http\Controllers\ProductCategoryController@index');
//   // Route::get('/product_category','App\Http\Controllers\ProductCategoryController@show');
//   // Route::post('/product_category','App\Http\Controllers\ProductCategoryController@store');
//   // Route::put('/product_category','App\Http\Controllers\ProductCategoryController@update');

//   // Route::get('/products','App\Http\Controllers\ProductController@index');
//   // Route::get('/product','App\Http\Controllers\ProductController@show');
//   // Route::post('/product','App\Http\Controllers\ProductController@store');
//   // Route::put('/product','App\Http\Controllers\ProductController@update');

// });
