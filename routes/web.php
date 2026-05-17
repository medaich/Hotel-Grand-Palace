<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Routes
    Route::resource('rooms', \App\Http\Controllers\RoomController::class)->except(['create', 'show']);
    Route::patch('/rooms/{room}/status', [\App\Http\Controllers\RoomController::class, 'updateStatus'])->name('rooms.status');
    
    Route::resource('bookings', \App\Http\Controllers\BookingController::class)->except(['create', 'show']);
    Route::resource('guests', \App\Http\Controllers\GuestController::class)->except(['create', 'show']);
    Route::resource('services', \App\Http\Controllers\ServiceController::class)->except(['create', 'show']);
    Route::resource('maintenance', \App\Http\Controllers\MaintenanceController::class)->except(['create', 'show']);
    Route::resource('messages', \App\Http\Controllers\MessageController::class)->except(['create', 'show', 'edit', 'update']);
    
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    
    Route::get('/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity_logs.index');
    
    Route::post('admin/ping', [\App\Http\Controllers\AdminController::class, 'runPing'])->name('admin.ping');
    Route::post('admin/backup', [\App\Http\Controllers\AdminController::class, 'runBackup'])->name('admin.backup');
    Route::post('admin/settings', [\App\Http\Controllers\AdminController::class, 'saveSettings'])->name('admin.settings');
    Route::resource('admin', \App\Http\Controllers\AdminController::class)->except(['create', 'show']);
    
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});
