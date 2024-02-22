<?php

use App\Http\Controllers\API\CronJob;
use App\Http\Controllers\API\IdleReportController;
use App\Http\Controllers\API\VehicleApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::middleware('auth')->group(function () {
// });
Route::get('idle_report', [IdleReportController::class, 'index']);
Route::POST('add_tripplan', [CronJob::class, 'add_trip_plan']);
Route::POST('add_trip_plan_B', [CronJob::class, 'add_trip_plan_B']);
Route::get('route_devation_cron', [CronJob::class, 'route_devation_cron']);
Route::POST('vehicle_status', [VehicleApiController::class, 'vehicle_status']);
Route::POST('create_polyline', [CronJob::class,'create_polyline']);
Route::POST('add_trip_curl', [CronJob::class, 'add_trip_curl']);
Route::get('incompleted_trips', [CronJob::class, 'incompleted_trips']);
Route::POST('manual_complete_trips', [CronJob::class,'manual_complete_trips']);




