<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaveApplicationPrintController;
use App\Http\Controllers\LocatorSlipPrintController;
use App\Http\Controllers\SalnPrintController;
use App\Http\Controllers\TravelOrderPrintController;

Route::get('/travel-order/{travelOrder}/print', [TravelOrderPrintController::class, 'print'])
    ->name('travel-order.print');

Route::get('/locator-slip/print/{id}', [LocatorSlipPrintController::class, 'print'])
    ->name('locator_slip.print');

Route::get('/leave-application/print/{id}', [LeaveApplicationPrintController::class, 'print'])
    ->name('leave_application.print');

Route::get('/saln/{saln}/print', [SalnPrintController::class, 'print'])
    ->name('saln.print');


Route::get('/', function () {
    return view('welcome');
});
