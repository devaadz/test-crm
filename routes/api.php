<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;

Route::get('/test', function () {
    return response()->json(['message' => 'API endpoint berhasil diakses!']);
});

// Tambahkan nama pada route login
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Route lainnya
Route::middleware(['auth:api'])->group(function () {
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::apiResource('employees', EmployeeController::class)->except(['show','destroy','delete']);
    Route::get('/employees/{employee}', [EmployeeController::class, 'show']);
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
