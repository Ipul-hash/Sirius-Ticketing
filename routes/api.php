<?php

use App\Http\Controllers\Api\V1\Master\DepartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Superadmin\CompanyController; 

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    // ##############################
    // ## master data departments ###
    // ##############################
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::get('/departments/{id}', [DepartmentController::class, 'show']);
    Route::put('/departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);

    Route::get('/{tenant}/departments', [DepartmentController::class, 'index']);
    Route::post('/{tenant}/departments', [DepartmentController::class, 'store']);
    Route::get('/{tenant}/departments/{id}', [DepartmentController::class, 'show']);
    Route::put('/{tenant}/departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('/{tenant}/departments/{id}', [DepartmentController::class, 'destroy']);

    // ##############################
    // ## master data company ###
    // ##############################
    Route::get('/superadmin/companies', [CompanyController::class, 'index']);
    Route::post('/superadmin/companies', [CompanyController::class, 'store']);
    Route::get('/superadmin/companies/{id}', [CompanyController::class, 'show']);
    Route::put('/superadmin/companies/{id}', [CompanyController::class, 'update']);
    Route::delete('/superadmin/companies/{id}', [CompanyController::class, 'destroy']);
});
