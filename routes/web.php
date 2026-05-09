<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

// ── Page Routes ─────────────────────────────────────────────────
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

// Private video page — hidden for now, redirect ke home
Route::get('/private-video', function () {
    return redirect()->route('home');
})->name('private');

// ── Auth Routes ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Video Processing API ────────────────────────────────────────
// Tidak di-wrap auth middleware — agar guest bisa download juga.
Route::prefix('video')->name('video.')->middleware('maintenance')->group(function () {
    Route::post('/process',         [VideoController::class, 'processUrl'])->name('process');
    Route::post('/extract-private', [VideoController::class, 'extractPrivate'])->name('extract-private');
    Route::get('/download',         [VideoController::class, 'proxyDownload'])->name('download');
});

// ── History (Auth Required) ─────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/history',                          [HistoryController::class, 'index'])->name('history');
    Route::get('/api/history',                      [HistoryController::class, 'apiIndex'])->name('api.history');
    Route::post('/api/history/log',                 [HistoryController::class, 'logDownload'])->name('api.history.log');
    Route::get('/api/history/{log}/redownload',     [HistoryController::class, 'redownload'])->name('api.history.redownload');
    Route::delete('/api/history/{log}',             [HistoryController::class, 'destroy'])->name('api.history.destroy');
});

use App\Http\Controllers\AdminController;

// ── Admin Dashboard ─────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');

    // User Management Actions
    Route::patch('/users/{user}/toggle-role',   [AdminController::class, 'toggleRole'])->name('users.toggleRole');
    Route::patch('/users/{user}/toggle-status',  [AdminController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::delete('/users/{user}',               [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Maintenance Mode Toggle
    Route::post('/toggle-maintenance', [AdminController::class, 'toggleMaintenance'])->name('toggleMaintenance');
});
