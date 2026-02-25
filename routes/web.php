<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, PegawaiController, UserController, ProfileController};

/*
| PENTING: Daftarkan middleware 'role' di:
|
| Laravel 11 — bootstrap/app.php:
|   ->withMiddleware(function (Middleware $middleware) {
|       $middleware->alias(['role' => \App\Http\Middleware\CheckRole::class]);
|   })
|
| Laravel 10 — app/Http/Kernel.php $middlewareAliases:
|   'role' => \App\Http\Middleware\CheckRole::class,
*/

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected
Route::middleware('auth')->group(function () {
    Route::get('/', [PegawaiController::class, 'index'])->name('pegawai.index');
    Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
    Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
    Route::get('/pegawai/import', [PegawaiController::class, 'importForm'])->name('pegawai.import.form');
    Route::post('/pegawai/import', [PegawaiController::class, 'import'])->name('pegawai.import');
    Route::get('/pegawai/template', [PegawaiController::class, 'downloadTemplate'])->name('pegawai.template');
    Route::get('/pegawai/export', [PegawaiController::class, 'export'])->name('pegawai.export');
    Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

    // Hapus semua — hanya admin & super_admin
    Route::delete('/pegawai-destroy-all', [PegawaiController::class, 'destroyAll'])
        ->name('pegawai.destroyAll')->middleware('role:super_admin,admin');

    // Hapus semua per unit — hanya operator
    Route::delete('/pegawai-destroy-unit', [PegawaiController::class, 'destroyAllUnit'])
        ->name('pegawai.destroyAllUnit')->middleware('role:operator');

    // Profil — semua user
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // User management — hanya super_admin
    Route::middleware('role:super_admin')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
