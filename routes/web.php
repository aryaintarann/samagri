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
use App\Http\Controllers\UserController;

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
Route::resource('users', UserController::class)->middleware(['auth', 'verified']);


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

// Kanban Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/projects/{project}/kanban', [\App\Http\Controllers\KanbanController::class, 'show'])->name('projects.kanban');

    Route::prefix('kanban')->group(function () {
        Route::post('/columns', [\App\Http\Controllers\KanbanController::class, 'storeColumn'])->name('kanban.columns.store');
        Route::put('/columns/{column}', [\App\Http\Controllers\KanbanController::class, 'updateColumn'])->name('kanban.columns.update');
        Route::delete('/columns/{column}', [\App\Http\Controllers\KanbanController::class, 'destroyColumn'])->name('kanban.columns.destroy');
        Route::post('/columns/reorder', [\App\Http\Controllers\KanbanController::class, 'reorderColumns'])->name('kanban.columns.reorder');

        Route::get('/cards/{card}', [\App\Http\Controllers\KanbanController::class, 'getCard'])->name('kanban.cards.show');
        Route::post('/cards', [\App\Http\Controllers\KanbanController::class, 'storeCard'])->name('kanban.cards.store');
        Route::post('/cards/{card}', [\App\Http\Controllers\KanbanController::class, 'updateCard'])->name('kanban.cards.update');
        Route::delete('/cards/{card}', [\App\Http\Controllers\KanbanController::class, 'destroyCard'])->name('kanban.cards.destroy');
        Route::post('/cards/move', [\App\Http\Controllers\KanbanController::class, 'moveCard'])->name('kanban.cards.move');

        Route::delete('/attachments/{attachment}', [\App\Http\Controllers\KanbanController::class, 'destroyAttachment'])->name('kanban.attachments.destroy');
    });
});

require __DIR__ . '/auth.php';
