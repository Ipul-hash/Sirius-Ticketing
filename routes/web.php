<?php

use App\Http\Controllers\Master\DepartmentWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Halaman Master Departemen (Metronic 8)
Route::get('/departments', [DepartmentWebController::class, 'index'])->name('departments.index');
