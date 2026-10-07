<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('copil.index'));

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [LoginController::class, 'create'])->name('login');
    Route::post('/connexion', [LoginController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::get('/copil/{period:key}/presentation', PresentationController::class)->name('presentation');
Route::get('/copil/fichiers/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/copil', DashboardController::class)->name('copil.index');
    Route::put('/copil/rapports/{report}/sections/{section}', [ReportController::class, 'updateSection'])->name('reports.sections.update');

    Route::post('/copil/rapports/{report}/fichiers', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('/copil/fichiers/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    Route::get('/copil/{period:key}/export.pdf', [ExportController::class, 'pdf'])->name('exports.pdf');
    Route::get('/copil/{period:key}/export.pptx', [ExportController::class, 'pptx'])->name('exports.pptx');

    Route::middleware('role:admin')->group(function () {
        Route::post('/copil/periodes', [PeriodController::class, 'store'])->name('periods.store');
        Route::post('/copil/periodes/{period}/cloturer', [PeriodController::class, 'close'])->name('periods.close');
        Route::post('/copil/periodes/{period}/rouvrir', [PeriodController::class, 'reopen'])->name('periods.reopen');

        Route::post('/copil/utilisateurs', [UserController::class, 'store'])->name('users.store');
        Route::put('/copil/utilisateurs/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/copil/utilisateurs/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
