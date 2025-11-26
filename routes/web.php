<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaveApplicationPrintController;
use App\Http\Controllers\LocatorSlipPrintController;
use App\Http\Controllers\SalnPrintController;

Route::get('/locator-slip/print/{id}', [LocatorSlipPrintController::class, 'print'])
    ->name('locator_slip.print');

Route::get('/leave-application/print/{id}', [LeaveApplicationPrintController::class, 'print'])
     ->name('leave_application.print');

Route::get('/saln/{saln}/print', [App\Http\Controllers\SalnPrintController::class, 'print'])
    ->name('saln.print');


Route::get('/', function () {
    return view('welcome');
});
