<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

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
Route::group([], function () use ($router) {
    $router->get('liap-ios', [TestingController::class, 'testing']);
    $router->get('notification-testing', [TestingController::class, 'notificationTesting']);

    $router->get('verify-user/{token}', [DashboardController::class, "verifyUser"])->name('verify-customer');
    $router->view('verify-message', 'verify')->name('verify-message');
    $router->redirect('/login', '/');

    $router->match(['get', 'post'], "", [DashboardController::class, "login"])->name('login');
    $router->match(['get', 'post'], 'reset-password', [DashboardController::class, "resetPassword"])->name('reset-password');
    $router->get('reset-password/check/token/{token}', [DashboardController::class, "verifyResetPasswordToken"])->name('token-check');
    $router->match(['get', 'post'], 'set-new-password', [DashboardController::class, "setNewPassword"])->name('set-newpassword');
    $router->get('table-data/{name?}', [DashboardController::class, "getData"]);
    $router->get('table-data/{name}/delete/{id}', [DashboardController::class, "deleteData"]);

    $router->middleware(['auth'])->group(function () use ($router) {
        $router->post('details/update', [DashboardController::class, "updateDetails"])->name('details.update');

        $router->post('password/update', [DashboardController::class, "updatePassword"])->name('password.update');
        $router->get('logout-ad', [DashboardController::class, "logout"])->name('logout-ad');
        /** Manage Companies Routes **/
        $router->prefix('roles')->name('roles.')->group(function () use ($router) {
            $router->get('list/{deleted?}', [RoleController::class, "getList"])->name('list')->middleware('permission:user-list');
            $router->match(['get', 'post'], 'edit/{id?}', [RoleController::class, "edit_form"])->name('edit')->middleware('permission:user-edit');
            $router->match(['get', 'post'], 'add', [RoleController::class, "create_record"])->name('add')->middleware('permission:user-add');
            $router->get('assign-permission', [RoleController::class, "assignPermission"])->middleware('permission:user-status');
        });
    });

    $router->middleware(['auth'])->group(function () use ($router) {
        /** Manage Users Routes **/
        $router->prefix('user')->name('user.')->group(function () use ($router) {
            $router->get('list/{deleted?}', [UserController::class, "getList"])->name('list')->middleware('permission:user-list');
            $router->get('devices/{user_id?}', [UserController::class, "getDevices"])->name('devices')->middleware('permission:device-list');
            $router->match(['get', 'post'], 'add/device', [UserController::class, "addDevice"])->name('adddevice')->middleware('permission:device-add');
            $router->match(['get', 'post'], 'add', [UserController::class, "add"])->name('add')->middleware('permission:user-add');
            $router->get('changeStatus', [UserController::class, "changeStatus"])->middleware('permission:user-status');
            $router->get('/changeDeviceStatus', [UserController::class, "changeDeviceStatus"])->middleware('permission:device-status');
            $router->match(['get', 'post'], 'edit/{id?}', [UserController::class, "edit"])->name('edit')->middleware('permission:user-edit');
            $router->match(['get', 'post'], 'device/edit/{id?}', [UserController::class, "deviceEdit"])->name('deviceEdit')->middleware('permission:user-edit');
            $router->match(['get', 'post'], 'update-password/{id}', [UserController::class, "updatePassword"])->name('update-password')->middleware('permission:user-edit');
            $router->get('delete/{id}', [UserController::class, "del_record"])->name('delete')->middleware('permission:user-delete');
            $router->get('delete/device/{id}', [UserController::class, "del_device"])->name('deviceDelete')->middleware('permission:user-delete');
            $router->post('notify/device/', [UserController::class, "sendPushNotification"])->name('device.push-notification')->middleware('permission:user-edit');
            $router->post('notify/users/', [UserController::class, "sendPushNotificationToUsers"])->name('users.push-notification');


            $router->get('restore/{id}', [UserController::class, "del_restore"])->name('restore')->middleware('permission:user-delete');
            $router->get('device/restore/{id}', [UserController::class, "device_restore"])->name('redev')->middleware('permission:device-restore');
            $router->get('details/{id}', [UserController::class, "view_detail"])->name('details')->middleware('permission:user-view');
            $router->get('track/device/{token?}', [UserController::class, "track_device"])->name('tracking');
            $router->get('export', [UserController::class, "export"])->name('export');
        });

        /** Manage Companies Routes **/
        $router->prefix('company')->name('company.')->group(function () use ($router) {
            $router->get('list/{deleted?}', [CompanyController::class, "getList"])->name('list')->middleware('permission:company-list');
            $router->match(['get', 'post'], 'add', [CompanyController::class, "add"])->name('add')->middleware('permission:company-add');
            $router->get('changeStatus', [CompanyController::class, "changeStatus"])->middleware('permission:company-status');
            $router->match(['get', 'post'], 'edit/{id?}', [CompanyController::class, "edit"])->name('edit');
            $router->match(['get', 'post'], 'update-password/{id}', [CompanyController::class, "updatePassword"])->name('update-password')->middleware('permission:company-edit');
            $router->get('delete/{id}', [CompanyController::class, "del_record"])->name('delete')->middleware('permission:company-delete');
            $router->get('restore/{id}', [CompanyController::class, "del_restore"])->name('restore')->middleware('permission:company-delete');
            $router->get('details/{id}', [CompanyController::class, "view_detail"])->name('details')->middleware('permission:company-view');
            $router->get('c/export', [CompanyController::class, "export"])->name('export')->middleware('permission:company-list');
        });

        /*
        // Manage media Routes
        $router->prefix('media')->name('media.')->group(function() use($router) {
        $router->get('list/{deleted?}', [MediaController::class,"getList"])->name('list')->middleware('permission:media-list');
        $router->match(['get','post'], 'add', [MediaController::class,"add"])->name('add')->middleware('permission:media-add');
        $router->get('changeStatus', [MediaController::class,"changeStatus"])->middleware('permission:media-status');
        $router->match(['get','post'], 'edit/{id?}', [MediaController::class,"edit"])->name('edit')->middleware('permission:media-edit');
        $router->get('delete/{id}', [MediaController::class,"del_record"])->name('delete')->middleware('permission:media-delete');
        $router->get('restore/{id}', [MediaController::class,"del_restore"])->name('restore')->middleware('permission:media-delete');
        });

        $router->get('subscription/payment-list',[PaymentController::class,"getList"])->middleware(['permission:payment-list'])->name('payment.list');

        Route::view('/paginate-media-files', 'admin.layouts.media-files');*/
        $router->get('dashboard', [HomeController::class, 'index'])->name('home');
        $router->view('invoices', 'admin.invoices')->name('invoices');
        $router->view('users', 'admin.user.list')->name('users');
        $router->view('question-answers', 'admin.question-answers')->name('question-answers');

    });

    $router->get('get-country-states/{id}', [HomeController::class, 'getCountryStates'])->name('get-country-states');
    $router->get('get-state-cities/{id}', [HomeController::class, 'getStateCities'])->name('get-state-cities');

    // Company and Admin Both can Use
    $router->middleware(['auth'])->group(function () use ($router) {
        $router->prefix('user')->name('user.')->group(function () use ($router) {
            $router->get('devices/{user_id?}', [UserController::class, "getDevices"])->name('devices')->middleware('permission:user-list');
            $router->match(['get', 'post'], 'add/device', [UserController::class, "addDevice"])->name('adddevice')->middleware('permission:user-list');
            $router->match(['get', 'post'], 'add', [UserController::class, "add"])->name('add')->middleware('permission:user-add');
            $router->get('changeStatus', [UserController::class, "changeStatus"])->middleware('permission:user-status');
            $router->get('/changeDeviceStatus', [UserController::class, "changeDeviceStatus"])->middleware('permission:user-status');
            $router->match(['get', 'post'], 'device/edit/{id?}', [UserController::class, "deviceEdit"])->name('deviceEdit')->middleware('permission:user-edit');
            $router->match(['get', 'post'], 'update-password/{id}', [UserController::class, "updatePassword"])->name('update-password')->middleware('permission:user-edit');
            $router->get('delete/{id}', [UserController::class, "del_record"])->name('delete')->middleware('permission:user-delete');
            $router->get('delete/device/{id}', [UserController::class, "del_device"])->name('deviceDelete')->middleware('permission:user-delete');
           /*  $router->post('notify/device/', [UserController::class, "sendPushNotification"])->name('device.push-notification')->middleware('permission:user-edit'); */
            $router->get('restore/{id}', [UserController::class, "del_restore"])->name('restore')->middleware('permission:user-delete');
            $router->get('device/restore/{id}', [UserController::class, "device_restore"])->name('redev')->middleware('permission:device-restore');
            $router->get('details/{id}', [UserController::class, "view_detail"])->name('details')->middleware('permission:user-view');
            $router->get('export', [UserController::class, "export"])->name('export');

        });

        /* ***************************** vehicles route *************************************** */
        $router->prefix('vehicle')->name('vehicle.')->group(function () use ($router) {
            $router->get('list/{deleted?}', [VehicleController::class, "getList"])->name('list')->middleware('permission:vehicle-list');
            $router->match(['get', 'post'], 'add', [VehicleController::class, "add"])->name('add')->middleware('permission:vehicle-add');
            $router->get('changeStatus', [VehicleController::class, "changeStatus"])->middleware('permission:vehicle-status');
            $router->match(['get', 'post'], 'edit/{id?}', [VehicleController::class, "edit"])->name('edit')->middleware('permission:vehicle-edit');
            $router->match(['get', 'post'], 'update-password/{id}', [VehicleController::class, "updatePassword"])->name('update-password')->middleware('permission:vehicle-edit');
            $router->get('delete/{id}', [VehicleController::class, "del_record"])->name('delete')->middleware('permission:vehicle-delete');
            $router->get('restore/{id}', [VehicleController::class, "del_restore"])->name('restore')->middleware('permission:vehicle-delete');
            $router->get('details/{id}', [VehicleController::class, "view_detail"])->name('details')->middleware('permission:vehicle-view');
            $router->get('v/export', [VehicleController::class, "export"])->name('export')->middleware('permission:vehicle-list');
            $router->post('get/assign/vehicle', [UserController::class, "getAssignVehicle"])->name('vehicle-user-list')->middleware('permission:vehicle-assign');
            $router->post('assign/vehicle', [UserController::class, "assignUserVehicle"])->name('assign')->middleware('permission:vehicle-assign');

        });

        // Company Routes
        $router->middleware(['auth'])->prefix('c')->group(function () use ($router) {
            $router->get('dashboard', [HomeController::class, 'company_index'])->name('company_home');

            /** Manage Users Routes **/
            $router->match(['get', 'post'], 'edit/{id?}', [UserController::class, "edit"])->name('c.user.edit')->middleware('permission:user-edit');
            $router->name('c.')->group(function () use ($router) {
                $router->get('users/list/{deleted?}', [CompanyController::class, "getUserList"])->name('list')->middleware('permission:user-list');

            });

        });
        $router->match(['post'], 'profile/edit/{id?}', [CompanyController::class, "edit"])->name('c.edit');
    });

});
