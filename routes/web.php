<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SopController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttachmentController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Placeholder routes for other modules - will implement controllers later
Route::resource('projects', ProjectController::class)->middleware(['auth', 'verified']);
Route::resource('clients', ClientController::class)->middleware(['auth', 'verified']);
Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->middleware(['auth', 'verified'])->name('invoices.download');
Route::resource('invoices', InvoiceController::class)->middleware(['auth', 'verified']);
Route::resource('sops', SopController::class)->middleware(['auth', 'verified']);
Route::get('/finance', [FinanceController::class, 'index'])->middleware(['auth', 'verified'])->name('finance.index');
Route::get('/global-search', [GlobalSearchController::class, 'index'])->middleware(['auth', 'verified'])->name('global.search');
Route::post('/finance/expenses', [FinanceController::class, 'storeExpense'])->middleware(['auth', 'verified'])->name('finance.expenses.store');
Route::delete('/finance/expenses/{expense}', [FinanceController::class, 'destroyExpense'])->middleware(['auth', 'verified'])->name('finance.expenses.destroy');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Attachments
    Route::get('/attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
});

require __DIR__ . '/auth.php';
