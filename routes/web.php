<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\ReportController;
use App\Http\Controllers\TripplanReportController;
use App\Http\Controllers\IdleReportController;
use App\Http\Controllers\ParkingReportController;
use App\Http\Controllers\RoutedeviationReportController;
use App\Http\Controllers\KeyoffKeyonReportController;
use App\Http\Controllers\PlayBackHistoryReportController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\RoutesController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\CronJob;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PasswordChangeController;


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

Route::get('get_apemcl_data', [PlayBackHistoryReportController::class, 'get_apemcl_data'])->name('playbackhistoryreport.get_apemcl_data');
Route::get('demo_test', [PlayBackHistoryReportController::class, 'demo_test'])->name('playbackhistoryreport.demo_test');
Route::get('trip_plan_cron', [TripplanReportController::class, 'trip_plan'])->name('tripplanreport.trip_plan');
Route::get('route_devation_cron',[CronJob::class,'route_devation_cron'])->name('route_deviation.cron');
Route::get('trip_polyline_create', [CronJob::class, 'trip_polyline_create']);
Route::POST('create_polyline', [TripplanReportController::class,'create_polyline']);
Route::get('forgot_password', [PasswordChangeController::class, 'index'])->name('forgot_password');
Route::POST('change_password', [PasswordChangeController::class, 'change_password'])->name('change_password');
Route::get('reload_captcha', [LoginController::class, 'reloadCaptcha'])->name('reload_captcha');


Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('all_vehicles', [DashboardController::class, 'all_vehicles'])->name('dashboard.all_vehicles');
    Route::resource('/setting', SettingController::class);
    // Route::get('get_apemcl_data', DashboardController::class,'get_apemcl_data')->name('dashboard');
    // Route::get('/tripplanreport', TripplanReportController::class);
    // Route::get('idlereport',[IdleReportController::class, 'index'])->name('idlereport.get_data');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('tripplanreport', [TripplanReportController::class, 'index'])->name('tripplanreport.index');
    Route::get('tripplanreport_complete', [TripplanReportController::class, 'tripplan_complete_report'])->name('tripplanreport.tripplan_complete_report');
    Route::post('tripplanreport_getdata', [TripplanReportController::class, 'complete_report_getData'])->name('tripplanreport.complete_report_getData');
    Route::post('trip_plan_table', [TripplanReportController::class, 'getData'])->name('trip_plan.getData');
    Route::get('planned_trips', [TripplanReportController::class, 'planned_trips'])->name('trip_plan.planned_trips');
    Route::get('idlereport', [IdleReportController::class, 'index'])->name('idlereport.index');
    Route::get('idlereport_table', [IdleReportController::class, 'getData'])->name('idlereport.getData');
// Route::get('parkingreport',[ParkingReportController::class,'index'])->name('parkingreport.index');
    Route::get('parkingreport', [ParkingReportController::class, 'index'])->name('parkingreport.index');
    Route::post('parkingreport_table', [ParkingReportController::class, 'getData'])->name('parkingreport.getData');
    Route::get('routedeviationreport', [RoutedeviationReportController::class, 'index'])->name('routedeviationreport.index');
    Route::post('routedeviationreport_table', [RoutedeviationReportController::class, 'getData'])->name('routedeviationreport.getData');
    Route::get('route_deviation_playdata', [RoutedeviationReportController::class, 'playdata'])->name('routedeivation.playdata');
    Route::get('keyonkeyoffreport', [KeyoffKeyonReportController::class, 'index'])->name('keyonkeyoffreport.index');
    Route::get('playbackhistoryreport', [PlayBackHistoryReportController::class, 'index'])->name('playbackhistoryreport.index');
    Route::post('playback_get_history', [PlayBackHistoryReportController::class, 'get_history'])->name('playback.get_history');
    Route::get('vehicle', [VehicleController::class, 'index'])->name('vehicle.index');
    Route::post('vehicle_table', [VehicleController::class, 'getData'])->name('vehicle.getData');
    Route::get('create', [VehicleController::class, 'create'])->name('vehicle.create');
    Route::post('store', [VehicleController::class, 'store'])->name('vehicle.store');
    Route::get('edit', [VehicleController::class, 'edit'])->name('vehicle.edit');
    Route::post('update', [VehicleController::class, 'update'])->name('vehicle.update');
    Route::get('parking_get_address', [ParkingReportController::class, 'get_address_modal'])->name('parkingreport.get_address');
    Route::resource('trip_routes', RoutesController::class);
    // Route::get('trip_routes', [RoutesController::class, 'index'])->name('route.index');
    Route::post('routes_table', [RoutesController::class, 'getData'])->name('route.getData');
    Route::get('routes_create', [RoutesController::class, 'route_create'])->name('route.route_create');
    Route::post('routes_store', [RoutesController::class, 'route_store'])->name('route.route_store');

    Route::get('tripplans_create', [TripplanReportController::class, 'create'])->name('trip.create');
    Route::post('tripplans_store', [TripplanReportController::class, 'store'])->name('trip_plan.store');
    Route::post('tripplan_report_parking', [TripplanReportController::class, 'get_playback_data'])->name('tripplanreport.get_parking');
    Route::get('generator_route_list', [RoutesController::class, 'generator_route_list'])->name('generator_route_list');
    Route::get('get_selected_polyline', [RoutesController::class, 'get_selected_polyline'])->name('get_selected_polyline');
    

});

require __DIR__ . '/auth.php';

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
