<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(PageController::class)->group(function () {
    Route::get('/departments', 'departments')->name('departments.index');
    Route::get('/companies', 'companies')->name('companies.index');
    Route::get('/assets', 'assets')->name('assets.index');
    Route::get('/sla-policies', 'slaPolicies')->name('sla-policies.index');
});
