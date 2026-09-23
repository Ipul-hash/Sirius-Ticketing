<?php

use App\Http\Controllers\Api\V1\Master\CannedResponseController;
use App\Http\Controllers\Api\V1\Master\CompanyAssetController;
use App\Http\Controllers\Api\V1\Master\DepartmentController;
use App\Http\Controllers\Api\V1\Master\SlaPolicyController;
use App\Http\Controllers\Api\V1\Master\TicketCategoryController;
use App\Http\Controllers\Api\V1\Master\UserController;
use App\Http\Controllers\Api\V1\Superadmin\CompanyController;
use App\Http\Controllers\Api\V1\Ticket\TicketActivityController;
use App\Http\Controllers\Api\V1\Ticket\TicketApprovalController;
use App\Http\Controllers\Api\V1\Ticket\TicketCollisionController;
use App\Http\Controllers\Api\V1\Ticket\TicketController;
use App\Http\Controllers\Api\V1\Ticket\TicketMergeController;
use App\Http\Controllers\Api\V1\Ticket\TicketMessageController;
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
    // ## Master Data Users / Staf ##
    // ##############################
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/{tenant}/users', [UserController::class, 'index']);
    Route::post('/{tenant}/users', [UserController::class, 'store']);
    Route::get('/{tenant}/users/{id}', [UserController::class, 'show']);
    Route::put('/{tenant}/users/{id}', [UserController::class, 'update']);
    Route::delete('/{tenant}/users/{id}', [UserController::class, 'destroy']);

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

    // ##############################
    // ## Master Data Ticket Categories ###
    // ##############################
    Route::get('/categories', [TicketCategoryController::class, 'index']);
    Route::post('/categories', [TicketCategoryController::class, 'store']);
    Route::get('/categories/{id}', [TicketCategoryController::class, 'show']);
    Route::put('/categories/{id}', [TicketCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [TicketCategoryController::class, 'destroy']);

    Route::get('/{tenant}/categories', [TicketCategoryController::class, 'index']);
    Route::post('/{tenant}/categories', [TicketCategoryController::class, 'store']);
    Route::get('/{tenant}/categories/{id}', [TicketCategoryController::class, 'show']);
    Route::put('/{tenant}/categories/{id}', [TicketCategoryController::class, 'update']);
    Route::delete('/{tenant}/categories/{id}', [TicketCategoryController::class, 'destroy']);

    // ##############################
    // ## Master Data Canned Responses ###
    // ##############################
    Route::get('/canned-responses', [CannedResponseController::class, 'index']);
    Route::post('/canned-responses', [CannedResponseController::class, 'store']);
    Route::get('/canned-responses/{id}', [CannedResponseController::class, 'show']);
    Route::put('/canned-responses/{id}', [CannedResponseController::class, 'update']);
    Route::delete('/canned-responses/{id}', [CannedResponseController::class, 'destroy']);

    Route::get('/{tenant}/canned-responses', [CannedResponseController::class, 'index']);
    Route::post('/{tenant}/canned-responses', [CannedResponseController::class, 'store']);
    Route::get('/{tenant}/canned-responses/{id}', [CannedResponseController::class, 'show']);
    Route::put('/{tenant}/canned-responses/{id}', [CannedResponseController::class, 'update']);
    Route::delete('/{tenant}/canned-responses/{id}', [CannedResponseController::class, 'destroy']);

    // ##############################
    // ## Transaksional & Operasional Tiket
    // ##############################
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::put('/tickets/{id}', [TicketController::class, 'update']);
    Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus']);
    Route::patch('/tickets/{id}/assign', [TicketController::class, 'assign']);
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);

    // Percakapan & Lampiran Tiket
    Route::get('/tickets/{ticketId}/messages', [TicketMessageController::class, 'index']);
    Route::post('/tickets/{ticketId}/messages', [TicketMessageController::class, 'store']);
    Route::get('/attachments/{id}/download', [TicketMessageController::class, 'downloadAttachment']);

    // Persetujuan Tiket ITIL (Ticket Approvals)
    Route::get('/approvals/pending', [TicketApprovalController::class, 'indexPending']);
    Route::post('/tickets/{ticketId}/approve', [TicketApprovalController::class, 'approve']);
    Route::post('/tickets/{ticketId}/reject', [TicketApprovalController::class, 'reject']);

    // Jejak Audit & Timeline Aktivitas Tiket (Ticket Activities)
    Route::get('/tickets/{ticketId}/activities', [TicketActivityController::class, 'index']);

    // Deteksi Kehadiran & Anti-Tabrakan Teknisi (Ticket Collisions)
    Route::post('/tickets/{ticketId}/collisions/ping', [TicketCollisionController::class, 'ping']);
    Route::get('/tickets/{ticketId}/collisions', [TicketCollisionController::class, 'active']);
    Route::post('/tickets/{ticketId}/collisions/leave', [TicketCollisionController::class, 'leave']);

    // Penggabungan Tiket Duplikat (Ticket Merging)
    Route::get('/tickets/{ticketId}/merge-candidates', [TicketMergeController::class, 'candidates']);
    Route::post('/tickets/{ticketId}/merge', [TicketMergeController::class, 'merge']);

    Route::get('/{tenant}/tickets', [TicketController::class, 'index']);
    Route::post('/{tenant}/tickets', [TicketController::class, 'store']);
    Route::get('/{tenant}/tickets/{id}', [TicketController::class, 'show']);
    Route::put('/{tenant}/tickets/{id}', [TicketController::class, 'update']);
    Route::patch('/{tenant}/tickets/{id}/status', [TicketController::class, 'updateStatus']);
    Route::patch('/{tenant}/tickets/{id}/assign', [TicketController::class, 'assign']);
    Route::delete('/{tenant}/tickets/{id}', [TicketController::class, 'destroy']);

    Route::get('/{tenant}/tickets/{ticketId}/messages', [TicketMessageController::class, 'index']);
    Route::post('/{tenant}/tickets/{ticketId}/messages', [TicketMessageController::class, 'store']);

    Route::get('/{tenant}/approvals/pending', [TicketApprovalController::class, 'indexPending']);
    Route::post('/{tenant}/tickets/{ticketId}/approve', [TicketApprovalController::class, 'approve']);
    Route::post('/{tenant}/tickets/{ticketId}/reject', [TicketApprovalController::class, 'reject']);

    Route::get('/{tenant}/tickets/{ticketId}/activities', [TicketActivityController::class, 'index']);

    Route::post('/{tenant}/tickets/{ticketId}/collisions/ping', [TicketCollisionController::class, 'ping']);
    Route::get('/{tenant}/tickets/{ticketId}/collisions', [TicketCollisionController::class, 'active']);
    Route::post('/{tenant}/tickets/{ticketId}/collisions/leave', [TicketCollisionController::class, 'leave']);

    Route::get('/{tenant}/tickets/{ticketId}/merge-candidates', [TicketMergeController::class, 'candidates']);
    Route::post('/{tenant}/tickets/{ticketId}/merge', [TicketMergeController::class, 'merge']);
});
