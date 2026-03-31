<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, PegawaiController, UserController, ProfileController};

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected
Route::middleware('auth')->group(function () {
    // Dashboard / Pegawai List
    Route::get('/', [PegawaiController::class, 'index'])->name('pegawai.index');
    Route::get('/pegawai/daftar', [PegawaiController::class, 'daftar'])->name('pegawai.daftar');
    
    // Pegawai CRUD
    Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
    Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
    
    // Import/Export
    Route::get('/pegawai/import', [PegawaiController::class, 'importForm'])->name('pegawai.import.form');
    Route::post('/pegawai/import', [PegawaiController::class, 'import'])->name('pegawai.import');
    Route::get('/pegawai/template', [PegawaiController::class, 'downloadTemplate'])->name('pegawai.template');
    Route::get('/pegawai/export', [PegawaiController::class, 'export'])->name('pegawai.export');
    
    // Bulk operations - must come before {id} route
    Route::delete('/pegawai/all', [PegawaiController::class, 'destroyAll'])
        ->name('pegawai.destroyAll')->middleware('role:super_admin, admin');
    Route::delete('/pegawai/unit', [PegawaiController::class, 'destroyAllUnit'])
        ->name('pegawai.destroyAllUnit')->middleware('role:operator');
    
    // Single delete - must come last
    Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // User management
    Route::middleware('role:super_admin')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});