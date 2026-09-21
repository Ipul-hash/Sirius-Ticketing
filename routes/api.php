<?php

use App\Http\Controllers\Api\V1\Master\CompanyAssetController;
use App\Http\Controllers\Api\V1\Master\DepartmentController;
use App\Http\Controllers\Api\V1\Master\SlaPolicyController;
use App\Http\Controllers\Api\V1\Superadmin\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    // ##############################
    // ## Master Data Departments ###
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
    // ## Master Data Companies #####
    // ##############################
    Route::get('/superadmin/companies', [CompanyController::class, 'index']);
    Route::post('/superadmin/companies', [CompanyController::class, 'store']);
    Route::get('/superadmin/companies/{id}', [CompanyController::class, 'show']);
    Route::put('/superadmin/companies/{id}', [CompanyController::class, 'update']);
    Route::patch('/superadmin/companies/{id}/status', [CompanyController::class, 'updateStatus']);
    Route::delete('/superadmin/companies/{id}', [CompanyController::class, 'destroy']);

    // ##############################
    // ## Master Data Company Assets#
    // ##############################
    Route::get('/assets', [CompanyAssetController::class, 'index']);
    Route::post('/assets', [CompanyAssetController::class, 'store']);
    Route::get('/assets/{id}', [CompanyAssetController::class, 'show']);
    Route::put('/assets/{id}', [CompanyAssetController::class, 'update']);
    Route::delete('/assets/{id}', [CompanyAssetController::class, 'destroy']);

    Route::get('/{tenant}/assets', [CompanyAssetController::class, 'index']);
    Route::post('/{tenant}/assets', [CompanyAssetController::class, 'store']);
    Route::get('/{tenant}/assets/{id}', [CompanyAssetController::class, 'show']);
    Route::put('/{tenant}/assets/{id}', [CompanyAssetController::class, 'update']);
    Route::delete('/{tenant}/assets/{id}', [CompanyAssetController::class, 'destroy']);

    // ##############################
    // ## Master Data SLA Policies ##
    // ##############################
    Route::get('/sla-policies', [SlaPolicyController::class, 'index']);
    Route::put('/sla-policies/{priority}', [SlaPolicyController::class, 'update']);

    Route::get('/{tenant}/sla-policies', [SlaPolicyController::class, 'index']);
    Route::put('/{tenant}/sla-policies/{priority}', [SlaPolicyController::class, 'update']);
});
