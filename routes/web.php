<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{DashboardController,UserController,MediaController,CmsManagementController,SubscriptionController,PaymentController,CompanyController};
use App\Http\Controllers\{HomeController,TestingController};
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ExcelCSVController;
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
Route::group([ ], function () use ($router) {
    $router->get('liap-ios',[TestingController::class,'testing']);
    $router->get('notification-testing',[TestingController::class,'notificationTesting']);

    $router->get('verify-user/{token}',[DashboardController::class,"verifyUser"])->name('verify-customer');
    $router->view('verify-message','verify')->name('verify-message');
    $router->redirect('/login','/');

    $router->match(['get','post'], "", [DashboardController::class, "login"])->name('login');
    $router->match(['get','post'],'reset-password', [DashboardController::class, "resetPassword"])->name('reset-password');
    $router->get('reset-password/check/token/{token}', [DashboardController::class,"verifyResetPasswordToken"])->name('token-check');
    $router->match(['get','post'],'set-new-password', [DashboardController::class,"setNewPassword"])->name('set-newpassword');
    $router->get('table-data/{name?}',[DashboardController::class,"getData"]);
    $router->get('table-data/{name}/delete/{id}',[DashboardController::class,"deleteData"]);

    $router->middleware(['auth','admin'])->group(function() use ($router) {
        /** Profile update **/
        $router->post('password/update', [DashboardController::class,"updatePassword"])->name('password.update');
        $router->post('details/update', [DashboardController::class,"updateDetails"])->name('details.update');
        $router->get('logout-ad', [DashboardController::class,"logout"])->name('logout-ad');

         /** Manage Users Routes **/
         $router->prefix('user')->name('user.')->group(function() use($router) {
            $router->get('list/{deleted?}', [UserController::class,"getList"])->name('list')->middleware('permission:user-list');
            $router->get('devices/{user_id?}', [UserController::class,"getDevices"])->name('devices')->middleware('permission:user-list');
            $router->match(['get','post'], 'add/device', [UserController::class,"addDevice"])->name('adddevice')->middleware('permission:user-list');
            $router->match(['get','post'], 'add', [UserController::class,"add"])->name('add')->middleware('permission:user-add');
            $router->get('changeStatus', [UserController::class,"changeStatus"])->middleware('permission:user-status');
            $router->get('/changeDeviceStatus', [UserController::class,"changeDeviceStatus"])->middleware('permission:user-status');
            $router->match(['get','post'], 'edit/{id?}', [UserController::class,"edit"])->name('edit')->middleware('permission:user-edit');
            $router->match(['get','post'], 'device/edit/{id?}', [UserController::class,"deviceEdit"])->name('deviceEdit')->middleware('permission:user-edit');

            $router->match(['get','post'], 'update-password/{id}', [UserController::class,"updatePassword"])->name('update-password')->middleware('permission:user-edit');
            $router->get('delete/{id}', [UserController::class,"del_record"])->name('delete')->middleware('permission:user-delete');
            $router->get('delete/device/{id}', [UserController::class,"del_device"])->name('deviceDelete')->middleware('permission:user-delete');
            $router->get('restore/{id}', [UserController::class,"del_restore"])->name('restore')->middleware('permission:user-delete');
            $router->get('device/restore/{id}', [UserController::class,"device_restore"])->name('restore')->middleware('permission:user-delete');
            $router->get('details/{id}', [UserController::class,"view_detail"])->name('details')->middleware('permission:user-view');
            $router->get('export', [UserController::class,"export"])->name('export')->middleware('permission:user-list');
        });


        /** Manage Companies Routes **/
        $router->prefix('company')->name('company.')->group(function() use($router) {
            $router->get('list/{deleted?}', [CompanyController::class,"getList"])->name('list')->middleware('permission:user-list');
            $router->match(['get','post'], 'add', [CompanyController::class,"add"])->name('add')->middleware('permission:user-add');
            $router->get('changeStatus', [CompanyController::class,"changeStatus"])->middleware('permission:user-status');
            $router->match(['get','post'], 'edit/{id?}', [CompanyController::class,"edit"])->name('edit')->middleware('permission:user-edit');
            $router->match(['get','post'], 'update-password/{id}', [CompanyController::class,"updatePassword"])->name('update-password')->middleware('permission:user-edit');
            $router->get('delete/{id}', [CompanyController::class,"del_record"])->name('delete')->middleware('permission:user-delete');
            $router->get('restore/{id}', [CompanyController::class,"del_restore"])->name('restore')->middleware('permission:user-delete');
            $router->get('details/{id}', [CompanyController::class,"view_detail"])->name('details')->middleware('permission:user-view');
            $router->get('export', [CompanyController::class,"export"])->name('export')->middleware('permission:user-list');
        });

        /** Manage media Routes **/
        $router->prefix('media')->name('media.')->group(function() use($router) {
            $router->get('list/{deleted?}', [MediaController::class,"getList"])->name('list')->middleware('permission:media-list');
            $router->match(['get','post'], 'add', [MediaController::class,"add"])->name('add')->middleware('permission:media-add');
            $router->get('changeStatus', [MediaController::class,"changeStatus"])->middleware('permission:media-status');
            $router->match(['get','post'], 'edit/{id?}', [MediaController::class,"edit"])->name('edit')->middleware('permission:media-edit');
            $router->get('delete/{id}', [MediaController::class,"del_record"])->name('delete')->middleware('permission:media-delete');
            $router->get('restore/{id}', [MediaController::class,"del_restore"])->name('restore')->middleware('permission:media-delete');
        });

        $router->get('subscription/payment-list',[PaymentController::class,"getList"])->middleware(['permission:payment-list'])->name('payment.list');

        Route::view('/paginate-media-files', 'admin.layouts.media-files');
        $router->get('get-country-states/{id}',[HomeController::class,'getCountryStates'])->name('get-country-states');
        $router->get('get-state-cities/{id}',[HomeController::class,'getStateCities'])->name('get-state-cities');
        $router->get('dashboard',[HomeController::class,'index'])->name('home');
        $router->view('invoices','admin.invoices')->name('invoices');
        $router->view('users','admin.user.list')->name('users');
        $router->view('question-answers','admin.question-answers')->name('question-answers');

    });

   /*  Route::get('/test-email', function () {
        Mail::send('emails.verify', [], function ($message) {
            $message->to('sushil.kumar@infostride.com');
            $message->subject('Test Email');
        });
    }); */
});
