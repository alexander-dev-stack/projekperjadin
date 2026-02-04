<?php

use Illuminate\Support\Facades\Route;

use App\Models\Perjadin;

use App\Http\Controllers\PerjadinController;

Route::get('/', [PerjadinController::class, 'index']);

// Auth Routes (Temporary Simple Implementation)
Route::post('/logout', function() {
    auth()->logout();
    return redirect('/');
})->name('logout');

Route::get('/rekap', [PerjadinController::class, 'index']);
Route::get('/tambah', [PerjadinController::class, 'create']);
Route::post('/simpan', [PerjadinController::class, 'store']);

Route::get('/edit/{id}', [PerjadinController::class, 'edit']);
Route::post('/update/{id}', [PerjadinController::class, 'update']);
Route::get('/hapus/{id}', [PerjadinController::class, 'destroy']);
Route::get('/export', [PerjadinController::class, 'export']);
Route::post('/import', [PerjadinController::class, 'import']);
Route::get('/hapus-perjalanan/{id}', [PerjadinController::class, 'destroyPerjalanan']);
